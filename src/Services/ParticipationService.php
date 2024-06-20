<?php

namespace App\Services;

use App\Entity\Activity;
use App\Entity\Participation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ParticipationService 
{
    private $entityManager;
    private $activityThumbnailDirectory;
    public function __construct(EntityManagerInterface $entityManager, $activityThumbnailDirectory)
    {
        $this->entityManager = $entityManager;
        $this->activityThumbnailDirectory = $activityThumbnailDirectory;
    }

    public function createParticipation(array $data, EntityManagerInterface $entityManager)
{
    $user = $entityManager->getRepository(User::class)->find($data['user']['id']);
    $activity = $entityManager->getRepository(Activity::class)->find($data['activity']['id']);
    
    // Vérifie si l'utilisateur a déjà une participation à cette activité
    $existingParticipation = $activity->getParticipations()->filter(function ($participation) use ($user) {
        return $participation->getUser() === $user;
    });

    if ($existingParticipation->count() > 0) {
        // Utilisateur déjà inscrit à cette activité
        return new JsonResponse(['error' => 'Eh mais tu participes déjà à cette activité.'], JsonResponse::HTTP_BAD_REQUEST);
    }
    
    // Compte le nombre de participations confirmées pour cette activité
    $confirmedParticipationCount = $activity->getParticipations()->filter(function ($participation) {
        return $participation->getStatus() == 1; // Supposons que le statut 1 indique une participation confirmée
    })->count();
    
    if ($confirmedParticipationCount >= $activity->getGroupSize()) {
        // La limite de participation à cette activité est déjà atteinte
        return new JsonResponse(['error' => 'La limite de participation à cette activité est déjà atteinte.'], JsonResponse::HTTP_BAD_REQUEST);
    }
    
    // Crée une nouvelle participation
    $participation = new Participation;
    $participation->setStatus($data['status']);    
    $participation->setUser($user);
    $participation->setActivity($activity);      
    $entityManager->persist($participation);
    $entityManager->flush();
    
    return $participation;
}
}