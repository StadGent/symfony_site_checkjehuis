<?php

namespace Digip\RenovationBundle\Service;

use Digip\RenovationBundle\Entity\Config;
use Digip\RenovationBundle\Entity\ConfigCategory;
use Digip\RenovationBundle\Entity\ConfigTransformation;
use Digip\RenovationBundle\Entity\House;
use Doctrine\ORM\EntityNotFoundException;

class ConfigService extends AbstractService
{
    /**
     * @return ConfigCategory[]
     */
    public function getAllCategories()
    {
        $em = $this->getDoctrine();
        $repo = $em->getRepository('DigipRenovationBundle:ConfigCategory');

        /** @var Config[] $configs */
        $query = $repo->createQueryBuilder('cat')
            ->select(array('cat', 'conf', 'trans'))
            ->leftJoin('cat.configs', 'conf')
            ->leftJoin('conf.transformations', 'trans')
            ->orderBy('cat.ordering')
            ;

        /** @var ConfigCategory[] $cats */
        $cats = $query->getQuery()->getResult();

        $formatted = array();
        foreach ($cats as $c) {
            $formatted[$c->getSlug()] = $c;
        }

        return $formatted;
    }

    /**
     * @param House $house
     * @return \Digip\RenovationBundle\Entity\ConfigCategory[]
     */
    public function getAllCategoriesForHouse(House $house)
    {
        $em = $this->getDoctrine();
        $repo = $em->getRepository('DigipRenovationBundle:ConfigCategory');

        $ignoreCategories = array();
        if ($house->hasElectricHeating()) $ignoreCategories[] = ConfigCategory::CAT_HEATING;
        else $ignoreCategories[] = ConfigCategory::CAT_HEATING_ELEC;

        /** @var Config[] $configs */
        $qb = $repo->createQueryBuilder('cat');
        $query = $qb
            ->select(array('cat', 'conf', 'trans'))
            ->leftJoin('cat.configs', 'conf')
            ->leftJoin('conf.transformations', 'trans')
            ->where($qb->expr()->notIn('cat.slug', $ignoreCategories))
            ->orderBy('cat.ordering')
            ;

        /** @var ConfigCategory[] $cats */
        $cats = $query->getQuery()->getResult();

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
        $em = $this->getDoctrine();
        $repo = $em->getRepository('DigipRenovationBundle:ConfigCategory');

        return $repo->find($id);
    }

    /**
     * @param string $slug
     * @return ConfigCategory
     */
    public function getCategoryBySlug($slug)
    {
        $em = $this->getDoctrine();
        $repo = $em->getRepository('DigipRenovationBundle:ConfigCategory');

        return $repo->findOneBy(array('slug' => $slug));
    }

    /**
     * @param $id
     * @return Config
     */
    public function getConfig($id)
    {
        $em = $this->getDoctrine();
        $repo = $em->getRepository('DigipRenovationBundle:Config');

        return $repo->find($id);
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
        $em = $this->getDoctrine();
        $category = $em->getRepository('DigipRenovationBundle:ConfigCategory')
            ->findOneBy(array('slug' => $categorySlug));

        if (!$category) {
            throw new EntityNotFoundException('no config category found for slug: ' . $categorySlug);
        }

        $category->setPercent($percent);
        $em->persist($category);
        $em->flush();
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
            $this->getDoctrine()->remove($configTransformation);
        } else {
            $this->getDoctrine()->persist($configTransformation);
        }
        $this->getDoctrine()->flush();

        return $this;
    }

    /**
     * removes transformations with the same start and end config
     */
    public function removeDuplicateTransformations()
    {
        $em = $this->getDoctrine();
        $repo = $em->getRepository('DigipRenovationBundle:Config');
        $configs = $repo->findAll();

        foreach ($configs as $c) {
            $transformations = $c->getTransformations();
            $to = [];
            $toInverse = [];
            foreach ($transformations as $t) {
                $conf = $t->getToConfig()->getId();
                if ($t->isInverse()) {
                    if (in_array($conf, $toInverse, true)) {
                        $em->remove($t);
                        continue;
                    }
                    $toInverse[] = $conf;
                } else {
                    if (in_array($conf, $to, true)) {
                        $em->remove($t);
                        continue;
                    }
                    $to[] = $conf;
                }
            }
        }

        $em->flush();
    }
}
