<?php

namespace App\Repository;

use App\Entity\FormFieldValidator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FormFieldValidator>
 *
 * @method FormFieldValidator|null find($id, $lockMode = null, $lockVersion = null)
 * @method FormFieldValidator|null findOneBy(array $criteria, array $orderBy = null)
 * @method FormFieldValidator[]    findAll()
 * @method FormFieldValidator[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormFieldValidatorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FormFieldValidator::class);
    }

    public function save(FormFieldValidator $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FormFieldValidator $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return FormFieldValidator[] Returns an array of FormFieldValidator objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?FormFieldValidator
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
