<?php

namespace App\Calculator;

use App\Entity\ConfigCategory;
use App\Entity\House;

class State
{
    /**
     * @var float
     */
    protected $gas = 0;

    /**
     * @var float
     */
    protected $electricity = 0;

    /**
     * @var float
     */
    protected $co2 = 0;

    /**
     * @var float
     */
    protected $start = 0;

    /**
     * @var float
     */
    protected $subtotal = 0;

    /**
     * @var float
     */
    protected $baseElectricity = 0;

    /**
     * @var float
     */
    protected $nonHeatingElectricity = 0;

    /**
     * @var float
     */
    protected $electricHeatingEnergy = 0;

    /**
     * @var bool
     */
    protected $isHeatingElectric = false;

    /**
     * @var bool
     */
    protected $forceElectricity = false;

    /**
     * State constructor.
     *
     * @param float $gas
     *   The gas amount.
     * @param float $electricity
     *   The electricity amount.
     * @param float $nonHeatingElectricity
     *   The non heating electricity amount.
     * @param bool $isHeatingElectric
     *   Whether or not electricity is used for heating.
     */
    public function __construct($gas, $electricity, $nonHeatingElectricity, $isHeatingElectric)
    {
        $this->setGas($gas);
        $this->setElectricity($electricity);
        $this->setBaseElectricity($electricity);
        $this->setIsHeatingElectric($isHeatingElectric);
        $this->setNonHeatingElectricity($nonHeatingElectricity);

        $this->init();
    }

    /**
     * Initialize the state.
     *
     * @return $this
     */
    public function init()
    {
        if ($this->isHeatingElectric()) {
            $this->start = $this->electricity - $this->nonHeatingElectricity;
        } else {
            $this->start = $this->gas;
        }

        $this->co2 = 0;
        $this->subtotal = $this->start;

        return $this;
    }

    /**
     * Factory method.
     *
     * @param House $house
     *   The house to create the state from.
     *
     * @return \self
     */
    public static function createFormHouse(House $house)
    {
        $isElectric = false;

        if ($house->hasCustomEnergy()) {
            $gas = $house->getConsumptionGas();
            $elec = $house->getConsumptionElec();
        } else {
            $gas = $house->getDefaultEnergy()->getGas();
            $elec = $house->getDefaultEnergy()->getElectricity($house->hasElectricHeating());
        }

        // We ignore the gas if heating is electric.
        if ($house->hasElectricHeating()) {
            $isElectric = true;
            $gas = 0;
        }

        return new self($gas, $elec, $house->getDefaultEnergy()->getElectricity(), $isElectric);
    }

    /**
     * Get the gas amount.
     *
     * @return float
     */
    public function getGas()
    {
        return $this->gas;
    }

    /**
     * Set the gas amount.
     *
     * @param float $gas
     *   The gas amount.
     *
     * @return $this
     */
    public function setGas($gas)
    {
        $this->gas = $gas;
        return $this;
    }

    /**
     * Get the electricity amount.
     *
     * @return float
     */
    public function getElectricity()
    {
        return $this->electricity;
    }

    /**
     * Set the electricity amount.
     *
     * @param float $electricity
     *   The electricity amount.
     *
     * @return $this
     */
    public function setElectricity($electricity)
    {
        $this->electricity = $electricity;
        return $this;
    }

    /**
     * Get the CO2 amount.
     *
     * @return float
     */
    public function getCo2()
    {
        return $this->co2 * -1;
    }

    /**
     * Set the CO2 amount.
     *
     * @param float $co2
     *   The CO2 amount.
     *
     * @return $this
     */
    public function setCo2($co2)
    {
        $this->co2 = $co2;
        return $this;
    }

    /**
     * Gets the start amount.
     *
     * @return float
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set the start amount.
     *
     * @param float $start
     *   The start amount.
     *
     * @return $this
     */
    public function setStart($start)
    {
        $this->start = $start;
        $this->subtotal = $start;

        return $this;
    }

    /**
     * Get the subtotal amount.
     *
     * @return float
     */
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * Set the subtotal amount.
     *
     * @param float $subtotal
     *   The subtotal amount.
     *
     * @return $this
     */
    public function setSubtotal($subtotal)
    {
        $this->subtotal = $subtotal;
        return $this;
    }

