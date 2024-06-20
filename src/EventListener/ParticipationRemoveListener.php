<?php 

namespace App\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use App\Entity\Activity;
use Doctrine\ORM\EntityManagerInterface;

class ParticipationRemoveListener
{
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        // Vérifier si l'entité est de type Activity
        if ($entity instanceof Activity) {
            $currentDate = new \DateTime();

            // Récupérer les participations associées à l'activité
            foreach ($entity->getParticipations() as $participation) {
                // Vérifier si le statut de la participation est zéro et si la date est passée
                if ($participation->getStatus() === 0 && $entity->getDate() < $currentDate) {
                    $entityManager = $args->getObjectManager();
                    $entityManager->remove($participation);
                }
            }
        }
    }
}

