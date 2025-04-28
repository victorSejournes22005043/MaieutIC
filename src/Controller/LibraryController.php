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
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Tag;
use App\Entity\Taggable;
use App\Repository\TaggableRepository;

final class LibraryController extends AbstractController{
    #[Route('/library', name: 'app_library')]
    public function index(AuthorRepository $authorRepository): Response
    {
        $authors = $authorRepository->findAllOrderedByName();

        $createForm = $this->createForm(AuthorType::class);
        $editForm = $this->createForm(AuthorType::class, new Author());

        return $this->render('library/index.html.twig', [
            'controller_name' => 'LibraryController',
            'authors' => $authors,
            'createForm' => $createForm,
            'editForm' => $editForm,
        ]);
    }

    #[Route('/library/author/add', name: 'app_author_add')]
    public function addAuthor(AuthorRepository $authorRepository, Request $request, EntityManagerInterface $em): Response
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
            // Les tags sont automatiquement gérés par le formulaire

            $tagIds = $request->request->all('author')['tags'] ?? [];
            foreach ($tagIds as $tagId) {
                $tag = $em->getRepository(Tag::class)->find($tagId);
                if ($tag) {
                    $taggable = new Taggable();
                    $taggable->setTag($tag);
                    $taggable->setEntityId($author->getId());
                    $taggable->setEntityType('author');
                    $em->persist($taggable);
                }
            }
            $em->flush();

            return $this->redirectToRoute('app_library');
        }

        // Si le formulaire n'est pas valide, on réaffiche la page avec les erreurs
        return $this->render('library/index.html.twig', [
            'libraryController' => 'LibraryController',
            'form' => $form,
        ]);
    }

    #[Route('/library/author/data/{id}', name: 'app_author_data', methods: ['GET'])]
    public function getAuthorData(Author $author, TaggableRepository $taggableRepository): JsonResponse
    {

        $authorTags = $taggableRepository->findByTypeAndId('author', $author->getId());
        $tags = [];
        foreach ($authorTags as $taggable) {
            $tag = $taggable->getTag();
            if ($tag) {
                $tags[] = [
                    'id' => $tag->getId(),
                    'name' => $tag->getName(),
                ];
            }
        }

        return $this->json([
            'name' => $author->getName(),
            'birthYear' => $author->getBirthYear(),
            'deathYear' => $author->getDeathYear(),
            'nationality' => $author->getNationality(),
            'link' => $author->getLink(),
            'image' => $author->getImage(),
            'tags' => $tags,
        ]);
    }

    #[Route('/library/author/edit/{id}', name: 'app_author_edit', methods:['POST'])]
    public function editAuthor(
        AuthorRepository $authorRepository,
        Request $request,
        Author $author,
        EntityManagerInterface $em,
        TaggableRepository $taggableRepository
    ): Response
    {
        $user = $this->getUser();
        if (!$user || $user->getUserType() !== 1) {
            return $this->redirectToRoute('app_login');
        }

        if (!$author) {
            throw $this->createNotFoundException('Auteur non trouvé');
        }

        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $authorRepository->editAuthor($author);

            // --- Gestion des tags ---
            // 1. Supprimer les anciens liens Taggable pour cet auteur
            $oldTaggables = $taggableRepository->findByTypeAndId('author', $author->getId());
            foreach ($oldTaggables as $taggable) {
                $em->remove($taggable);
            }

            // 2. Ajouter les nouveaux liens Taggable
            $tagIds = $request->request->all('author')['tags'] ?? [];
            foreach ($tagIds as $tagId) {
                $tag = $em->getRepository(\App\Entity\Tag::class)->find($tagId);
                if ($tag) {
                    $taggable = new \App\Entity\Taggable();
                    $taggable->setTag($tag);
                    $taggable->setEntityId($author->getId());
                    $taggable->setEntityType('author');
                    $em->persist($taggable);
                }
            }

            $em->flush();
            // --- Fin gestion tags ---

            return $this->redirectToRoute('app_library');
        }

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