<?php

namespace App\Calculator;

use App\Entity\BuildCost;
use App\Entity\Config;
use App\Entity\ConfigCategory;
use App\Entity\House;
use App\Entity\Renewable;
use App\Entity\Subsidy;

class SubsidyCalculator
{
    /**
     * @var House
     */
    protected $house;

    /**
     * @var array|Subsidy[]
     */
    protected $windRoofSubsidies;

    /**
     * @var BuildCost
     */
    protected $windRoofBuildCost;

    /**
     * @var array|Subsidy[]
     */
    protected $solarHeaterSubsidies;

    /**
     * @var BuildCost
     */
    protected $solarHeaterBuildCost;

    /**
     * @var array
     */
    protected $categories = array();

    /**
     * @var array
     */
    protected $renewables = array();

    /**
     * @var array|float[]
     */
    protected $windRoofPrice = 0;

    /**
     * @var float
     */
    protected $totalPrice = 0;

    /**
     * The max amount of roof insulation subsidies Stad Gent will hand out
     *
     * @var float
     */
    protected $subsidyCeilingGentRoof = 0;

    /**
     * @var Subsidy[]
     */
    protected $subsidies;

    public function __construct(House $house)
    {
        $this->house = $house;
    }

    /**
     * @param array|\App\Entity\Subsidy[] $windRoofSubsidies
     * @return $this
     */
    public function setWindRoofSubsidies($windRoofSubsidies)
    {
        $this->windRoofSubsidies = $windRoofSubsidies;
        return $this;
    }

    /**
     * @param BuildCost $windRoofBuildCost
     * @return $this
     */
    public function setWindRoofBuildCost($windRoofBuildCost)
    {
        $this->windRoofBuildCost = $windRoofBuildCost;
        return $this;
    }

    /**
     * @param array|\App\Entity\Subsidy[] $solarHeaterSubsidies
     * @return $this
     */
    public function setSolarHeaterSubsidies($solarHeaterSubsidies)
    {
        $this->solarHeaterSubsidies = $solarHeaterSubsidies;
        return $this;
    }

    /**
     * @param BuildCost $solarHeaterBuildCost
     * @return $this
     */
    public function setSolarHeaterBuildCost($solarHeaterBuildCost)
    {
        $this->solarHeaterBuildCost = $solarHeaterBuildCost;
        return $this;
    }

    /**
     * @param float $subsidyCeilingGentRoof
     * @return $this
     */
    public function setSubsidyCeilingGentRoof($subsidyCeilingGentRoof)
    {
        $this->subsidyCeilingGentRoof = $subsidyCeilingGentRoof;
        return $this;
    }

    /**
     * @return array
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @return array
     */
    public function getRenewables()
    {
        return $this->renewables;
    }

    /**
     * @return array|\float[]
     */
    public function getWindRoofPrice()
    {
        return $this->windRoofPrice;
    }

    /**
     * @return float
     */
    public function getTotalPrice()
    {
        return $this->totalPrice;
    }

