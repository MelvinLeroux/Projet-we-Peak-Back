<?php

namespace App\Repository;

use App\Entity\Sport;
use App\Entity\Activity;
use App\Entity\Difficulty;
use Doctrine\ORM\EntityManagerInterface;
use DoctrineExtensions\Query\Mysql\Acos;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class ActivityRepository extends ServiceEntityRepository 
{
    private $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Activity::class);
        $this->entityManager = $entityManager;
        
    }

    public function findActivitiesNearby(float $latitude, float $longitude, float $radius = 30)
    {
        // requête DQL directe
        $query = $this->entityManager->createQuery(
            'SELECT a, 
            ( 6371 * 
                acos(
                    sin(radians(:lat)) * sin(radians(a.lat)) + 
                    cos(radians(:lat)) * cos(radians(a.lat)) * cos(radians(a.lng) - radians(:lng))
                )
            ) AS distance 
            FROM App\Entity\Activity a
            HAVING distance < :radius
            ORDER BY distance'
        );
    
        $query->setParameter('lat', $latitude);
        $query->setParameter('lng', $longitude);
        $query->setParameter('radius', $radius);
        $query->setMaxResults(9);
    
        return $query->getResult();
    }

    public function findActivitiesNearbyByPageWithFilters( $latitude,  $longitude, int $page, array $filters = [], float $radius = 30)
    {
        
        $firstResult = ($page - 1) * 15;
        $queryBuilder = $this->createQueryBuilder('a');
        $currentDate = new \DateTime();
        $queryBuilder->andWhere('a.date >= :currentDate')
                    ->setParameter('currentDate', $currentDate->format('Y-m-d'));
        
        if (isset($filters['startDate']) && isset($filters['endDate'])) {
            $startDate = new \DateTime($filters['startDate']);
            $endDate = new \DateTime($filters['endDate']);
        
            // Ajouter la condition pour filtrer les activités dans la plage de dates spécifiée
            $queryBuilder->andWhere('a.date >= :startDate AND a.date <= :endDate')
                        ->setParameter('startDate', $startDate->format('Y-m-d'))
                        ->setParameter('endDate', $endDate->format('Y-m-d'));
        }
    
        if (isset($filters['difficulty'])) {
            $queryBuilder->join('a.difficulty', 'd')
                        ->andWhere('d.value = :difficulty')
                        ->setParameter('difficulty', $filters['difficulty']);
        }
    
        if (isset($filters['groupSize'])) {
            $groupSize = $filters['groupSize'];
            $groupSizeRanges = [
                '2-5' => ['min' => 2, 'max' => 5],
                '6-10' => ['min' => 6, 'max' => 10],
                '11-40' => ['min' => 11, 'max' => 40],
            ];
        
            foreach ($groupSizeRanges as $range => $values) {
                if ($groupSize === $range) {
                        $queryBuilder->andWhere('a.groupSize >= :minGroupSize AND a.groupSize <= :maxGroupSize')
                                     ->setParameter('minGroupSize', $values['min'])
                                     ->setParameter('maxGroupSize', $values['max']);
                  
                    break; // Sortir de la boucle une fois que la plage correspondante est trouvée
                }
            }
        }
    
        if (isset($filters['sport'])) {
            $queryBuilder->join('a.sports', 's')
                        ->andWhere('s.slug = :sport')
                        ->setParameter('sport', $filters['sport']);
        }
        // Ajouter les conditions de distance à la requête
        $queryBuilder->select('a', '(6371 * ACOS(SIN(RADIANS(:lat)) * SIN(RADIANS(a.lat)) + COS(RADIANS(:lat)) * COS(RADIANS(a.lat)) * COS(RADIANS(a.lng) - RADIANS(:lng)))) AS distance')
        ->having('distance < :radius')
        ->setParameter('lat', $latitude)
        ->setParameter('lng', $longitude)
        ->setParameter('radius', $radius);

        // Si le paramètre de distance est spécifié dans les filtres, utilisez-le pour remplacer le rayon par défaut
        if (isset($filters['distance'])) {
        $queryBuilder->setParameter('radius', $filters['distance']);
        }
        

        $queryBuilder->orderBy('a.date', 'ASC')
                ->setFirstResult($firstResult)
                ->setMaxResults(15);
    
        return $queryBuilder->getQuery()->getResult();
    }




    //    /**
    //     * @return Activity[] Returns an array of Activity objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Activity
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}


