<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

abstract class AbstractRepository extends ServiceEntityRepository
{
    public function findOneOrNew(array $criteria, array|null $orderBy = null)
    {
        $entityName = $this->getEntityName();
        if (!$entity = $this->findOneBy($criteria, $orderBy)) {
            $entity = new $entityName();
        }
        return $entity;
    }
}
