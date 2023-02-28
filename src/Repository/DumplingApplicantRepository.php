<?php

namespace App\Repository;

use App\Entity\DumplingApplicant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DumplingApplicant>
 *
 * @method DumplingApplicant|null find($id, $lockMode = null, $lockVersion = null)
 * @method DumplingApplicant|null findOneBy(array $criteria, array $orderBy = null)
 * @method DumplingApplicant[]    findAll()
 * @method DumplingApplicant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DumplingApplicantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DumplingApplicant::class);
    }

    public function save(DumplingApplicant $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DumplingApplicant $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return DumplingApplicants[] Returns an array of DumplingApplicants objects
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

//    public function findOneBySomeField($value): ?DumplingApplicants
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
