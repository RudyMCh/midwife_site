<?php
namespace App\Form\Handler;

use App\Entity\Midwife;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormInterface;

class MidwifeHandler extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
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
        if ($this->isCsrfTokenValid('delete'.$midwife->getId(), $request->request->getString('_token'))) {
            $this->entityManager->remove($midwife);
            $this->entityManager->flush();
        }
    }
}
