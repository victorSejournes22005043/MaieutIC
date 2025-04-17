<?php

namespace App\Controller;

use App\Repository\ResourceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Resource;
use App\Form\ResourceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ChillController extends AbstractController{
    #[Route('/chill', name: 'app_chill')]
    public function index(ResourceRepository $resourceRepository): Response
    {
        // Fetch resources from the database
        $resources = $resourceRepository->findByPage('chill');

        $form = $this->createForm(ResourceType::class, new Resource());
        
        return $this->render('chill/index.html.twig', [
            'controller_name' => 'ChillController',
            'resources' => $resources,
            'form' => $form,
        ]);
    }

    #[Route('/chill/resource/add', name: 'app_chill_resource_add', methods: ['POST'])]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        // Vérifie que l'utilisateur est connecté et userType == 1
        $user = $this->getUser();
        if (!$user || $user->getUserType() !== 1) {
            throw $this->createAccessDeniedException();
        }

        $resource = new Resource();
        $form = $this->createForm(ResourceType::class, $resource);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $resource->setPage('chill');
            $resource->setUser($user);
            $em->persist($resource);
            $em->flush();

            return $this->redirectToRoute('app_chill');
        }
    }
}
