<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ForumRepository;
use Doctrine\ORM\EntityManagerInterface;

class ForumController extends AbstractController
{
    #[Route('/forums', name: 'app_forums')]

    public function index(ForumRepository $forumRepository): Response
    {
        $forums = $forumRepository->findAll();

        return $this->render('forum/forums.html.twig', [
            'forums' => $forums,
        ]);
    }

    #[Route('/forums/{id}', name:'forum_show')]
    public function show($id, ForumRepository $forumRepository): Response
    {
        $forum = $forumRepository->find($id);

        if (!$forum) {
            throw $this->createNotFoundException('Forum not found');
        }

        return $this->render('forum/forum.html.twig', [
            'forum' => $forum,
        ]);
    }
}
