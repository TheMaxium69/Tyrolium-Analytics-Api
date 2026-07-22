<?php

namespace App\Repository;

use App\Entity\Input;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Input>
 *
 * @method Input|null find($id, $lockMode = null, $lockVersion = null)
 * @method Input|null findOneBy(array $criteria, array $orderBy = null)
 * @method Input[]    findAll()
 * @method Input[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

class InputRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Input::class);
    }

    public function findByAdvancedFilters(array $filters): array {

        $qb = $this->createQueryBuilder('i');

        // Filtre par ID de projet
        if (!empty($filters['$project_tag'])) {
            $qb->andWhere('i.tag = :$project_tag')
                ->setParameter('$project_tag', $filters['$project_tag']);
        }

        // Filtre par IP
        if (!empty($filters['ip'])) {
            $qb->andWhere('i.ip = :ip')
                ->setParameter('ip', $filters['ip']);
        }

        // Filtre par nom de page
        if (!empty($filters['pageName'])) {
            $qb->andWhere('i.page_name = :pageName')
                ->setParameter('pageName', $filters['pageName']);
        }

        // Filtre par URI
        if (!empty($filters['uri'])) {
            $qb->andWhere('i.uri = :uri')
                ->setParameter('uri', $filters['uri']);
        }

        // Filtre par id useritium
       // if (!empty($filters['useritiumId'])) {      // J'ai pas le useritium id donc je le commente
       // }


        return $qb->getQuery()->getResult();
    }

}
