<?php

namespace App\Repository;

use App\Entity\Dumpling;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Dumpling>
 *
 * @method Dumpling|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dumpling|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dumpling[]    findAll()
 * @method Dumpling[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DumplingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dumpling::class);
    }

    public function searchByKeywords(string $keywords, string $alias = 'd'): QueryBuilder
    {
        $query = $this->createQueryBuilder($alias);
        return $query
            ->where($query->expr()->like($alias . '.title', ':t'))
            ->orWhere($query->expr()->like($alias . '.subtitle', ':subtitle'))
//            ->orWhere("find_in_set(:tag, $alias.tag)")
            ->setParameter('t', "%$keywords%")
            ->setParameter('subtitle', "%$keywords%")
//            ->setParameter('tag', $keywords)
            ->orderBy($alias . '.id', 'ASC');
    }

    public function save(Dumpling $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Dumpling $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Dumpling[] Returns an array of Dumpling objects
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

//    public function findOneBySomeField($value): ?Dumpling
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
