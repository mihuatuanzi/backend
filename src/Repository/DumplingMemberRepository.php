<?php

namespace App\Repository;

use App\Entity\DumplingMember;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DumplingMember>
 *
 * @method DumplingMember|null find($id, $lockMode = null, $lockVersion = null)
 * @method DumplingMember|null findOneBy(array $criteria, array $orderBy = null)
 * @method DumplingMember[]    findAll()
 * @method DumplingMember[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DumplingMemberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DumplingMember::class);
    }

    public function save(DumplingMember $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DumplingMember $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function searchByKeywords(?string $keywords, string $alias = 'm'): QueryBuilder
    {
        $query = $this->createQueryBuilder($alias);
        if ($keywords) {
            $query = $query
                ->where($query->expr()->like($alias . '.nickname', ':n'))
                ->setParameter('n', "%$keywords%");
        }
        return $query->orderBy($alias . '.id', 'ASC');
    }

//    /**
//     * @return DumplingMember[] Returns an array of DumplingMember objects
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

//    public function findOneBySomeField($value): ?DumplingMember
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
