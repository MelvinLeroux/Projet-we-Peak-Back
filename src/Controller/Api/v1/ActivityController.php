<?php

namespace App\Controller\Api\v1;

use App\Entity\Activity;
use App\Entity\Sport;
use App\Repository\ActivityRepository;
use App\Services\ActivityService;
use App\Services\ActivityListByFilterService;
use App\Services\Base64FileExtractor;
use App\Services\HomeListService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Base;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
#[Route('/api/v1/activities', name: 'app_api_v1_activity')]
class ActivityController extends AbstractController
{
    private $activityService;
    private $homeListService;
    private $activityListByFilterService;
    private $base64FileExtractor;

    public function __construct(private SluggerInterface $slugger, ActivityService $activityService, HomeListService $homeListService, ActivityListByFilterService $activityListByFilterService, Base64FileExtractor $base64FileExtractor)
    {
        $this->slugger = $slugger;
        $this->activityService = $activityService;
        $this->homeListService = $homeListService;
        $this->activityListByFilterService = $activityListByFilterService;
        $this->base64FileExtractor = $base64FileExtractor;
    }
    
    #[Route('/home/{lat}/{lng}', name: 'home_list', methods:["GET"])]
    public function homeList(Request $request): JsonResponse
    {
        $latitude = $request->attributes->get('lat');
        $longitude = $request->attributes->get('lng');
        // Appel de la méthode getActivitiesNearby du service HomeListService pour récupérer les activités à proximité
        $activities = $this->homeListService->getActivitiesNearby($latitude, $longitude);

        return $this->json(
            $activities,
            200,
            [],
            ['groups' => ['activity.list']]
        );
    }

    #[Route('/{id}', name: 'show',  methods:["GET"])]
    public function show(Activity $activity): JsonResponse
    {
        return $this->json(
            $activity,
            200,
            [],
            ['groups' => ['activity.show']]
        );
    }
    #[Route('/page/{page}/{lat}/{lng}', name: 'list', methods:"GET")]
    public function list(Request $request, int $page): JsonResponse
    {   
        
        $latitude = $request->attributes->get('lat');
        $longitude = $request->attributes->get('lng');
        $page = $request->attributes->get('page');
        $filters = $request->query->all();
        
        // Appel de la méthode getActivitiesByFilter du service ActivityListByFilterService pour récupérer les activités en fonction des filtres
        $activities = $this->activityListByFilterService->getActivitiesByFilter($filters, $latitude, $longitude, $page);

        return $this->json(
            $activities,
            200,
            [],
            ['groups' => ['activity.list']]
        );
    }


    #[Route('', name: 'create',  methods:["POST"])]
    #[IsGranted('ROLE_USER')]
    public function create(
        Request $request,
        #[MapRequestPayload(serializationContext:['groups' => ['activity.create']])]
        Activity $activity,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        // Appel de la méthode createActivity du service ActivityService qui contient toutes les règles de création d'une activité
        $createdActivity = $this->activityService->createActivity($data, $entityManager, $this->base64FileExtractor);
    
        if ($createdActivity instanceof JsonResponse) {
            // Si c'est le cas, retourne directement cette réponse JSON
            return $createdActivity;
        }
    
        // Sinon, tout s'est bien passé, continuez avec la réponse JSON pour l'activité créée avec le code de statut 201
        $entityManager->flush();
    
        return $this->json(
            $createdActivity, // Utilisez $createdActivity au lieu de $activity
            201,
            [],
            ['groups' => ['activity.show']]
        );
    }

    #[Route('/{id}', name: 'update',  methods:["PATCH"])]
    public function update(Activity $activity, Request $request,ActivityService $activityService, EntityManagerInterface $entityManager, Security $security): JsonResponse
    {
        /** @var \App\Entity\User */
        $currentUser = $security->getUser();
        // Vérifier si l'utilisateur actuel est administrateur ou s'il est l'utilisateur qui a créé l'activité
        if (!in_array('ROLE_ADMIN', $currentUser->getRoles()) && !$activity->getCreatedBy() === $currentUser->getId()) {
            return new JsonResponse(['error' => 'Vous n\'avez pas la permission de modifier cette activité.'], JsonResponse::HTTP_FORBIDDEN);
        }
        $data = json_decode($request->getContent(), true);   
        
        // Appel de la méthode updateActivity du service ActivityService pour mettre à jour l'activité     
        $activity = $this->activityService->updateActivity($activity, $data);
        
        $entityManager->flush();

        return $this->json(
        $activity,
        200, 
        [], 
        ['groups' => ['activity.show']]);
    }
        // Appelez la fonction updateActivity du service ActivityService
        
    
    #[Route('/{id}', name: 'delete',  methods:["DELETE"])]
    public function delete(Activity $activity, EntityManagerInterface $entityManager, Security $security): JsonResponse
    {
        /** @var \App\Entity\User */
        $currentUser = $security->getUser();
        // Vérifier si l'utilisateur actuel est administrateur ou s'il est l'utilisateur qui a créé l'activité  
        if (!in_array('ROLE_ADMIN', $currentUser->getRoles()) && !$activity->getCreatedBy()->getId() !== $currentUser->getId()) {
            return new JsonResponse(['error' => 'Vous n\'avez pas la permission de supprimer cette activité.'], JsonResponse::HTTP_FORBIDDEN);
        
        }
        $entityManager->remove($activity);
        $entityManager->flush();
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('/sport/{id}', name: 'sport', methods:["GET"])]
    public function getActivitiesBySport(Sport $sport, ActivityRepository $activityRepository): JsonResponse
    {
        $activities = $activityRepository->findActivitiesBySport($sport);
        return $this->json(
            $activities,
            200,
            [],
            ['groups' => ['activity.list']]
        );
    }

    #[Route('/group-size/{groupSize}', name: 'group_size', methods:["GET"])]
    public function getActivitiesByGroupSize(int $groupSize, ActivityRepository $activityRepository, Request $request): JsonResponse
    {
        $groupSize = $request->attributes->get('groupSize');

        $groupSize = intval($groupSize);

        $activities = $activityRepository->findActivitiesByGroupSize($groupSize);
        return $this->json(
            $activities,
            200,
            [],
            ['groups' => ['activity.list']]
        );
    }

    public function getActivityByDifficulty(int $difficulty, ActivityRepository $activityRepository): JsonResponse
    {
        $activities = $activityRepository->findActivitiesByDifficulty($difficulty);
        return $this->json(
            $activities,
            200,
            [],
            ['groups' => ['activity.list']]
        );
    }

}   





