<?php

namespace App\Form\Handler;

use App\Entity\Midwife;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class MidwifeHandler
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
            $midwife = $form->getData();
            $this->entityManager->persist($midwife);
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

    public function delete(Midwife $midwife, Request $request): void
    {
        $token = new CsrfToken('delete'.$midwife->getId(), $request->request->getString('_token'));
        if ($this->csrfTokenManager->isTokenValid($token)) {
            $this->entityManager->remove($midwife);
            $this->entityManager->flush();
        }
    }
}