    public function calculate()
    {
        // All roof related subsidies combined can not go over the max of
        // $this->subsidyCeilingGentRoof.
        $roofRelated = 0;
        $roofSubsidies = array(
            'category' => array(),
            ConfigCategory::CAT_WIND_ROOF => array(),
        );
        $roofCat = null;

        // Configs.
        foreach ($this->house->getUpgradeConfigs() as $c) {

            // Renters only get subsidies for roof insulation.
            if ($this->house->getOwnership() === House::OWNERSHIP_RENTER
                && $c->getCategory()->getSlug() !== ConfigCategory::CAT_ROOF) {
                continue;
            }

            foreach ($c->getSubsidies() as $s) {

                // Stad Gent roof subsidies only count if a wind roof is placed,
                // except for a 100% flat roof (no wind roof possible in that
                // case), or if you place the isolation on the floor... which is
                // a "generic" config choice, so hardcoded on ID.
                if ($this->house->getRoofType() !== House::ROOF_TYPE_FLAT
                    && !$this->house->getPlaceWindroof() && !$this->house->hasWindRoof()
                    && $c->getId() !== Config::CONFIG_ATTIC_FLOOR
                    && $s->getCategory()->getId() === 1 && $c->getCategory()->getSlug() === ConfigCategory::CAT_ROOF
                ) {
                    continue;
                }

                $price = $s->getPrice($this->house, $c->getCost(), array('roof-type' => House::ROOF_TYPE_INCLINED));

                // Check for stad Gent roof related max roof subsidy param.
                if ($s->isRoofRelated() && $s->getCategory()->getId() === 1) {
                    $roofCat = $c->getCategory();
                    $roofSubsidies['category'][] = array(
                        'subsidy'   => $s,
                        'price'     => $price
                    );
                    $roofRelated += $price;
                }

                $this->totalPrice += $price;
                $this->add($this->categories[$c->getCategory()->getSlug()][$s->getCategory()->getId()], $price);
            }

        }

        // Add flat part of mixed roof.
        if ($this->house->getRoofType() === House::ROOF_TYPE_MIXED && $this->house->getExtraUpgradeRoof()) {

            $c = $this->house->getExtraUpgradeRoof();
            foreach ($c->getSubsidies() as $s) {

                $price = $s->getPrice($this->house, $c->getCost(), array('roof-type' => House::ROOF_TYPE_FLAT));

                // check for stad gent roof related max roof subsidy param
                if ($s->getCategory()->getId() === 1) {
                    $roofCat = $c->getCategory();
                    $roofSubsidies['category'][] = array(
                        'subsidy'   => $s,
                        'price'     => $price
                    );
                    $roofRelated += $price;
                }

                $this->totalPrice += $price;
                $this->add($this->categories[$c->getCategory()->getSlug()][$s->getCategory()->getId()], $price);
            }

        }

        // Add subsidy for placing windroof.
        if (!$this->house->hasWindRoof() && $this->house->getPlaceWindroof() && $this->house->getRoofType() !== House::ROOF_TYPE_FLAT) {
            foreach ($this->windRoofSubsidies as $s) {
                $price = $s->getPrice($this->house, $this->windRoofBuildCost, array('roof-type' => House::ROOF_TYPE_INCLINED));

                // Check for stad gent roof related max roof subsidy param.
                if ($s->getCategory()->getId() === 1) {
                    $roofSubsidies[ConfigCategory::CAT_WIND_ROOF][] = array(
                        'subsidy'   => $s,
                        'price'     => $price
                    );
                    $roofRelated += $price;
                }

                $this->totalPrice += $price;
                $this->add($this->categories[ConfigCategory::CAT_WIND_ROOF][$s->getCategory()->getId()], $price);
            }
        }

        // If we have roof related stuff, check if we haven't gone over the
        // limit.
        if ($roofRelated) {
            $this->assertRoofSubsidyLimits($roofRelated, $roofSubsidies, $roofCat);
        }

        // Nothing more for renters.
        if ($this->house->getOwnership() === House::OWNERSHIP_RENTER)  {
            return;
        }

        // Add subsidy for solar boiler.
        foreach ($this->house->getUpgradeRenewables() as $r) {
            if ($r->getSlug() === Renewable::RENEWABLE_SOLAR_WATER_HEATER) {
                foreach ($this->solarHeaterSubsidies as $s) {
                    $price = $s->getPrice($this->house, $this->solarHeaterBuildCost);
                    $this->totalPrice += $price;
                    $this->add($this->renewables[$r->getSlug()][$s->getCategory()->getId()], $price);
                }
            }
        }
    }

    /**
     * @param float $total
     * @param array $subsidies
     * @param ConfigCategory $roofConfigCategory
     */
    protected function assertRoofSubsidyLimits($total, $subsidies, $roofConfigCategory)
    {
        $count = count($subsidies['category']) + count($subsidies[ConfigCategory::CAT_WIND_ROOF]);

        if ($count && $total > $this->subsidyCeilingGentRoof) {
            // Subtract the subsidies given over the limit.
            $this->totalPrice -= $total - $this->subsidyCeilingGentRoof;

            // Update the details, spread subsidies pro-rata over the active
            // parts.
            foreach ($subsidies['category'] as $k => $val) {
                $price = $val['price'] * $this->subsidyCeilingGentRoof / $total;
                // On the first loop, reset the existing value.
                if ($k === 0) {
                    $this->categories[$roofConfigCategory->getSlug()][$val['subsidy']->getCategory()->getId()] = $price;
                } else {
                    $this->categories[$roofConfigCategory->getSlug()][$val['subsidy']->getCategory()->getId()] += $price;
                }
            }
            foreach ($subsidies[ConfigCategory::CAT_WIND_ROOF] as $val) {
                $this->categories[ConfigCategory::CAT_WIND_ROOF][$val['subsidy']->getCategory()->getId()] = $val['price'] * $this->subsidyCeilingGentRoof / $total;
            }
        }
    }

    protected function add(&$var, $val)
    {
        if (!isset($var)) {
            $var = $val;
        } else {
            $var += $val;
        }
    }
}
