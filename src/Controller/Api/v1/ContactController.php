<?php

namespace App\Controller\Api\v1;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Services\ContactService;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Services\ModerationService;

class ContactController extends AbstractController
{
    private $contactService;
    private $moderationService;
    public function __construct(ContactService $contactService, ModerationService $moderationService)
    {
        $this->contactService = $contactService;
        $this->moderationService = $moderationService;
    }


    #[Route('/api/v1/contact', name: 'app_api_v1_contact', methods: ['POST'])]
    public function contact(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!ctype_digit($data['phone'])) {
            // Si le numéro de téléphone n'est pas composé uniquement de chiffres, retournez une réponse JSON avec l'erreur
            return new JsonResponse(['errors' => ['Le numéro de téléphone ne doit contenir que des chiffres']], JsonResponse::HTTP_BAD_REQUEST);
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            // Si l'adresse e-mail n'est pas valide, retournez une réponse JSON avec l'erreur
            return new JsonResponse(['errors' => ['L\'adresse e-mail fournie n\'est pas valide']], JsonResponse::HTTP_BAD_REQUEST);
        }
        if ($this->moderationService->detecterMotInterdit(json_encode($data))) {
            return new JsonResponse(['message' => 'La création de cette activité a été refusée en raison de la présence de mots interdits.'], 400);
        }      
        if ($this->moderationService->detecterMotInterdit(json_encode($data))) {
            return new JsonResponse(['message' => 'La création de cette activité a été refusée en raison de la présence de mots interdits.'], 400);
        }  
        $this->contactService->sendEmail($data);
        return new JsonResponse(['success' => true]);

    }
}
