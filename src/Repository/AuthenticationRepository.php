<?php

namespace App\Repository;

use App\Config\AuthCredentialType;
use App\Config\UserType;
use App\Entity\Authentication;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * @extends ServiceEntityRepository<Authentication>
 *
 * @method Authentication|null find($id, $lockMode = null, $lockVersion = null)
 * @method Authentication|null findOneBy(array $criteria, array $orderBy = null)
 * @method Authentication[]    findAll()
 * @method Authentication[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @method Authentication findOneOrNew(array $criteria, array $orderBy = null)
 */
class AuthenticationRepository extends AbstractRepository
{
    public function __construct(
        ManagerRegistry       $registry,
        private readonly ParameterBagInterface $parameterBag
    )
    {
        parent::__construct($registry, Authentication::class);
    }

    public function save(Authentication $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Authentication $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOrCreateByEmail(string $email, UserType $userType): Authentication
    {
        $auth = $this->findOneBy([
            'credential_type' => AuthCredentialType::Email, 'credential_key' => $email
        ]);
        if (null === $auth) {
            $auth = new Authentication();
            $auth->setCredentialType(AuthCredentialType::Email);
            $auth->setCredentialKey($email);
            $auth->setCreatedAt(new DateTimeImmutable());
            // User 必须在 Auth 之前保存
            $user = $auth->createUserByType($userType);
            $user->getUserState()->setAppVersion($this->parameterBag->get('env.app_version'));
            $this->getEntityManager()->persist($user);
        }
        return $auth;
    }

//    /**
//     * @return Authentication[] Returns an array of Authentication objects
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

//    public function findOneBySomeField($value): ?Authentication
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
