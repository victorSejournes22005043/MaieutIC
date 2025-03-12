<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    // Redirect to the correct route according to the {page} parametre
    #[Route('/redirection/{page}', name: 'app_redirection')]
    public function redirection(Request $request): RedirectResponse
    {
        switch($request->attributes->get('page'))
        {
            case "register":
                return $this->redirectToRoute('app_register');
                break;
            
            case "login":
                return $this->redirectToRoute('app_login');
                break;
        }
        
    }
}
