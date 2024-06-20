<?php

namespace App\Services\Validations;

use App\Entity\Difficulty;
use App\Entity\Sport;
use App\Services\Validations\GenericValidationService;
use Doctrine\ORM\EntityManagerInterface;
use Error;

class ActivityValidationService
{
    private $genericValidationService;
    private $entityManager;

    public function __construct(GenericValidationService $genericValidationService, EntityManagerInterface $entityManager)
    {
        $this->genericValidationService = $genericValidationService;
        $this->entityManager = $entityManager;
    }

    public function validateActivityData(array $data)
    {
        $requiredFields = [
            'name' => "Le nom de l'activité est requis.",
            'description' => "La description de l'activité est requise.",
            'date' => "La date de l'activité est requise.",
            'lat' => "La latitude est requise.",
            'lng' => "La longitude est requise.",
            'sport' => "Le sport de l'activité est requis.",
            'difficulty' => "La difficulté de l'activité est requise.",
            'groupSize' => "La taille du groupe est requise.",
            'city' => "La ville est requise.",
            'createdBy' => "L'utilisateur créateur est requis."
        ];

        $this->genericValidationService->validateRequiredFields($data, array_keys($requiredFields), $requiredFields);

        $this->genericValidationService->validateNumeric($data['groupSize'], 'groupSize');

        // Valider le sport
        $sportId = $data['sport'][0]['id'];
        $sport = $this->entityManager->getRepository(Sport::class)->find($sportId);
        if (!$sport) {
            throw new Error("Le sport spécifié n'existe pas.");
        }

        // Valider la difficulté
        $difficultyId = $data['difficulty']['id'];
        $difficulty = $this->entityManager->getRepository(Difficulty::class)->find($difficultyId);
        if (!$difficulty) {
            throw new Error("La difficulté spécifiée n'existe pas.");
        }

        // Valider le lieu
        if (!isset($data['lat']) || empty($data['lat']) && !isset($data['lng']) || empty($data['lng'])) {
            throw new Error ("Il faut renseigner le lieu.");
        }

        // Valider la date
        $date = \DateTime::createFromFormat('d/m/Y-H:i', $data['date']);
        if (!$date || $date < new \DateTime()) {
            throw new Error("La date est invalide.");
        }
    }
}
