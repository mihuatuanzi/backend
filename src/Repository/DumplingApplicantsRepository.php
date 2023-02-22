<?php

namespace App\Repository;

use App\Entity\DumplingApplicants;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DumplingApplicants>
 *
 * @method DumplingApplicants|null find($id, $lockMode = null, $lockVersion = null)
 * @method DumplingApplicants|null findOneBy(array $criteria, array $orderBy = null)
 * @method DumplingApplicants[]    findAll()
 * @method DumplingApplicants[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DumplingApplicantsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DumplingApplicants::class);
    }

    public function save(DumplingApplicants $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DumplingApplicants $entity, bool $flush = false): void
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
