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
     * Load all config categories.
     *
     * @return ConfigCategory[]
     *   The config categories.
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
     * Get all config categories for a house.
     *
     * @param House $house
     *   The house to get the config categories for.
     *
     * @return ConfigCategory[]
     *   The config categories for the house.
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
     * Load a config category by its id.
     *
     * @param int $id
     *   The config category id.
     *
     * @return ConfigCategory
     *   The config category.
     */
    public function getCategory($id)
    {
        return $this->entityManager
            ->getRepository(ConfigCategory::class)
            ->find($id);
    }

    /**
     * Load a config category by its slug.
     *
     * @param string $slug
     *   The config category slug.
     *
     * @return ConfigCategory
     *   The config category.
     */
    public function getCategoryBySlug($slug)
    {
        return $this->entityManager
            ->getRepository(ConfigCategory::class)
            ->findOneBy(array('slug' => $slug));
    }

    /**
     * Load config by id.
     *
     * @param int $id
     *   The config id.
     *
     * @return Config
     *   The config.
     */
    public function getConfig($id)
    {
        return $this->entityManager
            ->getRepository(Config::class)
            ->find($id);
    }

    /**
     * Updates the category's percentage, aka it's weight in the calculations.
     *
     * @param string $categorySlug
     *   The slug of the category to update.
     * @param float $percent
     *   The percentage to set it to.
     *
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
     * Updates a matrix value.
     *
     * If the value is set to 0, the transformation is removed.
     *
     * @param ConfigTransformation $configTransformation
     *   The config transformation to update.
     *
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
     * Removes transformations with the same start and end config, for all
     * config entities.
     */
    public function removeDuplicateTransformations()
    {
        $configs = $this->entityManager
            ->getRepository(Config::class)
            ->findAll();

        foreach ($configs as $config) {
            $this->removeDuplicateTransformationsFromConfig($config);
        }

        $this->entityManager->flush();
    }

    /**
     * Removes transformations with the same start and end config, for a single
     * config entity.
     *
     * @param Config $config
     *   The config entity.
     */
    protected function removeDuplicateTransformationsFromConfig(Config $config) {
      $transformations = $config->getTransformations();
      $dedupe = [[], []];
      foreach ($transformations as $transformation) {
          $configId = $transformation->getToConfig()->getId();
          if (in_array($configId, $dedupe[intval($transformation->isInverse())], true)) {
              $this->entityManager->remove($transformation);
              continue;
          }
          $dedupe[intval($transformation->isInverse())][] = $configId;
      }
    }
}
