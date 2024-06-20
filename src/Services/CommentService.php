<?php

namespace App\Services;

use App\Entity\Activity;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Error;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\SluggerInterface;

class CommentService
{
    private $entityManager;
    private $commentDirectory;

    public function __construct(EntityManagerInterface $entityManager, SluggerInterface $slugger, string $commentDirectory)
    {
        $this->entityManager = $entityManager;
        $this->commentDirectory = $commentDirectory;
    }

    public function createComment(array $data, EntityManagerInterface $entityManager)
    {
        $activity= $entityManager->getRepository(Activity::class)->find($data['activity']['id']);
        $currentDate = new \DateTime();
        if ($activity->getDate() <= $currentDate) {
        
        $comment = new Comment;
        $comment->setDescription($data['description']);
        $comment->setActivity($entityManager->getRepository(Activity::class)->find($data['activity']['id']));
        $comment->setUser($entityManager->getRepository(User::class)->find($data['user']['id']));
        $comment->setCreatedAt(new \DateTimeImmutable());
        $entityManager->persist($comment);
        $entityManager->flush();
        
        return $comment;
        }
        else {
            throw new Error('Vous n\'êtes pas autorisé à ajouter des commentaires à cette activité');
        }
    }
    


    

    public function updateComment(Comment $comment, array $data): Comment
    {
        if (isset($data['description'])) {
            $comment->setDescription($data['description']);
        }
        $comment->setUpdatedAt(new \DateTimeImmutable());
        $this->entityManager->flush();
        return $comment;
    }
}
