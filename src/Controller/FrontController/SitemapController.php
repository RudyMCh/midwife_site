<?php

namespace App\Controller\FrontController;

use App\Repository\DomainRepository;
use App\Repository\MidwifeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SitemapController extends AbstractController
{
    #[Route(path: '/robots.txt', name: 'robots_txt', defaults: ['_format' => 'txt'])]
    public function robotsTxt(): Response
    {
        $sitemapUrl = $this->generateUrl('sitemap', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $content = "User-agent: *\nDisallow: /admin/\nDisallow: /login\nDisallow: /reset-password\n\nSitemap: {$sitemapUrl}\n";

        return new Response($content, Response::HTTP_OK, ['Content-Type' => 'text/plain']);
    }

    #[Route(path: '/sitemap.xml', name: 'sitemap', defaults: ['_format' => 'xml'])]
    public function sitemap(
        MidwifeRepository $midwifeRepository,
        DomainRepository $domainRepository,
    ): Response {
        $urls = [];

        $urls[] = [
            'loc' => $this->generateUrl('homepage_homepage', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'priority' => '1.0',
            'changefreq' => 'weekly',
        ];

        $urls[] = [
            'loc' => $this->generateUrl('informations_utiles', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'priority' => '0.5',
            'changefreq' => 'monthly',
        ];

        foreach ($midwifeRepository->findAll() as $midwife) {
            $urls[] = [
                'loc' => $this->generateUrl('midwife_show', ['slug' => $midwife->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL),
                'priority' => '0.9',
                'changefreq' => 'monthly',
                'lastmod' => $midwife->getUpdatedAt()->format('Y-m-d'),
            ];
        }

        foreach ($domainRepository->findAll() as $domain) {
            $urls[] = [
                'loc' => $this->generateUrl('domain_show', ['slug' => $domain->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL),
                'priority' => '0.8',
                'changefreq' => 'monthly',
                'lastmod' => $domain->getUpdatedAt()->format('Y-m-d'),
            ];
        }

        $response = new Response(
            $this->renderView('sitemap.xml.twig', ['urls' => $urls]),
            Response::HTTP_OK,
            ['Content-Type' => 'application/xml']
        );

        return $response;
    }
}
