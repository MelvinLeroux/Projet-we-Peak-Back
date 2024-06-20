<?php

namespace App\Controller\Api\v1;

use App\Entity\Comment;
use App\Services\CommentService;
use Doctrine\ORM\EntityManagerInterface;
use Error;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\ModerationService;
#[Route('/api/v1/comments', name: 'app_api_v1_comment')]
class CommentController extends AbstractController
{
    private $commentService;
    private $moderationService;
    public function __construct(CommentService $commentService, ModerationService $moderationService)
    {
        $this->commentService = $commentService;
        $this->moderationService = $moderationService;
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Comment $comment): JsonResponse
    {
        return $this->json(
            $comment,
            200,
            [],
            ['groups' => ['comment.show']]
        );
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            if ($this->moderationService->detecterMotInterdit(json_encode($data))) {
                return new JsonResponse(['message' => 'La création de cette activité a été refusée en raison de la présence de mots interdits.'], 400);
            }
            $comment = $this->commentService->createComment($data, $entityManager);
    
            return $this->json(
                $comment,
                201,
                [],
                ['groups' => ['comment.show']]
            );
        } catch (Error $e) {
            return new JsonResponse(['error' => $e->getMessage()], 403);
        }
    }

    #[Route('/{id}', name: 'update', methods: ['PATCH'])]
    public function update(Comment $comment, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if ($this->moderationService->detecterMotInterdit(json_encode($data))) {
            return new JsonResponse(['message' => 'La création de cette activité a été refusée en raison de la présence de mots interdits.'], 400);
        }
        $comment = $this->commentService->updateComment($comment, $data);


        return $this->json(
            $comment,
            200,
            [],
            ['groups' => ['comment.show']]
        );
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Comment $comment, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($comment);
        $entityManager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
