<?php

namespace App\Repository;

use App\Entity\LoginEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<LoginEvent> */
class LoginEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LoginEvent::class);
    }

    public function countForDate(\DateTimeImmutable $date): int
    {
        $start = $date->setTime(0, 0, 0);
        $end = $date->setTime(23, 59, 59);
        return (int)$this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->andWhere('e.occurredAt BETWEEN :s AND :e')
            ->setParameter('s', $start)
            ->setParameter('e', $end)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
