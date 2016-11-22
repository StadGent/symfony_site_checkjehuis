<?php

namespace Digip\RenovationBundle\Service;

use Digip\RenovationBundle\Entity\Content;

class ContentService extends AbstractService
{
    /**
     * @return Content[]
     */
    public function getAllContent()
    {
        $em = $this->getDoctrine();
        $repo = $em->getRepository('DigipRenovationBundle:Content');

        return $repo->findAll();
    }

    /**
     * @param int $id
     * @return Content
     */
    public function getContent($id)
    {
        $em = $this->getDoctrine();
        $repo = $em->getRepository('DigipRenovationBundle:Content');

        return $repo->find($id);
    }

    /**
     * @param string $slug
     * @return Content
     */
    public function getContentBySlug($slug)
    {
        $em = $this->getDoctrine();
        $repo = $em->getRepository('DigipRenovationBundle:Content');

        return $repo->findOneBy(array('slug' => $slug));
    }
}