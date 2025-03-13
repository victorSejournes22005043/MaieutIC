<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Forum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    #[Route('/forums/{forum_id}/posts', name: 'app_posts')]
    public function index(int $forum_id, EntityManagerInterface $entityManager): Response
    {
        $forum = $entityManager->getRepository(Forum::class)->find($forum_id);
        $posts = $entityManager->getRepository(Post::class)->findBy(['forum' => $forum]);

        return $this->render('forum/posts.html.twig', [
            'forum' => $forum,
            'posts' => $posts,
        ]);
    }
}