<?php

namespace App\Service;

use App\Entity\Renewable;

class RenewablesService extends AbstractService
{
    /**
     * Load all renewables.
     *
     * @return Renewable[]
     *   The renewables.
     */
    public function getAll()
    {
        return $this->entityManager
            ->getRepository(Renewable::class)
            ->findAll();
    }

    /**
     * Load a renewable by its id.
     *
     * @param int $id
     *   The renewable id.
     *
     * @return Renewable
     *   The renewable.
     */
    public function getRenewable($id)
    {
        return $this->entityManager
            ->getRepository(Renewable::class)
            ->find($id);
    }

    /**
     * Load a renewable by their slug.
     *
     * @param string $slug
     *   The slug to load the renewable for.
     *
     * @return Renewable
     *   The renewable.
     */
    public function getRenewableBySlug($slug)
    {
        return $this->entityManager
            ->getRepository(Renewable::class)
            ->findOneBy(array("slug" => $slug));
    }
}
