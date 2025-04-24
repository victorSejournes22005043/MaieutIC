<?php

namespace App\Controller;

use App\Repository\AuthorRepository;
use App\Repository\ArticleRepository;
use App\Repository\BookRepository;
use App\Form\AuthorType;
use App\Entity\Author;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class LibraryController extends AbstractController{
    #[Route('/library', name: 'app_library')]
    public function index(AuthorRepository $authorRepository): Response
    {
        $authors = $authorRepository->findAllOrderedByName();
        $form = $this->createForm(AuthorType::class);

        return $this->render('library/index.html.twig', [
            'controller_name' => 'LibraryController',
            'authors' => $authors,
            'form' => $form,
        ]);
    }

    #[Route('/library/author/add', name: 'app_author_add')]
    public function addAuthor(AuthorRepository $authorRepository, Request $request): Response
    {
        $user = $this->getUser();
        if (!$user || $user->getUserType() !== 1) {
            throw $this->createAccessDeniedException();
        }

        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $author->setUser($user);
            $authorRepository->addAuthor($author);

            return $this->redirectToRoute('app_library');
        }

        // Si le formulaire n'est pas valide, on réaffiche la page avec les erreurs
        return $this->render('library/index.html.twig', [
            'libraryController' => 'LibraryController',
            'form' => $form,
        ]);
    }

    #[Route('/library/author/edit/{id}', name: 'app_author_edit', methods:['POST'])]
    public function editAuthor(AuthorRepository $authorRepository, Request $request, Author $author): Response
    {
        $user = $this->getUser();
        if (!$user || $user->getUserType() !== 1) {
            return $this->redirectToRoute('app_login'); // Ou utilisez throw $this->createAccessDeniedException();
        }

        if (!$author) {
            throw $this->createNotFoundException('Auteur non trouvé');
        }

        $author->setUser($user);
        $author->setName($request->request->get('name'));
        $author->setImage($request->request->get('image'));
        $author->setBirthYear($request->request->get('birthYear'));
        $author->setDeathYear($request->request->get('deathYear'));
        $author->setNationality($request->request->get('nationality'));
        $author->setLink($request->request->get('link'));
        $authorRepository->editAuthor($author);


        return $this->redirectToRoute('app_library');
    }

    #[Route('/library/author/delete/{id}', name: 'app_author_delete')]
    public function deleteAuthor(AuthorRepository $authorRepository, Request $request, Author $author): RedirectResponse
    {
        $user = $this->getUser();
        if (!$user || $user->getUserType() !== 1) {
            return $this->redirectToRoute('app_login'); // Ou utilisez throw $this->createAccessDeniedException();
        }

        if (!$author) {
            throw $this->createNotFoundException('Auteur non trouvé');
        }

        if ($this->isCsrfTokenValid('delete_author_' . $author->getId(), $request->request->get('_token'))) {
            $authorRepository->removeAuthor($author, true);
        }

        return $this->redirectToRoute('app_library');
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