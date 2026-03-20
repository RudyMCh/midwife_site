<?php

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;

class Tools extends AbstractController
{

    public function saveFile($file, string $directory): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        // this is needed to safely include the file name as part of the URL
        $safeFilename = trim(htmlspecialchars($originalFilename));
        $safeFilename = preg_replace( '/[^a-z0-9]+/', '-', strtolower( $safeFilename ));
        $newFilename = $safeFilename.'-'.uniqid('', true).'.'.$file->guessExtension();

        // Move the file to the directory where brochures are stored
        try {
            $file->move(
                $this->getParameter($directory),
                $newFilename
            );
        } catch (FileException $e) {
            throw new Exception('le tranfert ne s\'est pas bien passé'.$e);
        }
        return $newFilename;
    }

    public function getProperties($class)
    {
        $phpDocExtractor = new PhpDocExtractor();
        $reflectionExtractor = new ReflectionExtractor();

        $listExtractors = [$reflectionExtractor];
        $typeExtractors = [$phpDocExtractor, $reflectionExtractor];
        $descriptionExtractors = [$phpDocExtractor];
        $accessExtractors = [$reflectionExtractor];
        $propertyInitializableExtractors = [$reflectionExtractor];

        $propertyInfo = new PropertyInfoExtractor(
            $listExtractors,
            $typeExtractors,
            $descriptionExtractors,
            $accessExtractors,
            $propertyInitializableExtractors
        );
        return $propertyInfo->getProperties($class);
    }
}