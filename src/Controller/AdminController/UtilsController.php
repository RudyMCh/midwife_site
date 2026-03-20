<?php

namespace App\Controller\AdminController;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UtilsController
 * @package App\Controller\AdminController
 * @Route("/admin", name="admin_utils_")
 */
class UtilsController extends AbstractController
{
    private EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    /**
     * @Route("/change-status/{class}/{id}/{prop}/{redirect}", name="change_status")
     * @param $class
     * @param $id
     * @param $prop
     * @param $redirect
     * @return RedirectResponse
     */
    public function changeStatus($class, $id, $prop, $redirect): RedirectResponse
    {
        $item = $this->em->getRepository($class)->findOneById($id);
        $setter = 'set'.ucfirst($prop);
        $getter = 'get'.ucfirst($prop);
        if(property_exists($item, lcfirst ($prop)))
        {
            $item->$setter(!$item->$getter());
        }
        $this->em->flush();
        return $this->redirect(urldecode($redirect));
    }

    /**
     * permet de changer les positions des éléments enfant d'une entité,
     * doit prendre en paramètres :
     * route du parent
     * id de l'élement cible + namespace complet ("Akyos\\CoreBundle\\Entity\\PostDocument")
     * id du parent + namespace complet "Akyos\\CoreBundle\\Entity\\Post"
     * @Route("/change-position-sub/{route}/{id}/{namespace}/{parent}/{parentClass}", name="change_position_sub", methods={"POST"})
     * @param $route
     * @param $id
     * @param $namespace
     * @param Request $request
     * @param null $parent
     * @param null $parentClass
     * @return RedirectResponse
     */
    public function changePositionSub($route, $id, $namespace, Request $request, $parent = null, $parentClass = null): RedirectResponse
    {
        $repository = $this->em->getRepository($namespace);
        $entityOne = $repository->findOneById($id);
        $oldPosition = $entityOne->getPosition();
        $newPosition = $request->get('position');
        if($parent && $parentClass){
            $getter = 'get'.ucfirst($parent);
            $parent = $entityOne->$getter();
            //Pour appeler la collection d'éléments depuis le parent à partir du nom de l'entité mise en param
            $array = explode('\\', $namespace);
            $command = 'get'.array_pop($array).'s';
            $els = $parent->$command();
        }else{
            $els = $repository->findAll();
        }
        if($oldPosition < $newPosition){
            foreach ($els as $item) {
                if($item->getPosition() > $oldPosition && $item->getPosition() <= $newPosition){
                    $item->setPosition($item->getPosition()-1);
                }
            }
        }elseif($oldPosition > $newPosition){
            foreach ($els as $item) {
                if($item->getPosition() >= $newPosition && $item->getPosition() < $oldPosition){
                    $item->setPosition($item->getPosition()+1);
                }
            }
        }
        $entityOne->setPosition($request->get('position'));
        $this->em->flush();

        return $this->redirectToRoute($route.'_index');
    }
}