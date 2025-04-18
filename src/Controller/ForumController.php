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
    #[Route('/forums/{category}/{postId?}', name: 'app_forums')]
    public function index(ForumRepository $forumRepository, PostRepository $postRepository, Request $request, ?int $postId = null): Response
    {
        $forums = $forumRepository->findAllOrderedByTitle();
        
        // Decode the category from the URL
        $category = urldecode($request->attributes->get('category'));
        if (!$category) {
            $category = 'General';
        }
        
        if ($category === 'General') {
            $posts = $postRepository->findAllOrderedByName();
        } else {
            $posts = $postRepository->findByForum($category);
        }

        // If a postId is provided, fetch the post
        $selectedPost = null;
        if ($postId) {
            $selectedPost = $postRepository->find($postId);
            if (!$selectedPost) {
                throw $this->createNotFoundException('Post not found');
            }
        }

        return $this->render('forum/forums.html.twig', [
            'forums' => $forums,
            'category' => $category,
            'posts' => $posts,
            'selectedPost' => $selectedPost,
        ]);
    }
}
