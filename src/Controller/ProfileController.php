<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\UserQuestions;
use App\Repository\PostRepository;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;

final class ProfileController extends AbstractController{
    #[Route('/profile', name: 'app_profile')]
    public function index(Security $security): Response
    {
        $user = $security->getUser();

        // Récupérer les réponses aux questions
        $userQuestions = [];
        if ($user) {
            foreach ($user->getUserQuestions() as $question) {
                $userQuestions[$question->getQuestion()][] = $question->getAnswer();
            }
        }

        // Libellés des questions classiques
        $questionLabels = [
            'Question 0' => 'Pourquoi cette thématique de recherche vous intéresse-t-elle ?',
            'Question 1' => 'Pourquoi avez-vous souhaité être chercheur ?',
            'Question 2' => 'Qu\'aimez-vous dans la recherche ?',
            'Question 3' => 'Quels sont les problèmes de recherche auxquels vous vous intéressez ?',
            'Question 4' => 'Quelles sont les méthodologies de recherche que vous utilisez dans votre domaine d\'étude ?',
            'Question 5' => 'Qu\'est-ce qui, d\'après vous, vous a amené(e) à faire de la recherche ?',
            'Question 6' => 'Comment vous définiriez-vous en tant que chercheur ?',
            'Question 7' => 'Pensez-vous que ce choix ait un lien avec un évènement de votre biographie ?',
            'Question 8' => 'Pouvez-vous nous raconter ce qui a motivé le choix de vos thématiques de recherche ?',
            'Question 9' => 'Comment vos expériences personnelles ont-elles influencé votre choix de carrière et vos recherches en sciences humaines et sociales ?',
            'Question 10' => 'En quelques mots, en tant que chercheur(se) qu\'est-ce qui vous anime ?',
            'Question 11' => 'Si vous deviez choisir 4 auteurs qui vous ont marquée, quels seraient-ils ?',
            'Question 12' => 'Quelle est la phrase ou la citation qui vous représente le mieux ?',
        ];

        // Libellés des questions taggables
        $taggableLabels = [
            'Taggable Question 0' => 'Quels mot-clés peuvent être reliés à votre projet en cours ?',
            'Taggable Question 1' => 'Si vous deviez choisir 5 mots pour vous définir en tant que chercheur(se), quels seraient-ils ?',
        ];

        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'userQuestions' => $userQuestions,
            'questionLabels' => $questionLabels,
            'taggableLabels' => $taggableLabels,
        ]);
    }

    #[Route('/profile/posts', name: 'app_profile_posts')]
    public function posts(Security $security, PostRepository $postRepository): Response
    {
        $user = $security->getUser();
        $posts = [];
        if ($user) {
            $posts = $postRepository->findBy(['user' => $user]);
        }
        return $this->render('profile/_posts.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/profile/comments', name: 'app_profile_comments')]
    public function comments(Security $security, CommentRepository $commentRepository): Response
    {
        $user = $security->getUser();
        $comments = [];
        if ($user) {
            $comments = $commentRepository->findBy(['user' => $user]);
        }
        return $this->render('profile/_comments.html.twig', [
            'comments' => $comments,
        ]);
    }
}
