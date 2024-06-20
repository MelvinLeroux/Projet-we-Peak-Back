<?php

namespace App\Services;

use App\Entity\Difficulty;
use App\Repository\ActivityRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Sport;
use Doctrine\ORM\EntityManagerInterface;


class ActivityListByFilterService
{
    private $entityManager;
    private $activityRepository;
    

    public function __construct(ActivityRepository $activityRepository, EntityManagerInterface $entityManager)
    {
        $this->activityRepository = $activityRepository;
        $this->entityManager = $entityManager;
        
        
    }

    public function getActivitiesByFilter(array $filters, $latitude, $longitude, $page): array
    {
    // Récupérer les paramètres de l'URL


    // Vérifier si les valeurs de latitude et de longitude sont présentes dans l'URL
    if ($latitude === null || $longitude === null) {
    
        return new JsonResponse(['error' => 'Les paramètres de latitude et de longitude sont manquants dans l\'URL'], JsonResponse::HTTP_BAD_REQUEST);
    }

    $latitude = str_replace(',', '.', $latitude);
    $longitude = str_replace(',', '.', $longitude);

    // Vérifier si les valeurs sont des nombres
    if (!is_numeric($latitude) || !is_numeric($longitude)) {

        return new JsonResponse(['error' => 'Les valeurs de latitude et de longitude ne sont pas valides'], JsonResponse::HTTP_BAD_REQUEST);
    }

    // Convertion des valeurs en float
    $latitude = floatval($latitude);
    $longitude = floatval($longitude);
    $page = intval($page);

    // Valider la date
    if (isset($filters['date'])) {
        $date = $filters['date'];

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return new JsonResponse(['error' => 'Format de date invalide. Utilisez le format YYYY-MM-DD.'], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
    
    // Valider la difficulté
    if (isset($filters['difficulty'])) {
        $difficultyValue = $filters['difficulty'];
        // Définir les options valides pour la difficulté
        $difficulty = $this->entityManager->getRepository(Difficulty::class)->findOneBy(['value' => $difficultyValue]);
        if (!$difficulty) {
            return new JsonResponse(['error' => 'La difficulté spécifiée n\'existe pas.'], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    // Valider la taille du groupe
    if (isset($filters['groupSize'])) {
        // Vérifier si la valeur de groupSize est valide
        $validGroupSizeValues = ['2-5', '6-10', '11-40'];
        if (!in_array($filters['groupSize'], $validGroupSizeValues)) {
            return new JsonResponse(['error' => 'La taille du groupe spécifiée n\'est pas valide. Utilisez une des valeurs suivantes : "2-5", "6-10", "11-40"'], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    // Valider la distance
    if (isset($filters['distance'])) {
        $distance = $filters['distance'];
        if (!is_numeric($distance) || $distance < 0) {
            return new JsonResponse(['error' => 'La distance doit être un nombre positif.'], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    // Valider le sport
    if (isset($filters['sport'])) {
        $sportSlug = $filters['sport'];
        
        // Vérifier si le nom du sport est une chaîne non vide
        if (empty($sportSlug)) {
            return new JsonResponse(['error' => 'Le nom du sport ne peut pas être vide.'], JsonResponse::HTTP_BAD_REQUEST);
        }
    
        // Vérifier si le sport existe dans la base de données
        $sport = $this->entityManager->getRepository(Sport::class)->findOneBy(['slug' => $sportSlug]);
        if (!$sport) {
            return new JsonResponse(['error' => 'Le sport spécifié n\'existe pas.'], JsonResponse::HTTP_BAD_REQUEST);
        }

    }
    // Effectuer la requête en utilisant les filtres
    return $this->activityRepository->findActivitiesNearbyByPageWithFilters($latitude, $longitude, $page, $filters);
    }
}