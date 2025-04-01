<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdministrativeController extends AbstractController{
    #[Route('/administrative', name: 'app_administrative')]
    public function index(): Response
    {
        return $this->render('administrative/index.html.twig', [
            'controller_name' => 'AdministrativeController',
        ]);
    }
}
