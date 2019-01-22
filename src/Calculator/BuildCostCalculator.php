<?php

namespace App\Calculator;

use App\Entity\BuildCost;
use App\Entity\ConfigCategory;
use App\Entity\House;

class BuildCostCalculator
{
    /**
     * @var House
     */
    protected $house;

    /**
     * @var BuildCost
     */
    protected $windRoofCost;

    /**
     * @var array|float[]
     */
    protected $categories = array();

    /**
     * @var array|float[]
     */
    protected $renewables = array();

    /**
     * @var float
     */
    protected $totalPrice = 0;

    public function __construct(House $house)
    {
        $this->house = $house;
    }

    /**
     * @return BuildCost
     */
    public function getWindRoofCost()
    {
        return $this->windRoofCost;
    }

    /**
     * @param BuildCost $windRoofCost
     * @return $this
     */
    public function setWindRoofCost($windRoofCost)
    {
        $this->windRoofCost = $windRoofCost;
        return $this;
    }

    /**
     * @return array|\float[]
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @return array|\float[]
     */
    public function getRenewables()
    {
        return $this->renewables;
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
        // Configurations.
        foreach ($this->house->getUpgradeConfigs() as $c) {
            if ($c->getCost()) {
                $price = $c->getCost()->getPrice($this->house, array(
                    'roof-type' => House::ROOF_TYPE_INCLINED
                ));
                $this->totalPrice += $price;
                $this->categories[$c->getCategory()->getSlug()] = $price;
            }
        }

        if ($this->house->getExtraUpgradeRoof()) {
            $c = $this->house->getExtraUpgradeRoof();
            if ($c->getCost()) {
                $price = $c->getCost()->getPrice($this->house, array(
                    'roof-type' => House::ROOF_TYPE_FLAT
                ));
                $this->totalPrice += $price;
                if (!isset($this->categories[$c->getCategory()->getSlug()])) {
                    $this->categories[$c->getCategory()->getSlug()] = 0;
                }
                $this->categories[$c->getCategory()->getSlug()] += $price;
            }
        }

        // Add cost of placing windroof.
        if ($this->windRoofCost && !$this->house->hasWindRoof() && $this->house->getPlaceWindroof() && $this->house->getRoofType() !== House::ROOF_TYPE_FLAT) {
            $price = $this->windRoofCost->getPrice($this->house, array('roof-type' => House::ROOF_TYPE_INCLINED));
            $this->totalPrice += $price;
            if (!isset($this->categories[ConfigCategory::CAT_ROOF])) {
                $this->categories[ConfigCategory::CAT_ROOF] = 0;
            }
            $this->categories[ConfigCategory::CAT_ROOF] += $price;
        }

        // Renewables.
        foreach ($this->house->getUpgradeRenewables() as $r) {
            if ($r->getCost()) {
                $price = $r->getCost()->getPrice($this->house);
                $this->totalPrice += $price;
                $this->renewables[$r->getSlug()] = $price;
            }
        }
    }
}
