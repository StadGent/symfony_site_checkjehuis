<?php

namespace App\Service;

use App\Entity\Content;

class ContentService extends AbstractService
{
    /**
     * @return Content[]
     */
    public function getAllContent()
    {
        return $this->entityManager
            ->getRepository(Content::class)
            ->findAll();
    }

    /**
     * @param int $id
     * @return Content
     */
    public function getContent($id)
    {
        return $this->entityManager
            ->getRepository(Content::class)
            ->find($id);
    }

    /**
     * @param string $slug
     * @return Content
     */
    public function getContentBySlug($slug)
    {
        return $this->entityManager
            ->getRepository(Content::class)
            ->findOneBy(array('slug' => $slug));
    }
}
