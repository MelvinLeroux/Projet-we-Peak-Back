<?php

namespace App\Services;

use App\Repository\ActivityRepository;
use Symfony\Component\HttpFoundation\Request;

class HomeListService
{
    private $activityRepository;

    public function __construct(ActivityRepository $activityRepository)
    {
        $this->activityRepository = $activityRepository;
    }

    public function getActivitiesNearby($latitude, $longitude): array
    {
        
        $latitude = str_replace(',', '.', $latitude);
        $longitude = str_replace(',', '.', $longitude);
    
        // Vérifier si les valeurs de latitude et de longitude sont présentes dans l'URL
        if ($latitude === null || $longitude === null) {
            // Les paramètres de latitude et de longitude sont manquants dans l'URL
            throw new \InvalidArgumentException('Les paramètres de latitude et de longitude sont manquants dans l\'URL');
        }
    
        // Vérifier si les valeurs sont des nombres
        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            // Les valeurs de latitude et de longitude ne sont pas valides
            throw new \InvalidArgumentException('Les valeurs de latitude et de longitude ne sont pas valides');
        }
    
        // Convertion des valeurs en float
        $latitude = floatval($latitude);
        $longitude = floatval($longitude);
    
        return $this->activityRepository->findActivitiesNearby($latitude, $longitude);
    }
}