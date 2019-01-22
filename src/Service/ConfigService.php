<?php

namespace App\Service;

use App\Entity\Config;
use App\Entity\ConfigCategory;
use App\Entity\ConfigTransformation;
use App\Entity\House;
use Doctrine\ORM\EntityNotFoundException;

class ConfigService extends AbstractService
{
    /**
     * @return ConfigCategory[]
     */
    public function getAllCategories()
    {
        /** @var ConfigCategory[] $cats */
        $cats = $this->entityManager
            ->getRepository(ConfigCategory::class)
            ->createQueryBuilder('cat')
            ->select(array('cat', 'conf', 'trans'))
            ->leftJoin('cat.configs', 'conf')
            ->leftJoin('conf.transformations', 'trans')
            ->orderBy('cat.ordering')
            ->getQuery()
            ->getResult();

        $formatted = array();
        foreach ($cats as $c) {
            $formatted[$c->getSlug()] = $c;
        }

        return $formatted;
    }

    /**
     * @param House $house
     * @return \App\Entity\ConfigCategory[]
     */
    public function getAllCategoriesForHouse(House $house)
    {
        $ignoreCategories = array();
        if ($house->hasElectricHeating()) {
            $ignoreCategories[] = ConfigCategory::CAT_HEATING;
        } else {
            $ignoreCategories[] = ConfigCategory::CAT_HEATING_ELEC;
        }

        $qb = $this->entityManager
            ->getRepository(ConfigCategory::class)
            ->createQueryBuilder('cat')
            ->select(array('cat', 'conf', 'trans'))
            ->leftJoin('cat.configs', 'conf')
            ->leftJoin('conf.transformations', 'trans');
        /** @var ConfigCategory[] $cats */
        $cats = $qb
            ->where($qb->expr()->notIn('cat.slug', $ignoreCategories))
            ->orderBy('cat.ordering')
            ->getQuery()
            ->getResult();

        $formatted = array();
        foreach ($cats as $c) {
            $formatted[$c->getSlug()] = $c;
        }

        return $formatted;
    }

    /**
     * @param $id
     * @return ConfigCategory
     */
    public function getCategory($id)
    {
        return $this->entityManager
            ->getRepository(ConfigCategory::class)
            ->find($id);
    }

    /**
     * @param string $slug
     * @return ConfigCategory
     */
    public function getCategoryBySlug($slug)
    {
        return $this->entityManager
            ->getRepository(ConfigCategory::class)
            ->findOneBy(array('slug' => $slug));
    }

    /**
     * @param $id
     * @return Config
     */
    public function getConfig($id)
    {
        return $this->entityManager
            ->getRepository(Config::class)
            ->find($id);
    }

    /**
     * Updates the category's percentage, aka it's weight in the calculations
     *
     * @param string $categorySlug
     * @param float $percent
     * @throws EntityNotFoundException
     */
    public function updateCategoryPercentBySlug($categorySlug, $percent)
    {
        $category =$this->entityManager
            ->getRepository(ConfigCategory::class)
            ->findOneBy(array('slug' => $categorySlug));

        if (!$category) {
            throw new EntityNotFoundException('no config category found for slug: ' . $categorySlug);
        }

        $category->setPercent($percent);
        $this->entityManager->persist($category);
        $this->entityManager->flush();
    }

    /**
     * Updates a matrix value if the value is set to 0, the transformation is removed
     *
     * @param ConfigTransformation $configTransformation
     * @return $this
     */
    public function updateConfigTransformation(ConfigTransformation $configTransformation)
    {
        if ($configTransformation->getValue() == 0) {
            $this->entityManager->remove($configTransformation);
        } else {
            $this->entityManager->persist($configTransformation);
        }
        $this->entityManager->flush();

        return $this;
    }

    /**
     * removes transformations with the same start and end config
     */
    public function removeDuplicateTransformations()
    {
        $configs = $this->entityManager
            ->getRepository(Config::class)
            ->findAll();

        foreach ($configs as $c) {
            $transformations = $c->getTransformations();
            $to = [];
            $toInverse = [];
            foreach ($transformations as $t) {
                $conf = $t->getToConfig()->getId();
                if ($t->isInverse()) {
                    if (in_array($conf, $toInverse, true)) {
                        $this->entityManager->remove($t);
                        continue;
                    }
                    $toInverse[] = $conf;
                } else {
                    if (in_array($conf, $to, true)) {
                        $this->entityManager->remove($t);
                        continue;
                    }
                    $to[] = $conf;
                }
            }
        }

        $this->entityManager->flush();
    }
}
