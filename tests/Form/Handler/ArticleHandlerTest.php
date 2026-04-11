<?php

namespace App\Tests\Form\Handler;

use App\Entity\Article;
use App\Form\Handler\ArticleHandler;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class ArticleHandlerTest extends TestCase
{
    /** @var EntityManagerInterface&MockObject */
    private EntityManagerInterface $em;

    /** @var CsrfTokenManagerInterface&MockObject */
    private CsrfTokenManagerInterface $csrfTokenManager;

    private ArticleHandler $handler;

    protected function setUp(): void
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->csrfTokenManager = $this->createMock(CsrfTokenManagerInterface::class);
        $this->handler = new ArticleHandler($this->em, $this->csrfTokenManager);
    }

    public function testNewReturnsFalseWhenFormNotSubmitted(): void
    {
        $form = $this->createMock(FormInterface::class);
        $form->expects($this->once())->method('handleRequest');
        $form->method('isSubmitted')->willReturn(false);

        $this->em->expects($this->never())->method('persist');
        $this->em->expects($this->never())->method('flush');

        $result = $this->handler->new($form, new Request());

        $this->assertFalse($result);
    }

    public function testNewReturnsFalseWhenFormInvalid(): void
    {
        $form = $this->createMock(FormInterface::class);
        $form->expects($this->once())->method('handleRequest');
        $form->method('isSubmitted')->willReturn(true);
        $form->method('isValid')->willReturn(false);

        $this->em->expects($this->never())->method('persist');

        $result = $this->handler->new($form, new Request());

        $this->assertFalse($result);
    }

    public function testNewReturnsTrueAndPersistsWhenFormValid(): void
    {
        $article = new Article();
        $article->setTitle('Mon article test');
        $article->setContent('Contenu');
        $article->setIsPublished(true);

        $form = $this->createMock(FormInterface::class);
        $form->expects($this->once())->method('handleRequest');
        $form->method('isSubmitted')->willReturn(true);
        $form->method('isValid')->willReturn(true);
        $form->method('getData')->willReturn($article);

        $this->em->expects($this->once())->method('persist')->with($article);
        $this->em->expects($this->once())->method('flush');

        $result = $this->handler->new($form, new Request());

        $this->assertTrue($result);
    }

    public function testNewSetsPublishedAtWhenPublishedAndNotSet(): void
    {
        $article = new Article();
        $article->setTitle('Article');
        $article->setContent('Contenu');
        $article->setIsPublished(true);
        // publishedAt est null par défaut

        $form = $this->createMock(FormInterface::class);
        $form->method('handleRequest');
        $form->method('isSubmitted')->willReturn(true);
        $form->method('isValid')->willReturn(true);
        $form->method('getData')->willReturn($article);
        $this->em->method('persist');
        $this->em->method('flush');

        $this->handler->new($form, new Request());

        $this->assertNotNull($article->getPublishedAt());
    }

    public function testNewDoesNotOverrideExistingPublishedAt(): void
    {
        $article = new Article();
        $article->setTitle('Article');
        $article->setContent('Contenu');
        $article->setIsPublished(true);
        $existingDate = new \DateTime('2025-01-01');
        $article->setPublishedAt($existingDate);

        $form = $this->createMock(FormInterface::class);
        $form->method('handleRequest');
        $form->method('isSubmitted')->willReturn(true);
        $form->method('isValid')->willReturn(true);
        $form->method('getData')->willReturn($article);
        $this->em->method('persist');
        $this->em->method('flush');

        $this->handler->new($form, new Request());

        $this->assertSame($existingDate, $article->getPublishedAt());
    }

    public function testEditReturnsTrueOnValidForm(): void
    {
        $article = new Article();
        $article->setTitle('Article édité');
        $article->setContent('Contenu');
        $article->setIsPublished(false);

        $form = $this->createMock(FormInterface::class);
        $form->expects($this->once())->method('handleRequest');
        $form->method('isSubmitted')->willReturn(true);
        $form->method('isValid')->willReturn(true);
        $form->method('getData')->willReturn($article);

        $this->em->expects($this->once())->method('flush');

        $result = $this->handler->edit($form, new Request());

        $this->assertTrue($result);
    }

    public function testEditReturnsFalseOnInvalidForm(): void
    {
        $article = new Article();

        $form = $this->createMock(FormInterface::class);
        $form->method('handleRequest');
        $form->method('isSubmitted')->willReturn(true);
        $form->method('isValid')->willReturn(false);
        $form->method('getData')->willReturn($article);

        $this->em->expects($this->never())->method('flush');

        $result = $this->handler->edit($form, new Request());

        $this->assertFalse($result);
    }

    public function testDeleteRemovesArticleWhenCsrfValid(): void
    {
        $article = $this->createPartialMock(Article::class, ['getId']);
        $article->method('getId')->willReturn(42);

        $request = new Request([], ['_token' => 'valid_token']);

        $this->csrfTokenManager
            ->method('isTokenValid')
            ->with(new CsrfToken('delete42', 'valid_token'))
            ->willReturn(true);

        $this->em->expects($this->once())->method('remove')->with($article);
        $this->em->expects($this->once())->method('flush');

        $this->handler->delete($article, $request);
    }

    public function testDeleteDoesNothingWhenCsrfInvalid(): void
    {
        $article = $this->createPartialMock(Article::class, ['getId']);
        $article->method('getId')->willReturn(42);

        $request = new Request([], ['_token' => 'bad_token']);

        $this->csrfTokenManager
            ->method('isTokenValid')
            ->willReturn(false);

        $this->em->expects($this->never())->method('remove');
        $this->em->expects($this->never())->method('flush');

        $this->handler->delete($article, $request);
    }
}
