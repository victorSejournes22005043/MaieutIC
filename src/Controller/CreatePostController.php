<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreatePostController extends AbstractController
{
    #[Route('/forums/{forum_id}/createpost', name: 'app_create_post')]
    public function createPost(int $forum_id, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Ensure the user is authenticated
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $forum = $entityManager->getRepository(Forum::class)->find($forum_id);

        if (!$forum) {
            throw $this->createNotFoundException('Forum not found');
        }

        $post = new Post();
        $form = $this->createForm(PostFormType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();

            $post->setUserId($this->getUser());
            $post->setForumId($forum);
            $post->setCreationDate(new \DateTime());
            $post->setLastActivity(new \DateTime());

            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app_posts', ['forum_id' => $forum_id]);
        }

        return $this->render('post/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
