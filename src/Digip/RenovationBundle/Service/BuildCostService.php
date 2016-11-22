<?php

namespace Digip\RenovationBundle\Service;

use Digip\RenovationBundle\Entity\BuildCost;

class BuildCostService extends AbstractService
{
    /**
     * @return BuildCost[]
     */
    public function getAll()
    {
        $repo = $this->getDoctrine()->getRepository('DigipRenovationBundle:BuildCost');

        return $repo->findBy(array(), array('ordering' => 'ASC'));
    }

    /**
     * @param $id
     * @return BuildCost
     */
    public function getCost($id)
    {
        $em = $this->getDoctrine();
        $repo = $em->getRepository('DigipRenovationBundle:BuildCost');

        return $repo->find($id);
    }

    /**
     * @param string $slug
     * @return BuildCost
     */
    public function getCostBySlug($slug)
    {
        $em = $this->getDoctrine();
        $repo = $em->getRepository('DigipRenovationBundle:BuildCost');

        return $repo->findOneBy(array('slug' => $slug));
    }
}