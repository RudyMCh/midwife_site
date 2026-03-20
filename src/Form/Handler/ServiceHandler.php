<?php
namespace App\Form\Handler;

use App\Entity\Service;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormInterface;

class ServiceHandler extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ServiceRepository $serviceRepository;
    public function __construct(EntityManagerInterface $entityManager, ServiceRepository $serviceRepository)
    {
        $this->entityManager = $entityManager;
        $this->serviceRepository = $serviceRepository;
    }

    public function new(FormInterface $form, Request $request): bool
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $service = $form->getData();
            $service->setPosition($this->serviceRepository->count(['domain'=>$service->getDomain()]) + 1);
            $this->entityManager->persist($service);
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

    public function delete(Service $service, Request $request): void
    {
        if ($this->isCsrfTokenValid('delete'.$service->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($service);
            $this->entityManager->flush();
        }
    }
}
