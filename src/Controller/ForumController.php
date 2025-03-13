<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ForumRepository;

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
}
