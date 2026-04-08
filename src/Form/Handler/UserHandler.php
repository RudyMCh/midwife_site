<?php

namespace App\Form\Handler;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class UserHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $hasher,
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
    ) {
    }

    public function new(FormInterface $form, Request $request): bool
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();
            $plainPassword = $form->get('password')->getData();
            if (!empty($plainPassword)) {
                $user->setPassword($this->hasher->hashPassword($user, $plainPassword));
            }
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return true;
        }

        return false;
    }

    public function edit(FormInterface $form, Request $request): bool
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
            if (!empty($plainPassword)) {
                /** @var User $user */
                $user = $form->getData();
                $user->setPassword($this->hasher->hashPassword($user, $plainPassword));
            }
            $this->entityManager->flush();

            return true;
        }

        return false;
    }

    public function delete(User $user, Request $request): void
    {
        $token = new CsrfToken('delete'.$user->getId(), $request->request->getString('_token'));
        if ($this->csrfTokenManager->isTokenValid($token)) {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
        }
    }
}
