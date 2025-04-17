<?php

namespace App\Controller;

use App\Repository\ResourceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MethodologyController extends AbstractController{
    #[Route('/methodology', name: 'app_methodology')]
    public function index(ResourceRepository $resourceRepository): Response
    {
        // Fetch resources from the database
        $resources = $resourceRepository->findByPage('methodology');

        return $this->render('methodology/index.html.twig', [
            'controller_name' => 'MethodologyController',
            'resources' => $resources,
        ]);
    }
}
