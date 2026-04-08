<?php

namespace App\Controller\AdminController;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[\Symfony\Component\Routing\Attribute\Route(path: '/admin', name: 'admin_utils_')]
class UtilsController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    #[\Symfony\Component\Routing\Attribute\Route(path: '/change-status/{class}/{id}/{prop}/{redirect}', name: 'change_status')]
    public function changeStatus(string $class, int $id, string $prop, string $redirect): RedirectResponse
    {
        /** @var class-string $class */
        $item = $this->em->getRepository($class)->find($id);
        if ($item === null) {
            return $this->redirect(urldecode($redirect));
        }
        $setter = 'set'.ucfirst($prop);
        $getter = 'get'.ucfirst($prop);
        if (property_exists($item, lcfirst($prop))) {
            $item->$setter(!$item->$getter());
        }
        $this->em->flush();

        return $this->redirect(urldecode($redirect));
    }

    #[\Symfony\Component\Routing\Attribute\Route(path: '/change-position-sub/{route}/{id}/{namespace}/{parent}/{parentClass}', name: 'change_position_sub', methods: ['POST'])]
    public function changePositionSub(string $route, int $id, string $namespace, Request $request, ?string $parent = null, ?string $parentClass = null): RedirectResponse
    {
        /** @var class-string $namespace */
        $repository = $this->em->getRepository($namespace);
        $entityOne = $repository->find($id);
        if ($entityOne === null) {
            return $this->redirectToRoute($route.'_index');
        }
        /** @phpstan-ignore-next-line */
        $oldPosition = $entityOne->getPosition();
        $newPosition = $request->query->getInt('position');
        if ($parent !== null && $parentClass !== null) {
            $getter = 'get'.ucfirst($parent);
            $parentEntity = $entityOne->$getter();
            $array = explode('\\', $namespace);
            $command = 'get'.array_pop($array).'s';
            $els = $parentEntity->$command();
        } else {
            $els = $repository->findAll();
        }
        if ($oldPosition < $newPosition) {
            foreach ($els as $item) {
                if ($item->getPosition() > $oldPosition && $item->getPosition() <= $newPosition) {
                    $item->setPosition($item->getPosition() - 1);
                }
            }
        } elseif ($oldPosition > $newPosition) {
            foreach ($els as $item) {
                if ($item->getPosition() >= $newPosition && $item->getPosition() < $oldPosition) {
                    $item->setPosition($item->getPosition() + 1);
                }
            }
        }
        /** @phpstan-ignore-next-line */
        $entityOne->setPosition($newPosition);
        $this->em->flush();

        return $this->redirectToRoute($route.'_index');
    }
}