    /**
     * Get the base electricity amount.
     *
     * @return float
     */
    public function getBaseElectricity()
    {
        return $this->baseElectricity;
    }

    /**
     * Set the base electricity amount.
     *
     * @param float $baseElectricity
     *   The base electricity amount.
     *
     * @return $this
     */
    public function setBaseElectricity($baseElectricity)
    {
        $this->baseElectricity = $baseElectricity;
        return $this;
    }

    /**
     * Get the non heating electricity amount.
     *
     * @return float
     */
    public function getNonHeatingElectricity()
    {
        return $this->nonHeatingElectricity;
    }

    /**
     * Set the non heating electricity amount.
     *
     * @param float $nonHeatingElectricity
     *   The non heating electricity amount.
     *
     * @return $this
     */
    public function setNonHeatingElectricity($nonHeatingElectricity)
    {
        $this->nonHeatingElectricity = $nonHeatingElectricity;
        return $this;
    }

    /**
     * Get the electric heating energy.
     *
     * @return float
     */
    public function getElectricHeatingEnergy()
    {
        return $this->electricHeatingEnergy;
    }

    /**
     * Set the electric heating energy.
     *
     * @param float $electricHeatingEnergy
     *   The electric heating energy.
     *
     * @return $this
     */
    public function setElectricHeatingEnergy($electricHeatingEnergy)
    {
        $this->electricHeatingEnergy = $electricHeatingEnergy;
        return $this;
    }

    /**
     * Get whether or not heating is done electric.
     *
     * @return boolean
     */
    public function isHeatingElectric()
    {
        return $this->isHeatingElectric;
    }

    /**
     * Set whether or not heating is electric.
     *
     * @param boolean $isHeatingElectric
     *   Whether or not heating is electric.
     *
     * @return $this
     */
    public function setIsHeatingElectric($isHeatingElectric)
    {
        $this->isHeatingElectric = $isHeatingElectric;
        return $this;
    }

    /**
     * Whether or not electricity is forced.
     *
     * @return boolean
     */
    public function forceElectricity()
    {
        return $this->forceElectricity;
    }

    /**
     * Set electricity forced.
     *
     * @return $this
     */
    public function setForceElectricity()
    {
        if (!$this->forceElectricity) {
            $this->forceElectricity = true;
            $this->electricity += $this->gas;
            $this->gas = 0;
        }

        return $this;
    }

    /**
     * Returns the base figure to use when calculating energy differences for a
     * certain category
     *
     * @param ConfigCategory $category
     *   The category.
     *
     * @return float
     */
    public function getCalculationBaseFormCategory(ConfigCategory $category)
    {
        if (!$category->isFromActual()) {
            return $this->getStart();
        }

        if ($this->isHeatingElectric() || $this->forceElectricity()) {
            return $this->getSubtotal();
        }

        return $this->getGas();
    }

    /**
     * Subtracts energy from the correct type.
     *
     * @param float $amount
     *   The amount to subtract.
     * @param bool $fromActual
     *   Whether or not to substract form the actual amount.
     * @param bool $forceElec
     *   Wether or not heating electric is forced.
     *
     * @return $this
     */
    public function subtractEnergy($amount, $fromActual, $forceElec = false)
    {
        if (($forceElec || $this->forceElectricity) || $this->isHeatingElectric()) {
            $this->electricity -= $amount;
        } else {
            $this->gas -= $amount;
        }

        if (!$fromActual) {
            $this->subtotal -= $amount;
        }

        /*
         * Can't go below zero.
         */

        if ($this->electricity < 0) {
            $this->electricity = 0;
        }
        if ($this->gas < 0) {
            $this->gas = 0;
        }
        if ($this->subtotal < 0) {
            $this->subtotal = 0;
        }

        return $this;
    }

    /**
     * Subtracts CO2.
     *
     * @param float $amount
     *   The amount to subtract.
     *
     * @return $this
     */
    public function subtractCo2($amount)
    {
        $this->co2 -= $amount;

        return $this;
    }

    /**
     * Clone the state.
     */
    public function __clone()
    {
        $this->init();
    }
}
