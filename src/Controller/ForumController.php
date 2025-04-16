<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ForumRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class ForumController extends AbstractController
{
    #[Route('/forums/{category}', name: 'app_forums')]

    public function index(ForumRepository $forumRepository, PostRepository $postRepository, Request $request): Response
    {
        $forums = $forumRepository->findAllOrderedByTitle();

        $category = urldecode($request->attributes->get('category'));
        if (!$category) {
            $category = 'General';
        }

        if ($category === 'General') {
            $posts = $postRepository->findAllOrderedByName();
        } else {
            $posts = $postRepository->findByForum($category);
        }

        if (!$posts) {
            throw $this->createNotFoundException('No posts found for this forum');
        }

        return $this->render('forum/forums.html.twig', [
            'forums' => $forums,
            'category' => $category,
            'posts' => $posts,
        ]);
    }
}
