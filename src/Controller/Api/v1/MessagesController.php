<?php

namespace App\Controller\Api\v1;

use App\Entity\Messages;
use App\Entity\User;
use App\Repository\MessagesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/messages', name: 'app_api_v1_messages')]
class MessagesController extends AbstractController
{
    #[Route('/received/{id}', name: 'receivedList', methods: ['GET'])]
    public function listMessagesReceivedByUser(User $user, MessagesRepository $messagesRepository): JsonResponse
    {
        // Récupérer tous les messages reçus par l'utilisateur
        $receivedMessages = $messagesRepository->findAllReceivedByUser($user);
        // Convertir les messages en tableau associatif
        $messagesArray = [];
        foreach ($receivedMessages as $message) {
            $messagesArray[] = [
                'id' => $message->getId(),
                'title' => $message->getTitle(),
                'message' => $message->getMessage(),
                'createdAt' => $message->getCreatedAt()->format('Y-m-d H:i:s'),
                'isRead' => $message->isRead(),
                'sender' => [
                    'id' => $message->getSender()->getId(),
                    'username' => $message->getSender()->getPseudo(),
                    // Ajoutez d'autres propriétés de l'entité User si nécessaire
                ],
                // Vous pouvez ajouter d'autres données de message ici si nécessaire
            ];
            return new JsonResponse($messagesArray);

        }
    }
    #[Route('/sent/{id}', name: 'sentList', methods: ['GET'])]
    public function listMessagesSentByUser(User $user, MessagesRepository $messagesRepository): JsonResponse
    {
        // Récupérer tous les messages reçus par l'utilisateur
        $receivedMessages = $messagesRepository->findAllSentByUser($user);
        // Convertir les messages en tableau associatif
        $messagesArray = [];
        foreach ($receivedMessages as $message) {
            $messagesArray[] = [
                'id' => $message->getId(),
                'title' => $message->getTitle(),
                'message' => $message->getMessage(),
                'createdAt' => $message->getCreatedAt()->format('Y-m-d H:i:s'),
                'isRead' => $message->isRead(),
                'sender' => [
                    'id' => $message->getSender()->getId(),
                    'username' => $message->getSender()->getPseudo(),
                    // Ajoutez d'autres propriétés de l'entité User si nécessaire
                ],
                // Vous pouvez ajouter d'autres données de message ici si nécessaire
            ];
            return new JsonResponse($messagesArray);

        }
    }
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $message = new Messages();
        $message->setTitle($data['title']);
        $message->setMessage($data['message']);
        $message->setSender($this->getUser());
        $recipient = $entityManager->getRepository(User::class)->find($data['recipient']);
        $message->setRecipient($recipient);
        $message->setRead(false);
        $entityManager->persist($message);
        $entityManager->flush();
        return new JsonResponse(['message' => 'Message sent'], Response::HTTP_CREATED);

    }
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
public function delete(Messages $message, EntityManagerInterface $entityManager, Security $security): JsonResponse
{
    // Récupérer l'utilisateur actuellement authentifié
    $currentUser = $security->getUser();

    // Vérifier si l'utilisateur actuel est l'expéditeur ou le destinataire du message
    if ($message->getSender() === $currentUser) {
        // Si l'utilisateur actuel est l'expéditeur, marquer le message comme supprimé pour le sender
        $message->setDeletedForSender(true);
    } elseif ($message->getRecipient() === $currentUser) {
        // Si l'utilisateur actuel est le destinataire, marquer le message comme supprimé pour le destinataire
        $message->setDeletedForRecipient(true);
    } else {
        // Si l'utilisateur actuel n'est ni l'expéditeur ni le destinataire, renvoyer une erreur 403 (Interdit)
        return new JsonResponse(['error' => 'Vous n\'êtes pas autorisé à supprimer ce message.'], JsonResponse::HTTP_FORBIDDEN);
    }
    if ($message->isDeletedForSender() && $message->isDeletedForRecipient()) {
        $entityManager->remove($message);
    } else {
        // Sinon, enregistrez simplement les modifications dans la base de données
        $entityManager->flush();
    }
    // Enregistrer les modifications dans la base de données
    $entityManager->flush();

    // Retourner une réponse JSON pour indiquer que le message a été supprimé avec succès
    return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
}
    
}