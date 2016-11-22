<?php

namespace Digip\RenovationBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;

abstract class AbstractService
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var EntityManager
     */
    protected $doctrine;

    /**
     * @param ContainerInterface $container
     * @param EntityManager $doctrine
     */
    public function __construct(ContainerInterface $container, EntityManager $doctrine)
    {
        $this->container = $container;
        $this->doctrine = $doctrine;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param ContainerInterface $container
     * @return $this
     */
    public function setContainer($container)
    {
        $this->container = $container;
        return $this;
    }

    /**
     * @return EntityManager
     */
    public function getDoctrine()
    {
        return $this->doctrine;
    }

    /**
     * @param EntityManager $doctrine
     * @return $this
     */
    public function setDoctrine($doctrine)
    {
        $this->doctrine = $doctrine;
        return $this;
    }

    /**
     * persist an entity, optionally flushing
     * passing true as $entity will only flush
     *
     * @param $entity
     * @param bool $flush
     */
    public function persist($entity = null, $flush = true)
    {
        if ($entity !== true) {
            $this->getDoctrine()->persist($entity);
        }
        if ($flush) {
            $this->getDoctrine()->flush();
        }
    }

    /**
     * proxy for EntityManager::remove
     *
     * @param $entity
     * @param bool $flush
     */
    public function remove($entity, $flush = true)
    {
        $this->getDoctrine()->remove($entity);
        if ($flush) {
            $this->getDoctrine()->flush();
        }
    }
} 