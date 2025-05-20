<?php
namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Repository\ConversationRepository;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class PrivateMessageController extends AbstractController
{
    #[Route('/messages', name: 'private_messages')]
    public function listConversations(ConversationRepository $conversationRepo): Response
    {
        $user = $this->getUser();
        $conversations = $conversationRepo->createQueryBuilder('c')
            ->where('c.user1 = :user OR c.user2 = :user')
            ->setParameter('user', $user)
            ->orderBy('c.id', 'DESC')
            ->getQuery()->getResult();
        return $this->render('private_message/conversations.html.twig', [
            'conversations' => $conversations,
        ]);
    }

    #[Route('/messages/{id}', name: 'private_conversation')]
    public function showConversation(Conversation $conversation, MessageRepository $messageRepo): Response
    {
        $user = $this->getUser();
        if ($conversation->getUser1() !== $user && $conversation->getUser2() !== $user) {
            throw $this->createAccessDeniedException();
        }
        $messages = $messageRepo->findBy(['conversation' => $conversation], ['sentAt' => 'ASC']);
        return $this->render('private_message/conversation.html.twig', [
            'conversation' => $conversation,
            'messages' => $messages,
        ]);
    }

    #[Route('/messages/new/{userId}', name: 'private_message_new')]
    public function newConversation(int $userId, UserRepository $userRepo, ConversationRepository $conversationRepo, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $other = $userRepo->find($userId);
        if (!$other || $other === $user) {
            throw $this->createNotFoundException();
        }
        $conversation = $conversationRepo->createQueryBuilder('c')
            ->where('(c.user1 = :user1 AND c.user2 = :user2) OR (c.user1 = :user2 AND c.user2 = :user1)')
            ->setParameter('user1', $user)
            ->setParameter('user2', $other)
            ->getQuery()->getOneOrNullResult();
        if (!$conversation) {
            $conversation = new Conversation();
            $conversation->setUser1($user);
            $conversation->setUser2($other);
            $em->persist($conversation);
            $em->flush();
        }
        return $this->redirectToRoute('private_conversation', ['id' => $conversation->getId()]);
    }

    #[Route('/messages/{id}/send', name: 'private_message_send', methods: ['POST'])]
    public function sendMessage(Request $request, Conversation $conversation, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if ($conversation->getUser1() !== $user && $conversation->getUser2() !== $user) {
            throw $this->createAccessDeniedException();
        }
        $content = trim($request->request->get('content'));
        if ($content) {
            $message = new Message();
            $message->setConversation($conversation);
            $message->setSender($user);
            $message->setContent($content);
            $message->setSentAt(new \DateTime());
            $em->persist($message);
            $em->flush();
        }
        return $this->redirectToRoute('private_conversation', ['id' => $conversation->getId()]);
    }

    #[Route('/messages/{id}/ajax', name: 'private_conversation_ajax')]
    public function ajaxMessages(Conversation $conversation, MessageRepository $messageRepo): Response
    {
        $user = $this->getUser();
        if ($conversation->getUser1() !== $user && $conversation->getUser2() !== $user) {
            throw $this->createAccessDeniedException();
        }
        $messages = $messageRepo->findBy(['conversation' => $conversation], ['sentAt' => 'ASC']);
        return $this->render('private_message/_messages.html.twig', [
            'messages' => $messages,
        ]);
    }
}
