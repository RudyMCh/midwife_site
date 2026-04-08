<?php

namespace App\Form\Handler;

use App\Entity\Domain;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class DomainHandler
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
            $domain = $form->getData();
            $this->entityManager->persist($domain);
            $this->entityManager->flush();

            return true;
        }

        return false;
    }

    public function edit(FormInterface $form, Request $request): bool
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return true;
        }

        return false;
    }

    public function delete(Domain $domain, Request $request): void
    {
        $token = new CsrfToken('delete'.$domain->getId(), $request->request->getString('_token'));
        if ($this->csrfTokenManager->isTokenValid($token)) {
            $this->entityManager->remove($domain);
            $this->entityManager->flush();
        }
    }
}
