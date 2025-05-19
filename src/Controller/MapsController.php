<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;

final class MapsController extends AbstractController{
    #[Route('/maps', name: 'app_maps')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        return $this->render('maps/index.html.twig', [
            'controller_name' => 'MapsController',
            'users' => $users,
        ]);
    }
}
