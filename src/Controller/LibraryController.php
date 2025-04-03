<?php

namespace App\Controller;

use App\Repository\AuthorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LibraryController extends AbstractController{
    #[Route('/library', name: 'app_library')]
    public function index(AuthorRepository $authorRepository): Response
    {
        $authors = $authorRepository->findAllOrderedByName();

        return $this->render('library/index.html.twig', [
            'controller_name' => 'LibraryController',
            'authors' => $authors,
        ]);
    }
}