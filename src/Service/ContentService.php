<?php

namespace App\Service;

use App\Entity\Content;

class ContentService extends AbstractService
{
    /**
     * Get all content.
     *
     * @return Content[]
     *   An array of all content.
     */
    public function getAllContent()
    {
        return $this->entityManager
            ->getRepository(Content::class)
            ->findAll();
    }

    /**
     * Get content by id.
     *
     * @param int $id
     *   The content it.
     *
     * @return Content
     *   The content.
     */
    public function getContent($id)
    {
        return $this->entityManager
            ->getRepository(Content::class)
            ->find($id);
    }

    /**
     * Get content by slug.
     *
     * @param string $slug
     *   The slug.
     *
     * @return Content
     *   The content.
     */
    public function getContentBySlug($slug)
    {
        return $this->entityManager
            ->getRepository(Content::class)
            ->findOneBy(array('slug' => $slug));
    }
}
