<?php

namespace App\Form\Handler;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class ArticleHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
    ) {
    }

    public function new(FormInterface $form, Request $request): bool
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Article $article */
            $article = $form->getData();
            if ($article->isPublished() && $article->getPublishedAt() === null) {
                $article->setPublishedAt(new \DateTime());
            }
            $this->entityManager->persist($article);
            $this->entityManager->flush();

            return true;
        }

        return false;
    }

    public function edit(FormInterface $form, Request $request): bool
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Article $article */
            $article = $form->getData();
            if ($article->isPublished() && $article->getPublishedAt() === null) {
                $article->setPublishedAt(new \DateTime());
            }
            $this->entityManager->flush();

            return true;
        }

        return false;
    }

    public function delete(Article $article, Request $request): void
    {
        $token = new CsrfToken('delete'.$article->getId(), $request->request->getString('_token'));
        if ($this->csrfTokenManager->isTokenValid($token)) {
            $this->entityManager->remove($article);
            $this->entityManager->flush();
        }
    }
}
