<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class CreatePostController extends AbstractController
{
    #[Route('/createpost', name: 'app_create_post')]

    public function createPost(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Ensure the user is authenticated
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $post = new Post();
        $form = $this->createForm(PostFormType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();

            $post->setUser($this->getUser());
            $post->setCreationDate(new \DateTime());
            $post->setLastActivity(new \DateTime());
            $post->setNbComments(0);

            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('post/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
