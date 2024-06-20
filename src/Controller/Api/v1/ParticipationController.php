<?php

namespace App\Controller\Api\v1;

use App\Entity\Participation;
use App\Repository\ParticipationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use App\Services\ParticipationService;
use Symfony\Bundle\SecurityBundle\Security;

#[Route('/api/v1/participations', name: 'app_api_v1_participation')]
class ParticipationController extends AbstractController
{
    private $participationService;

    public function __construct(ParticipationService $participationService)
    {
        $this->participationService = $participationService;
    }
    #[Route('', name: 'list', methods: ['GET'])]
    public function index(ParticipationRepository $participationRepository): JsonResponse
    {
        $participations = $participationRepository->findBy([],['activity' => 'ASC']);
        return $this->json(
            $participations,
            200,
            [],
            ['groups' => ['participation.list']]
        );
    }
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Participation $participation): JsonResponse
    {
        
        return $this->json(
            $participation,
            200,
            [],
            ['groups' => ['participation.show']]
        );
    }
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(
        Request $request,
        #[MapRequestPayload(serializationContext:['groups' => ['participation.create']])]
        Participation $participation,
        EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $participation = $this->participationService->createParticipation($data, $entityManager);
        if($participation instanceof JsonResponse){

        return $participation;}
        
        return $this->json(
            $participation,
            201,
            [],
            ['groups' => ['participation.list']]
        );
    }
    #[Route('/{id}', name: 'update', methods: ['PATCH'])]
    public function update(
        Request $request,
        Participation $participation,
        EntityManagerInterface $entityManager, Security $security): JsonResponse
    {   
        /** @var \App\Entity\User */
        $currentUser = $security->getUser();
        if (!in_array('ROLE_ADMIN', $currentUser->getRoles()) && ($participation->getUser()->getId() !== $currentUser->getId()|| $participation->getActivity()->getCreatedBy()->getId()!== $currentUser->getId())) {
            return new JsonResponse(['error' => 'Vous n\'avez pas la permission de modifier cette participation.'], JsonResponse::HTTP_FORBIDDEN);
        }
        $data = json_decode($request->getContent(), true);
        $participation->setStatus($data['status']);
        $entityManager->flush();
        return $this->json(
            $participation,
            200,
            [],
            ['groups' => ['participation.list']]
        );
    
    }
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Participation $participation, EntityManagerInterface $entityManager,Security $security): JsonResponse
    {
        /** @var \App\Entity\User */
        $currentUser = $security->getUser();
        if (!in_array('ROLE_ADMIN', $currentUser->getRoles()) &&
        $participation->getUser()->getId() !== $currentUser->getId() &&
        $participation->getActivity()->getCreatedBy()->getId() !== $currentUser->getId()) {
            return new JsonResponse(['error' => 'Vous n\'avez pas la possibilité de supprimer cette participation.'], JsonResponse::HTTP_FORBIDDEN);
        }
        $entityManager->remove($participation);
        $entityManager->flush();
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
