<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Comment;
use App\Entity\UserLike;
use App\Repository\ForumRepository;
use App\Repository\PostRepository;
use App\Repository\CommentRepository;
use App\Repository\UserLikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class ForumController extends AbstractController
{
    #[Route('/like/{id}', name: 'app_forums_like', methods: ['POST'])]
    public function likeComment(
        ?Comment $comment,
        CommentRepository $commentRepository, 
        UserLikeRepository $userLikeRepository, 
        EntityManagerInterface $entityManager
    ): Response {
        if (!$comment) {
            return $this->json(['error' => 'Comment not found'], 404);
        }

        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $existingLike = $userLikeRepository->findOneBy(['user' => $user, 'comment' => $comment]);
        if ($existingLike) {
            $entityManager->remove($existingLike);
            $entityManager->flush();
            return $this->json(['liked' => false]);
        }

        $like = new UserLike();
        $like->setUser($user);
        $like->setComment($comment);

        $entityManager->persist($like);
        $entityManager->flush();

        return $this->json(['liked' => true]);
    }

    #[Route('forums/{category}/{postId?}', name: 'app_forums')]
    public function index(
        ForumRepository $forumRepository, 
        PostRepository $postRepository, 
        CommentRepository $commentRepository, 
        UserLikeRepository $userLikeRepository,
        Request $request, 
        ?int $postId = null
    ): Response {
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

        $selectedPost = null;
        $comments = null;
        $likes = null;
        $userLikes = null;

        if ($postId) {
            $selectedPost = $postRepository->find($postId);
            $comments = $commentRepository->findByPost($postId);
            $likes = [];
            $userLikes = [];

            if (!$selectedPost) {
                throw $this->createNotFoundException('Post not found');
            }

            foreach ($comments as $comment) {
                $likes[] = $userLikeRepository->countByCommentId($comment->getId());
                $userLikes[$comment->getId()] = $this->getUser() 
                    ? $userLikeRepository->hasUserLikedComment($this->getUser()->getId(), $comment->getId()) 
                    : false;
            }

            if ($request->isMethod('POST')) {
                $commentBody = $request->request->get('comment');
                if ($commentBody && $this->isGranted('IS_AUTHENTICATED_FULLY')) {
                    $commentRepository->addComment($commentBody, $selectedPost, $this->getUser());
                    
                    return $this->redirectToRoute('app_forums', [
                        'category' => $category,
                        'postId' => $postId,
                    ]);
                }
            }
        }

        return $this->render('forum/forums.html.twig', [
            'forums' => $forums,
            'category' => $category,
            'posts' => $posts,
            'selectedPost' => $selectedPost,
            'comments' => $comments,
            'likes' => $likes,
            'userLikes' => $userLikes,
        ]);
    }
}