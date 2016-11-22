<?php

namespace Digip\RenovationBundle\Service;

use Digip\RenovationBundle\Entity\Subsidy;
use Digip\RenovationBundle\Entity\SubsidyCategory;

class SubsidyService extends AbstractService
{
    /**
     * @return SubsidyCategory[]
     */
    public function getAllSubsidyCategories()
    {
        $em = $this->getDoctrine();
        $repo = $em->getRepository('DigipRenovationBundle:SubsidyCategory');

        return $repo->findAll();
    }

    /**
     * @param int $id
     * @return SubsidyCategory
     */
    public function getSubsidyCategory($id)
    {
        $em = $this->getDoctrine();
        $repo = $em->getRepository('DigipRenovationBundle:SubsidyCategory');

        return $repo->find($id);
    }

    /**
     * @return Subsidy[]
     */
    public function getAllSubsidies()
    {
        $em = $this->getDoctrine();
        $repo = $em->getRepository('DigipRenovationBundle:Subsidy');

        return $repo->findAll();
    }

    /**
     * @param int $id
     * @return Subsidy
     */
    public function getSubsidy($id)
    {
        $em = $this->getDoctrine();
        $repo = $em->getRepository('DigipRenovationBundle:Subsidy');

        return $repo->find($id);
    }

    /**
     * @param string $slug
     * @return Subsidy[]
     */
    public function getSubsidiesBySlug($slug)
    {
        $em = $this->getDoctrine();
        $repo = $em->getRepository('DigipRenovationBundle:Subsidy');

        return $repo->findBy(array('slug' => $slug));
    }
}