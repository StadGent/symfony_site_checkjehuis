<?php

namespace App\Service;

use App\Entity\Subsidy;
use App\Entity\SubsidyCategory;

class SubsidyService extends AbstractService
{
    /**
     * Loads all subsidy categories.
     *
     * @return SubsidyCategory[]
     *   The subsidy categories.
     */
    public function getAllSubsidyCategories()
    {
        return $this->entityManager
            ->getRepository(SubsidyCategory::class)
            ->findAll();
    }

    /**
     * Load a subsidy category by its id.
     *
     * @param int $id
     *   The subsidy category id.
     *
     * @return SubsidyCategory
     *   The subsidy category.
     */
    public function getSubsidyCategory($id)
    {
        return $this->entityManager
            ->getRepository(SubsidyCategory::class)
            ->find($id);
    }

    /**
     * Load all subsidies.
     *
     * @return Subsidy[]
     *   The subsidies.
     */
    public function getAllSubsidies()
    {
        return $this->entityManager
            ->getRepository(Subsidy::class)
            ->findAll();
    }

    /**
     * Load a subsidy by its id.
     *
     * @param int $id
     *   The subsidy id.
     *
     * @return Subsidy
     *   The subsidy.
     */
    public function getSubsidy($id)
    {
        return $this->entityManager
            ->getRepository(Subsidy::class)
            ->find($id);
    }

    /**
     * Load subsidies by their slug.
     *
     * @param string $slug
     *   The slug to load subsidies for.
     *
     * @return Subsidy[]
     *   The subsidies.
     */
    public function getSubsidiesBySlug($slug)
    {
        return $this->entityManager
            ->getRepository(Subsidy::class)
            ->findBy(array('slug' => $slug));
    }
}
