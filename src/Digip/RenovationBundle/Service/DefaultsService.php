<?php

namespace Digip\RenovationBundle\Service;

use Digip\RenovationBundle\Entity\DefaultEnergy;
use Digip\RenovationBundle\Entity\DefaultSurface;
use Digip\RenovationBundle\Entity\DefaultRoof;

class DefaultsService extends AbstractService
{
    /**
     * @param array $filter
     * @return DefaultSurface[]
     */
    public function getAllSurfaces(array $filter = array())
    {
        $repo = $this->getDoctrine()->getRepository('DigipRenovationBundle:DefaultSurface');

        return $repo->findBy($this->getFilterCriteria($filter, array('building-size', 'building-type')));
    }

    /**
     * @param array $filter
     * @return DefaultRoof[]
     */
    public function getAllRoofs(array $filter = array())
    {
        $repo = $this->getDoctrine()->getRepository('DigipRenovationBundle:DefaultRoof');

        return $repo->findBy($this->getFilterCriteria($filter, array('building-size', 'building-type', 'roof-type')));
    }

    /**
     * @param array $filter
     * @return DefaultEnergy[]
     */
    public function getAllEnergy(array $filter = array())
    {
        $repo = $this->getDoctrine()->getRepository('DigipRenovationBundle:DefaultEnergy');

        return $repo->findBy($this->getFilterCriteria($filter, array('building-size', 'building-type', 'building-year')));
    }

    protected function getFilterCriteria(array $filter, array $fields)
    {
        $criteria = array();

        if (in_array('building-type', $fields) && isset($filter['building-type']) && !empty($filter['building-type'])) {
            $criteria['type'] = $filter['building-type'];
        }
        if (in_array('building-size', $fields) && isset($filter['building-size']) && !empty($filter['building-size'])) {
            $criteria['size'] = $filter['building-size'];
        }
        if (in_array('building-year', $fields) && isset($filter['building-year']) && !empty($filter['building-year'])) {
            $criteria['maxYear'] = $filter['building-year'];
        }
        if (in_array('roof-type', $fields) && isset($filter['roof-type']) && !empty($filter['roof-type'])) {
            $criteria['inclined'] = $filter['roof-type'];
        }

        return $criteria;
    }

    /**
     * @param string $type
     * @param string $size
     * @return DefaultSurface
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getSurface($type, $size)
    {
        $repo = $this->getDoctrine()->getRepository('DigipRenovationBundle:DefaultSurface');

        $qb = $repo->createQueryBuilder('d')
            ->andWhere('d.type = :type')
            ->andWhere('d.size = :size')
            ->setParameters(array(
                'type' => $type,
                'size' => $size,
            ))
        ;

        return $qb->getQuery()->getSingleResult();
    }

    /**
     * @param string $type
     * @param string $size
     * @param bool $inclined
     * @return DefaultRoof
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getRoof($type, $size, $inclined)
    {
        $repo = $this->getDoctrine()->getRepository('DigipRenovationBundle:DefaultRoof');

        $qb = $repo->createQueryBuilder('d')
            ->andWhere('d.type = :type')
            ->andWhere('d.size = :size')
            ->andWhere('d.inclined = :inclined')
            ->setParameters(array(
                'type' => $type,
                'size' => $size,
                'inclined' => $inclined,
            ))
        ;

        return $qb->getQuery()->getSingleResult();
    }

    /**
     * @param $type
     * @param $size
     * @param $year
     * @return DefaultEnergy
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getEnergy($type, $size, $year)
    {
        $repo = $this->getDoctrine()->getRepository('DigipRenovationBundle:DefaultEnergy');

        $qb = $repo->createQueryBuilder('d')
            ->andWhere('d.type = :type')
            ->andWhere('d.size = :size')
            ->setParameters(array(
                'type' => $type,
                'size' => $size,
            ))
        ;

        /** @var DefaultEnergy[] $defaults */
        $defaults = $qb->getQuery()->getResult();
        $myDefault = null;
        foreach ($defaults as $d) {
            if ($d->getMaxYear(true) >= $year && (!$myDefault || $d->getMaxYear(true) < $year)) {
                $myDefault = $d;
            }
        }

        if (!$myDefault) {
            throw new \RuntimeException('no default energy found.');
        }

        return $myDefault;
    }

    public function getSurfaceById($id)
    {
        $repo = $this->getDoctrine()->getRepository('DigipRenovationBundle:DefaultSurface');

        return $repo->find($id);
    }

    public function getRoofById($id)
    {
        $repo = $this->getDoctrine()->getRepository('DigipRenovationBundle:DefaultRoof');

        return $repo->find($id);
    }

    public function getEnergyById($id)
    {
        $repo = $this->getDoctrine()->getRepository('DigipRenovationBundle:DefaultEnergy');

        return $repo->find($id);
    }
}