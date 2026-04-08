<?php
namespace App\Controller\FrontController;

use App\Repository\DomainRepository;
use App\Repository\HomePageRepository;
use App\Repository\MidwifeRepository;
use App\Repository\OfficeRepository;
use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DomainController
 * @package App\Controller\AdminController
 */
#[\Symfony\Component\Routing\Attribute\Route(path: '/', name: 'homepage_')]
class HomePageController extends AbstractController
{
    /**
     * @param MidwifeRepository $midwifeRepository
     * @param DomainRepository $domainRepository
     * @param HomePageRepository $homePageRepository
     * @param OfficeRepository $officeRepository
     * @return Response
     */
    #[\Symfony\Component\Routing\Attribute\Route(path: '', name: 'homepage')]
    public function homepage(MidwifeRepository $midwifeRepository, DomainRepository $domainRepository,
                             HomePageRepository $homePageRepository, OfficeRepository $officeRepository): Response
    {
        $homePage = $homePageRepository->findAll();
        $office = $officeRepository->findAll();
        $office = $office[0];
        $homePage = $homePage[0];
        return $this->render('front/homepage.html.twig', [
            'midwives'=>$midwifeRepository->findAll(),
            'domains'=>$domainRepository->findAll(),
            'homepage'=>$homePage,
            'office'=>$office,
            'meta_title'=>$homePage->getMetaTitle(),
            'meta_description'=>$homePage->getMetaDescription()
        ]);
    }
}