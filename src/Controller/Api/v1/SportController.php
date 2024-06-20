<?php

namespace App\Controller\Api\v1;

use App\Entity\Sport;
use App\Repository\SportRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;


#[Route('/api/v1/sports', name: 'app_api_v1_sport')]
class SportController extends AbstractController
{
    private $sportService;
    public function __construct(private SluggerInterface $slugger)
    {
        $this->slugger = $slugger;

    }
    #[Route('', name: 'list',  methods:"GET")]
    public function index(SportRepository $sportRepository): JsonResponse
    {
        $sport = $sportRepository->findAll();

        return $this->json(
            $sport,
            200,
            [],
            ['groups' => ['sport.list']]
        );
    }

    #[Route('/{id}', name: 'show',  methods:"GET")]
    public function show(Sport $sport): JsonResponse
    {
        return $this->json(
            $sport,
            200,
            [],
            ['groups' => ['sport.show']]
        );
    }
}

