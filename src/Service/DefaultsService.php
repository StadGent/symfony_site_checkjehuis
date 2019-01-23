<?php

namespace App\Service;

use App\Entity\DefaultEnergy;
use App\Entity\DefaultSurface;
use App\Entity\DefaultRoof;

class DefaultsService extends AbstractService
{
    /**
     * Get all surfaces (optionally filtered).
     *
     * @param array $filter
     *   Filters (optional).
     *
     * @return DefaultSurface[]
     *   The matching surfaces.
     */
    public function getAllSurfaces(array $filter = array())
    {
        return $this->entityManager
            ->getRepository(DefaultSurface::class)
            ->findBy(
                $this->getFilterCriteria(
                    $filter,
                    ['building-size', 'building-type']
                )
            );
    }

    /**
     * Get all roof surfaces (optionally filtered).
     * @param array $filter
     *   Filters (optional).
     *
     * @return DefaultRoof[]
     *   The matching surfaces.
     */
    public function getAllRoofs(array $filter = array())
    {
        return $this->entityManager
            ->getRepository(DefaultRoof::class)
            ->findBy(
                $this->getFilterCriteria(
                    $filter,
                    ['building-size', 'building-type', 'roof-type']
                )
            );
    }

    /**
     * Get all energy consumptions (optionally filtered).
     *
     * @param array $filter
     *   Filters (optional).
     *
     * @return DefaultEnergy[]
     *   The matching energy consumptions.
     */
    public function getAllEnergy(array $filter = array())
    {
        return $this->entityManager
            ->getRepository(DefaultEnergy::class)
            ->findBy(
                $this->getFilterCriteria(
                    $filter,
                    ['building-size', 'building-type', 'building-year']
                )
            );
    }

    /**
     * Get filter criteria for a query builder based on the given filters.
     *
     * @param array $filter
     *   Filters to create the criteria for.
     * @param array $fields
     *   Fields that may be filtered.
     *
     * @return array
     *   The criteria.
     */
    protected function getFilterCriteria(array $filter, array $fields)
    {
        $criteria = array();

        $filters = [
            'type' => 'building-type',
            'size' => 'building-size',
            'maxYear' => 'building-year',
            'inclined' => 'roof-type',
        ];

        foreach ($filters as $key => $filterName) {
            if (in_array($filterName, $fields) && isset($filter[$filterName]) && !empty($filter[$filterName])) {
                $criteria[$key] = $filter[$filterName];
            }
        }

        return $criteria;
    }

    /**
     * Load a surface by type and size.
     *
     * @param string $type
     *   House type (closed, open, corner).
     * @param string $size
     *   Surface size (large, medium, small, ...).
     *
     * @return DefaultSurface
     *   The matching surface.
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getSurface($type, $size)
    {
        return $this->entityManager
            ->getRepository(DefaultSurface::class)
            ->createQueryBuilder('d')
            ->andWhere('d.type = :type')
            ->andWhere('d.size = :size')
            ->setParameters(array(
                'type' => $type,
                'size' => $size,
            ))
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * Get a default roof by type, size and inclination.
     *
     * @param string $type
     *   House type (closed, open, corner)
     * @param string $size
     *   Roof size (large, medium, small, ...).
     * @param string $inclined
     *   Inclination (yes, no, mixed).
     *
     * @return DefaultRoof
     *   The matching roof.
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getRoof($type, $size, $inclined)
    {
        return $this->entityManager
            ->getRepository(DefaultRoof::class)
            ->createQueryBuilder('d')
            ->andWhere('d.type = :type')
            ->andWhere('d.size = :size')
            ->andWhere('d.inclined = :inclined')
            ->setParameters(array(
                'type' => $type,
                'size' => $size,
                'inclined' => $inclined,
            ))
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * Get a default energy consumption by type, size and year.
     *
     * @param string $type
     *   House type (closed, open, corner)
     * @param string $size
     *   House size (large, medium, small, ...).
     * @param int $year
     *   The (max) year for which this energy consumption is valid.
     *
     * @return DefaultEnergy
     *   The matching energy consumption.
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getEnergy($type, $size, $year)
    {
        /** @var DefaultEnergy[] $defaults */
        $defaults = $this->entityManager
            ->getRepository(DefaultEnergy::class)
            ->createQueryBuilder('d')
            ->andWhere('d.type = :type')
            ->andWhere('d.size = :size')
            ->setParameters(array(
                'type' => $type,
                'size' => $size,
            ))
            ->getQuery()
            ->getResult();

        $myDefault = null;
        foreach ($defaults as $d) {
            if ($d->getMaxYear(true) >= $year && (!$myDefault || $d->getMaxYear(true) < $myDefault->getMaxYear(true))) {
                $myDefault = $d;
            }
        }

        if (!$myDefault) {
            throw new \RuntimeException('No default energy found.');
        }

        return $myDefault;
    }

    /**
     * Load surface by id.
     *
     * @param int $id
     *   The surface id.
     *
     * @return DefaultSurface
     *   The surface.
     */
    public function getSurfaceById($id)
    {
        return $this->entityManager
            ->getRepository(DefaultSurface::class)
            ->find($id);
    }

    /**
     * Load a roof by id.
     *
     * @param int $id
     *   The roof id.
     *
     * @return DefaultRoof
     *   The roof.
     */
    public function getRoofById($id)
    {
        return $this->entityManager
            ->getRepository(DefaultRoof::class)
            ->find($id);
    }

    /**
     * Load energy consumption by id.
     *
     * @param int $id
     *   The energy consumption id.
     *
     * @return DefaultEnergy
     */
    public function getEnergyById($id)
    {
        return $this->entityManager
            ->getRepository(DefaultEnergy::class)
            ->find($id);
    }
}
