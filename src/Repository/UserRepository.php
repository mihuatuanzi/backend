<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function searchByKeywords(string $keywords, string $alias = 'u'): QueryBuilder
    {
        $query = $this->createQueryBuilder($alias);
        return $query
            ->where($query->expr()->like($alias . '.nickname', ':val'))
            ->setParameter('val', "%$keywords%")
            ->orderBy($alias . '.id', 'ASC');
    }

    public function increaseExp(User $entity, int $exp = 1)
    {
        $this->createQueryBuilder('u')
            ->update()
            ->set('u.exp', 'u.exp + :val')
            ->where('u.id = :id')
            ->setParameters([
                'val' => $exp,
                'id' => $entity->getId()
            ])
            ->getQuery()
            ->execute();
    }

    public function setRolesByIdentifiers(array $identifiers, array $roles)
    {
        $query = $this->createQueryBuilder('u');
        $query
            ->update()
            ->set('u.roles', ':roles')
            ->where($query->expr()->in('u.unique_id', ':identifiers'))
            ->getQuery()
            ->execute([
                'identifiers' => $identifiers,
                'roles' => json_encode($roles)
            ]);
    }


//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
