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
use App\Form\ArticleType;
use App\Form\BookType;
use App\Entity\Article;
use App\Entity\Book;
use App\Repository\TagRepository;

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
        $createForm = $this->createForm(ArticleType::class);
        $editForm = $this->createForm(ArticleType::class, new Article());
        return $this->render('library/articles.html.twig', [
            'controller_name' => 'LibraryController',
            'articles' => $articles,
            'createForm' => $createForm,
            'editForm' => $editForm,
        ]);
    }

    #[Route('/library/article/add', name: 'app_article_add')]
    public function addArticle(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user || $user->getUserType() !== 1) {
            throw $this->createAccessDeniedException();
        }

        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setUser($user);
            $em->persist($article);
            $em->flush();

            $tagIds = $request->request->all('article')['tags'] ?? [];
            foreach ($tagIds as $tagId) {
                $tag = $em->getRepository(Tag::class)->find($tagId);
                if ($tag) {
                    $taggable = new Taggable();
                    $taggable->setTag($tag);
                    $taggable->setEntityId($article->getId());
                    $taggable->setEntityType('article');
                    $em->persist($taggable);
                }
            }
            $em->flush();

            return $this->redirectToRoute('app_library_articles');
        }

        return $this->render('library/articles.html.twig', [
            'controller_name' => 'LibraryController',
            'articles' => [],
            'createForm' => $form,
            'editForm' => $this->createForm(ArticleType::class, new Article()),
        ]);
    }

    #[Route('/library/article/data/{id}', name: 'app_article_data', methods: ['GET'])]
    public function getArticleData(Article $article, TaggableRepository $taggableRepository): JsonResponse
    {
        $articleTags = $taggableRepository->findByTypeAndId('article', $article->getId());
        $tags = [];
        foreach ($articleTags as $taggable) {
            $tag = $taggable->getTag();
            if ($tag) {
                $tags[] = [
                    'id' => $tag->getId(),
                    'name' => $tag->getName(),
                ];
            }
        }

        return $this->json([
            'title' => $article->getTitle(),
            'author' => $article->getAuthor(),
            'link' => $article->getLink(),
            'tags' => $tags,
        ]);
    }

    #[Route('/library/article/edit/{id}', name: 'app_article_edit', methods:['POST'])]
    public function editArticle(
        Request $request,
        Article $article,
        EntityManagerInterface $em,
        TaggableRepository $taggableRepository
    ): Response
    {
        $user = $this->getUser();
        if (!$user || $user->getUserType() !== 1) {
            return $this->redirectToRoute('app_login');
        }

        if (!$article) {
            throw $this->createNotFoundException('Article non trouvé');
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($article);

            $oldTaggables = $taggableRepository->findByTypeAndId('article', $article->getId());
            foreach ($oldTaggables as $taggable) {
                $em->remove($taggable);
            }

            $tagIds = $request->request->all('article')['tags'] ?? [];
            foreach ($tagIds as $tagId) {
                $tag = $em->getRepository(Tag::class)->find($tagId);
                if ($tag) {
                    $taggable = new Taggable();
                    $taggable->setTag($tag);
                    $taggable->setEntityId($article->getId());
                    $taggable->setEntityType('article');
                    $em->persist($taggable);
                }
            }

            $em->flush();

            return $this->redirectToRoute('app_library_articles');
        }

        return $this->redirectToRoute('app_library_articles');
    }

    #[Route('/library/article/delete/{id}', name: 'app_article_delete')]
    public function deleteArticle(Request $request, Article $article, EntityManagerInterface $em): RedirectResponse
    {
        $user = $this->getUser();
        if (!$user || $user->getUserType() !== 1) {
            return $this->redirectToRoute('app_login');
        }

        if (!$article) {
            throw $this->createNotFoundException('Article non trouvé');
        }

        if ($this->isCsrfTokenValid('delete_article_' . $article->getId(), $request->request->get('_token'))) {
            $em->remove($article);
            $em->flush();
        }

        return $this->redirectToRoute('app_library_articles');
    }

    #[Route('/library/books', name: 'app_library_books')]
    public function books(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findAllOrderedByTitle();
        $createForm = $this->createForm(BookType::class);
        $editForm = $this->createForm(BookType::class, new Book());
        return $this->render('library/books.html.twig', [
            'controller_name' => 'LibraryController',
            'books' => $books,
            'createForm' => $createForm,
            'editForm' => $editForm,
        ]);
    }

    #[Route('/library/book/add', name: 'app_book_add')]
    public function addBook(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user || $user->getUserType() !== 1) {
            throw $this->createAccessDeniedException();
        }

        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $book->setUser($user);
            $em->persist($book);
            $em->flush();

            $tagIds = $request->request->all('book')['tags'] ?? [];
            foreach ($tagIds as $tagId) {
                $tag = $em->getRepository(Tag::class)->find($tagId);
                if ($tag) {
                    $taggable = new Taggable();
                    $taggable->setTag($tag);
                    $taggable->setEntityId($book->getId());
                    $taggable->setEntityType('book');
                    $em->persist($taggable);
                }
            }
            $em->flush();

            return $this->redirectToRoute('app_library_books');
        }

        return $this->render('library/books.html.twig', [
            'controller_name' => 'LibraryController',
            'books' => [],
            'createForm' => $form,
            'editForm' => $this->createForm(BookType::class, new Book()),
        ]);
    }

    #[Route('/library/book/data/{id}', name: 'app_book_data', methods: ['GET'])]
    public function getBookData(Book $book, TaggableRepository $taggableRepository): JsonResponse
    {
        $bookTags = $taggableRepository->findByTypeAndId('book', $book->getId());
        $tags = [];
        foreach ($bookTags as $taggable) {
            $tag = $taggable->getTag();
            if ($tag) {
                $tags[] = [
                    'id' => $tag->getId(),
                    'name' => $tag->getName(),
                ];
            }
        }

        return $this->json([
            'title' => $book->getTitle(),
            'author' => $book->getAuthor(),
            'link' => $book->getLink(),
            'image' => $book->getImage(),
            'tags' => $tags,
        ]);
    }

    #[Route('/library/book/edit/{id}', name: 'app_book_edit', methods:['POST'])]
    public function editBook(
        Request $request,
        Book $book,
        EntityManagerInterface $em,
        TaggableRepository $taggableRepository
    ): Response
    {
        $user = $this->getUser();
        if (!$user || $user->getUserType() !== 1) {
            return $this->redirectToRoute('app_login');
        }

        if (!$book) {
            throw $this->createNotFoundException('Livre non trouvé');
        }

        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($book);

            $oldTaggables = $taggableRepository->findByTypeAndId('book', $book->getId());
            foreach ($oldTaggables as $taggable) {
                $em->remove($taggable);
            }

            $tagIds = $request->request->all('book')['tags'] ?? [];
            foreach ($tagIds as $tagId) {
                $tag = $em->getRepository(Tag::class)->find($tagId);
                if ($tag) {
                    $taggable = new Taggable();
                    $taggable->setTag($tag);
                    $taggable->setEntityId($book->getId());
                    $taggable->setEntityType('book');
                    $em->persist($taggable);
                }
            }

            $em->flush();

            return $this->redirectToRoute('app_library_books');
        }

        return $this->redirectToRoute('app_library_books');
    }

    #[Route('/library/book/delete/{id}', name: 'app_book_delete')]
    public function deleteBook(Request $request, Book $book, EntityManagerInterface $em): RedirectResponse
    {
        $user = $this->getUser();
        if (!$user || $user->getUserType() !== 1) {
            return $this->redirectToRoute('app_login');
        }

        if (!$book) {
            throw $this->createNotFoundException('Livre non trouvé');
        }

        if ($this->isCsrfTokenValid('delete_book_' . $book->getId(), $request->request->get('_token'))) {
            $em->remove($book);
            $em->flush();
        }

        return $this->redirectToRoute('app_library_books');
    }

    #[Route('/library/tag/search', name: 'app_tag_search', methods: ['GET'])]
    public function tagSearch(Request $request, TagRepository $tagRepository): JsonResponse
    {
        $q = $request->query->get('q', '');
        $tags = $tagRepository->searchByName($q);
        $result = [];
        foreach ($tags as $tag) {
            $result[] = ['id' => $tag->getId(), 'name' => $tag->getName()];
        }
        return $this->json($result);
    }

    #[Route('/library/author/filter', name: 'app_author_filter', methods: ['GET'])]
    public function filterAuthors(Request $request, AuthorRepository $authorRepository, TaggableRepository $taggableRepository): Response
    {
        $tagIds = $request->query->all('tags');
        if (empty($tagIds)) {
            $authors = $authorRepository->findAllOrderedByName();
        } else {
            $authors = $authorRepository->findByTags($tagIds, $taggableRepository);
        }
        return $this->render('library/_authors_grid.html.twig', [
            'authors' => $authors,
            'app' => ['user' => $this->getUser()],
        ]);
    }
}