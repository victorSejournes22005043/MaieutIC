<?php

namespace App\Controller;

use App\Repository\AuthorRepository;
use App\Repository\ArticleRepository;
use App\Repository\BookRepository;
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

    #[Route('/library/articles', name: 'app_library_articles')]
    public function articles(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findAllOrderedByTitle();
        return $this->render('library/articles.html.twig', [
            'controller_name' => 'LibraryController',
            'articles' => $articles,
        ]);
    }

    #[Route('/library/books', name: 'app_library_books')]
    public function books(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findAllOrderedByTitle();
        return $this->render('library/books.html.twig', [
            'controller_name' => 'LibraryController',
            'books' => $books,
        ]);
    }
}