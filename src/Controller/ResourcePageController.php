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

final class ResourcePageController extends AbstractController
{
    #[Route(
        '/{page}',
        name: 'app_resource_page',
        requirements: ['page' => 'chill|methodology|administrative']
    )]
    public function index(string $page, ResourceRepository $resourceRepository): Response {
        $allowedPages = ['chill', 'methodology', 'administrative'];
        if (!in_array($page, $allowedPages)) {
            throw $this->createNotFoundException();
        }
        
        $resources = $resourceRepository->findByPage($page);
        $form = $this->createForm(ResourceType::class);

        return $this->render("$page/index.html.twig", [
            'controller_name' => ucfirst($page) . 'Controller',
            'resources' => $resources,
            'form' => $form,
            'page' => $page,
        ]);
    }

    #[Route(
        '/{page}/resource/add',
        name: 'app_resource_add',
        methods: ['POST'],
        requirements: ['page' => 'chill|methodology|administrative']
    )]
    public function add(string $page, Request $request, ResourceRepository $resourceRepository, EntityManagerInterface $em): Response {
        $allowedPages = ['chill', 'methodology', 'administrative'];
        if (!in_array($page, $allowedPages)) {
            throw $this->createNotFoundException();
        }

        $user = $this->getUser();
        if (!$user || $user->getUserType() !== 1) {
            throw $this->createAccessDeniedException();
        }

        $resource = new Resource();
        $form = $this->createForm(ResourceType::class, $resource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $resource->setPage($page);
            $resource->setUser($user);
            $em->persist($resource);
            $em->flush();

            return $this->redirectToRoute('app_resource_page', ['page' => $page]);
        }

        // Si le formulaire n'est pas valide, on réaffiche la page avec les erreurs
        $resources = $resourceRepository->findByPage($page);
        return $this->render("$page/index.html.twig", [
            'controller_name' => ucfirst($page) . 'Controller',
            'resources' => $resources,
            'form' => $form,
            'page' => $page,
        ]);
    }
}