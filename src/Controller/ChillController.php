<?php

namespace App\Controller;

use App\Repository\ResourceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ChillController extends AbstractController{
    #[Route('/chill', name: 'app_chill')]
    public function index(ResourceRepository $resourceRepository): Response
    {
        // Fetch resources from the database
        $resources = $resourceRepository->findByPage('chill');
        
        return $this->render('chill/index.html.twig', [
            'controller_name' => 'ChillController',
            'resources' => $resources,
        ]);
    }
}
