<?php

namespace App\Services;

use App\Entity\Activity;
use App\Entity\Difficulty;
use App\Entity\Participation;
use App\Entity\User;
use App\Entity\Sport;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Services\Base64FileExtractor;
use App\Services\UploadedBase64File;
use Doctrine\ORM\Mapping\Entity;
use Error;

class ActivityService 
{
    private $entityManager;
    private $slugger;
    private $activityThumbnailDirectory;
    

    public function __construct(EntityManagerInterface $entityManager, SluggerInterface $slugger, string $activityThumbnailDirectory)
    {
        $this->entityManager = $entityManager;
        $this->slugger = $slugger;
        $this->activityThumbnailDirectory = $activityThumbnailDirectory;
    }

    public function createActivity(array $data, EntityManagerInterface $entityManager, Base64FileExtractor $base64FileExtractor)
    {
        $requiredKeys = ['name', 'description', 'date', 'city', 'lat', 'lng', 'difficulty', 'groupSize', 'createdBy'];
        foreach ($requiredKeys as $key) {
            if (!isset($data[$key])) {
                return new JsonResponse(['error' => "La clé '$key' est manquante dans les données fournies."], 400);
            }
        }
        
        $activity = new Activity();
        $activity->setName($data['name']);
        $activity->setDescription($data['description']);
        $date = \DateTime::createFromFormat('m/d/Y-H:i', $data['date']);
        $activity->setDate($date);
        $activity->setCity($data['city']);
        $activity->setLat($data['lat']);
        $activity->setLng($data['lng']);
        $difficultyId = $data['difficulty']['id'] ?? null;
        $difficulty = $entityManager->getRepository(Difficulty::class)->find($difficultyId);
        $activity->setDifficulty($difficulty);
        $activity->setGroupSize($data['groupSize']);
        $userID = $data['createdBy'];
        $activity->setCreatedBy($entityManager->getRepository(User::class)->find($userID));
        $activity->setSlug($this->slugger->slug($data['name']));
        $activity->setCreatedAt(new \DateTimeImmutable());
        $activity->setUpdatedAt(new \DateTimeImmutable());
        $creatorParticipation = new Participation();
        $creatorParticipation->setStatus(1)
            ->setUser($activity->getCreatedBy())
            ->setActivity($activity);
            $sportID = $data['sport'][0]["id"];
            
            $activity->addSport($this->entityManager->getRepository(Sport::class)->find($sportID));
       
            // Gestion du fichier thumbnail
            if (isset($data['thumbnail'])) {
                $thumbnailBase64 = $data['thumbnail'];
                $thumbnailBase64 = $base64FileExtractor->extractBase64String($thumbnailBase64);
                
                // Créer une instance UploadedBase64File pour le fichier thumbnail
                $thumbnail = new UploadedBase64File($thumbnailBase64, "blabla");
        
                // Obtenez le chemin relatif de l'image
                $relativePath = $thumbnail->getRelativePath();
        
                // Attribuez le chemin relatif à l'utilisateur
                $activity->setThumbnail($relativePath);
            } 
            
        $entityManager->persist($creatorParticipation);
        $entityManager->persist($activity);


        return $activity;
    }

    public function updateActivity(Activity $activity, array $data)
    {

        if (isset($data['name'])) {
            $activity->setName($data['name']);
            $activity->setSlug($this->slugger->slug(($data['name'])));
        }
        if (isset($data['description'])) {
            $activity->setDescription($data['description']);
        }
        if (isset($data['date'])) {
            $activity->setDate(new \DateTime($data['date']));
        }
        if (isset($data['lat'])) {
            $activity->setLat(floatval($data['lat']));
        }
        if (isset($data['lng'])) {
            $activity->setLng(floatval($data['lng']));
        }
        if (isset($data['difficulty']['id'])) {
            $difficultyId = $data['difficulty']['id'];
            $difficulty = $this->entityManager->getRepository(Difficulty::class)->find($difficultyId);
            $activity->setDifficulty($difficulty);
        }
        if (isset($data['groupSize'])) {
            $activity->setGroupSize($data['groupSize']);
        }
        if  (isset($data['sport']) && !empty($data['sport'])) {
            $sportID = $data['sport'];
            $activity->addSport($this->entityManager->getRepository(Sport::class)->find($sportID));
        }

        $userIds = [];
        if (isset($data['participations'])) {
            foreach ($data['participations'] as $participationData) {
                $userId = $participationData['user']['id'];
                $userIds[] = $userId;

                $participation = $this->entityManager->getRepository(Participation::class)->findOneBy(['user' => $userId, 'activity' => $activity]);

                if (!$participation) {
                    $user = $this->entityManager->getRepository(User::class)->find($userId);

                    if ($user) {
                        $participation = new Participation();
                        $participation->setUser($user);
                        $participation->setActivity($activity);
                        $activity->addParticipation($participation);
                    } else {
                        return new JsonResponse(['error' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
                    }
                }

                if (isset($participationData['status'])) {
                    $participation->setStatus($participationData['status']);
                }

                $this->entityManager->persist($participation);
            }

            foreach ($activity->getParticipations() as $existingParticipation) {
                $existingUser = $existingParticipation->getUser();
                if ($existingUser && !in_array($existingUser->getId(), $userIds)) {
                    $this->entityManager->remove($existingParticipation);
                }
            }
        }

        $activity->setUpdatedAt(new \DateTimeImmutable());
        return $activity;
    }
    
}
