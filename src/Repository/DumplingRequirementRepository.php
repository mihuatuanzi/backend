<?php

namespace App\Repository;

use App\Entity\DumplingRequirement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DumplingRequirement>
 *
 * @method DumplingRequirement|null find($id, $lockMode = null, $lockVersion = null)
 * @method DumplingRequirement|null findOneBy(array $criteria, array $orderBy = null)
 * @method DumplingRequirement[]    findAll()
 * @method DumplingRequirement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DumplingRequirementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DumplingRequirement::class);
    }

    public function save(DumplingRequirement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DumplingRequirement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return DumplingRequirement[] Returns an array of DumplingRequirement objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DumplingRequirement
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
