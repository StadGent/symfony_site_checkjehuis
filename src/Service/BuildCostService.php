<?php

namespace App\Service;

use App\Entity\BuildCost;

class BuildCostService extends AbstractService
{
    /**
     * @return BuildCost[]
     */
    public function getAll()
    {
        return $this->entityManager
            ->getRepository(BuildCost::class)
            ->findBy(array(), array('ordering' => 'ASC'));
    }

    /**
     * @param $id
     * @return BuildCost
     */
    public function getCost($id)
    {
        return $this->entityManager
            ->getRepository(BuildCost::class)
            ->find($id);
    }

    /**
     * @param string $slug
     * @return BuildCost
     */
    public function getCostBySlug($slug)
    {
        return $this->entityManager
            ->getRepository(BuildCost::class)
            ->findOneBy(array('slug' => $slug));
    }
}
