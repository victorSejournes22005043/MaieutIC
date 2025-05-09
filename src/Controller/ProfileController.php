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
use App\Form\ProfileEditFormType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TagRepository;
use App\Repository\UserQuestionsRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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

    #[Route('/profile/edit', name: 'app_profile_edit')]
    public function edit(
        Security $security,
        Request $request,
        EntityManagerInterface $entityManager,
        TagRepository $tagRepository,
        UserQuestionsRepository $userQuestionsRepository
    ): Response
    {
        $user = $security->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Questions dynamiques et taggables (mêmes que registration)
        $dynamicQuestions = [
            'Pourquoi cette thématique de recherche vous intéresse-t-elle ?',
            'Pourquoi avez-vous souhaité être chercheur ?',
            'Qu\'aimez vous dans la recherche ?',
            'Quels sont les problèmes de recherche auxquels vous vous intéressez ? *',
            'Quelles sont les méthodologies de recherche que vous utilisez dans votre domaine d\'étude ? *',
            'Qu\'est ce qui, d\'après vous, vous a amené(e) à faire de la recherche ?',
            'Comment vous définirirez vous en tant que chercheur?',
            'Pensez-vous que ce choix ait un lien avec  un évènement de votre biographie ? (rencontre, auteur, environnement personnel, professionnel ....) et si oui pouvez-vous brièvement le/la décrire ?',
            'Pouvez-vous nous raconter qu\'est ce qui a motivé le choix  de vos thématiques de recherche ?',
            'Comment vos expériences personnelles ont-elles influencé votre choix de carrière et vos recherches en sciences humaines et sociales ?',
            'En quelques mots, en tant que chercheur(se) qu\'est ce qui vous anime ?',
            'Si vous deviez choisir 4 auteurs qui vous ont marquée, quels seraient-ils? *',
            'Quelle est la phrase ou la citation qui vous représente le mieux ? *',
        ];
        $taggableQuestions = [
            'Quels mot-clés peuvent être reliés à votre projet en cours ? *',
            'Si vous deviez choisir 5 mots pour vous définir en tant que chercheur (se); quels seraient-ils? *',
        ];
        $tags = $tagRepository->findAllOrderedByName();

        // Pré-remplir les réponses existantes
        $userQuestionsData = array_fill(0, count($dynamicQuestions), '');
        $taggableQuestionsData = array_fill(0, count($taggableQuestions), []);

        foreach ($user->getUserQuestions() as $uq) {
            if (preg_match('/^Question (\d+)$/', $uq->getQuestion(), $m)) {
                $idx = (int)$m[1];
                if (isset($userQuestionsData[$idx])) {
                    $userQuestionsData[$idx] = $uq->getAnswer();
                }
            }
            if (preg_match('/^Taggable Question (\d+)$/', $uq->getQuestion(), $m)) {
                $idx = (int)$m[1];
                if (isset($taggableQuestionsData[$idx])) {
                    $taggableQuestionsData[$idx][] = $tagRepository->findOneBy(['name' => $uq->getAnswer()]);
                }
            }
        }

        $form = $this->createForm(ProfileEditFormType::class, $user, [
            'dynamic_questions' => $dynamicQuestions,
            'taggable_questions' => $taggableQuestions,
            'tags' => $tags,
        ]);

        $form->get('userQuestions')->setData($userQuestionsData);
        $form->get('taggableQuestions')->setData($taggableQuestionsData);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Mettre à jour les réponses aux questions

            // Réenregistrer les nouvelles réponses
            $questions = $form->get('userQuestions')->getData();
            foreach ($questions as $index => $questionText) {
                if (!empty($questionText)) {
                    $userQuestion = new UserQuestions();
                    $userQuestion->setUser($user);
                    $userQuestion->setQuestion('Question ' . $index);
                    $userQuestion->setAnswer($questionText);
                    $user->addUserQuestion($userQuestion);
                    $entityManager->persist($userQuestion);
                }
            }
            $taggableRaw = $form->get('taggableQuestions')->getData() ?? [];
            foreach ($taggableRaw as $index => $ids) {
                if (!is_array($ids)) {
                    $ids = $ids ? [$ids] : [];
                }
                foreach ($ids as $tagId) {
                    $tag = $tagRepository->find($tagId);
                    if ($tag) {
                        // Vérifier s'il existe déjà une UserQuestions pour ce user, question, answer
                        $existing = $userQuestionsRepository->findOneBy([
                            'user' => $user,
                            'question' => "Taggable Question $index",
                            'answer' => $tag->getName()
                        ]);
                        if (!$existing) {
                            dd($tag->getName());
                            $userQuestion = new UserQuestions();
                            $userQuestion->setUser($user);
                            $userQuestion->setQuestion("Taggable Question $index");
                            $userQuestion->setAnswer($tag->getName());
                            $user->addUserQuestion($userQuestion);
                            $entityManager->persist($userQuestion);
                        }
                    }
                }
            }

            $entityManager->flush();
            $this->addFlash('success', 'Profil mis à jour avec succès.');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/edit.html.twig', [
            'editForm' => $form,
            'user' => $user,
            'dynamic_questions' => $dynamicQuestions,
            'taggable_questions' => $taggableQuestions,
        ]);
    }

    #[Route('/profile/delete', name: 'app_profile_delete', methods: ['POST'])]
    public function delete(
        Security $security,
        Request $request,
        SessionInterface $session,
        TokenStorageInterface $tokenStorage
    ): Response {
        $user = $security->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->isCsrfTokenValid('delete-user', $request->request->get('_token'))) {
            $session->set('user_to_delete_id', $user->getId());

            $tokenStorage->setToken(null);
            $session->invalidate();

            return $this->redirectToRoute('app_profile_delete_confirm');
        }

        $this->addFlash('error', 'Token CSRF invalide.');
        return $this->redirectToRoute('app_home');
    }

    #[Route('/profile/delete/confirm', name: 'app_profile_delete_confirm')]
    public function confirmDelete(
        SessionInterface $session,
        EntityManagerInterface $entityManager
    ): Response {
        $id = $session->get('user_to_delete_id');
        $session->remove('user_to_delete_id');

        if ($id) {
            $user = $entityManager->getRepository(User::class)->find($id);
            if ($user) {
                $entityManager->remove($user);
                $entityManager->flush();
            }
        }

        $this->addFlash('success', 'Votre compte a été supprimé.');
        return $this->redirectToRoute('app_home');
    }

    private function findTagIdByName($tags, $name)
    {
        foreach ($tags as $tag) {
            if ($tag->getName() === $name) {
                return $tag->getId();
            }
        }
        return null;
    }
}
