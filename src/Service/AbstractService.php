<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractService
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Persist an entity, optionally flushing.
     * Passing true as $entity will only flush.
     *
     * @param $entity
     * @param bool $flush
     */
    public function persist($entity = null, $flush = true)
    {
        if ($entity !== true) {
            $this->entityManager->persist($entity);
        }
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * Proxy for EntityManager::remove().
     *
     * @param $entity
     * @param bool $flush
     */
    public function remove($entity, $flush = true)
    {
        $this->entityManager->remove($entity);
        if ($flush) {
            $this->entityManager->flush();
        }
    }
}
