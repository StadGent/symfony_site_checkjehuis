<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractService
{
    /**
     * The entity manager.
     *
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * Service constructor.
     *
     * @param EntityManager $entityManager
     *   The entity manager.
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Persist an entity, optionally flushing.
     *
     * @param mixed $entity
     *   The entity to persist. Passing true as $entity will only flush.
     * @param bool $flush
     *   Whether or not to flush.
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
     * @param object $entity
     *   The entity to remove.
     * @param bool $flush
     *   Whether or not to flush.
     */
    public function remove($entity, $flush = true)
    {
        $this->entityManager->remove($entity);
        if ($flush) {
            $this->entityManager->flush();
        }
    }
}
