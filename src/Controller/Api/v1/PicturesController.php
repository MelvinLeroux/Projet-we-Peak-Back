<?php

namespace App\Controller\Api\v1;

use App\Entity\Pictures;
use App\Services\PicturesService;
use Doctrine\ORM\EntityManagerInterface;
use Error;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\Base64FileExtractor;

#[Route('/api/v1/pictures', name: 'app_api_v1_picture')]
class PicturesController extends AbstractController
{
    private $picturesService;
    private $base64FileExtractor;

    public function __construct(PicturesService $picturesService, Base64FileExtractor $base64FileExtractor)
    {
        $this->picturesService = $picturesService;
        $this->base64FileExtractor = $base64FileExtractor;

    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Pictures $picture): JsonResponse
    {
        return $this->json(
            $picture,
            200,
            [],
            ['groups' => ['picture.show']]
        );
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $picture = $this->picturesService->createPicture($data, $entityManager, $this->base64FileExtractor);
    
            return $this->json(
                $picture,
                201,
                [],
                ['groups' => ['picture.show']]
            );
        } catch (Error $e) {
            return new JsonResponse(['error' => $e->getMessage()], 403);
        }
    }

    #[Route('/{id}', name: 'update', methods: ['PATCH'])]
    public function update(Pictures $pictures, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $picture = $this->picturesService->updatePicture($pictures, $data);

        $entityManager->flush();

        return $this->json(
            $picture,
            200,
            [],
            ['groups' => ['picture.show']]
        );
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Pictures $picture, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($picture);
        $entityManager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
