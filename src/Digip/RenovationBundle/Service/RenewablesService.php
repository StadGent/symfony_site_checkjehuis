<?php

namespace Digip\RenovationBundle\Service;

use Digip\RenovationBundle\Entity\Renewable;

class RenewablesService extends AbstractService
{
    /**
     * @return Renewable[]
     */
    public function getAll()
    {
        $em = $this->getDoctrine();
        $repo = $em->getRepository('DigipRenovationBundle:Renewable');

        return $repo->findAll();
    }

    /**
     * @param int $id
     * @return Renewable
     */
    public function getRenewable($id)
    {
        $em = $this->getDoctrine();
        $repo = $em->getRepository('DigipRenovationBundle:Renewable');

        return $repo->find($id);
    }

    /**
     * @param string $slug
     * @return Renewable
     */
    public function getRenewableBySlug($slug)
    {
        $em = $this->getDoctrine();
        $repo = $em->getRepository('DigipRenovationBundle:Renewable');

        return $repo->findOneBy(array("slug" => $slug));
    }
}