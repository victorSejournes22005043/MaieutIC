<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Repository\ChatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ChatController extends AbstractController{
    #[Route('/chat', name: 'app_chat')]
    public function index(): Response
    {
        return $this->render('chat/index.html.twig', [
            'controller_name' => 'ChatController',
        ]);
    }

    #[Route('/chat/messages', name: 'chat_messages', methods: ['GET'])]
    public function getMessages(ChatRepository $chatRepository): JsonResponse
    {
        $messages = $chatRepository->findBy([], ['creationDate' => 'ASC']);
        $data = array_map(fn(Chat $chat) => [
            'user' => $chat->getUser()->getUsername(),
            'text' => $chat->getText(),
        ], $messages);

        return new JsonResponse($data);
    }

    #[Route('/chat/send', name: 'chat_send', methods: ['POST'])]
    public function sendMessage(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validate the data
        if (!isset($data['text']) || empty(trim($data['text']))) {
            return new JsonResponse(['error' => 'Message text is required'], Response::HTTP_BAD_REQUEST);
        }

        // Validate the user
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        // Create and save the message
        try {
            $message = new Chat();
            $message->setText($data['text']);
            $message->setUser($user);
            $message->setCreationDate(new \DateTime());

            $em->persist($message);
            $em->flush();

            return new JsonResponse(['status' => 'Message sent']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
