<?php

namespace App\Service;

use App\Entity\BuildCost;

class BuildCostService extends AbstractService
{
    /**
     * Get all build costs.
     *
     * @return BuildCost[]
     *   The build costs.
     */
    public function getAll()
    {
        return $this->entityManager
            ->getRepository(BuildCost::class)
            ->findBy(array(), array('ordering' => 'ASC'));
    }

    /**
     * Get a build cost by id.
     *
     * @param int $id
     *   The build cost id.
     *
     * @return BuildCost
     *   The build cost.
     */
    public function getCost($id)
    {
        return $this->entityManager
            ->getRepository(BuildCost::class)
            ->find($id);
    }

    /**
     * Get build cost by slug.
     *
     * @param string $slug
     *   The build cost slug.
     *
     * @return BuildCost
     *   The build cost.
     */
    public function getCostBySlug($slug)
    {
        return $this->entityManager
            ->getRepository(BuildCost::class)
            ->findOneBy(array('slug' => $slug));
    }
}
