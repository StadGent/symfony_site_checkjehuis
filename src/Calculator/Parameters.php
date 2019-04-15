<?php

namespace App\Calculator;

class Parameters
{
    /**
     * Price of gas.
     *
     * @var float
     */
    protected $priceGas;

    /**
     * Price of electricity.
     *
     * @var float
     */
    protected $priceElec;

    /**
     * How much CO2 one KwH of energy produces.
     *
     * @var float
     */
    protected $co2PerKwh;

    /**
     * Get the price of gas.
     *
     * @return float
     */
    public function getPriceGas()
    {
        return $this->priceGas;
    }

    /**
     * Set the price of gas.
     *
     * @param float $priceGas
     *   The price of gas.
     *
     * @return $this
     */
    public function setPriceGas($priceGas)
    {
        $this->priceGas = $priceGas;
        return $this;
    }

    /**
     * Get the price of electricity.
     *
     * @return float
     */
    public function getPriceElec()
    {
        return $this->priceElec;
    }

    /**
     * Set the price of electricity.
     *
     * @param float $priceElec
     *   The price of electricity.
     *
     * @return $this
     */
    public function setPriceElec($priceElec)
    {
        $this->priceElec = $priceElec;
        return $this;
    }

    /**
     * Get the CO2 per Kwh.
     *
     * @return float
     */
    public function getCo2PerKwh()
    {
        return $this->co2PerKwh;
    }

    /**
     * Set the CO2 per Kwh.
     *
     * @param float $co2PerKwh
     *   The CO2 per Kwh.
     *
     * @return $this
     */
    public function setCo2PerKwh($co2PerKwh)
    {
        $this->co2PerKwh = $co2PerKwh;
        return $this;
    }

    /**
     * Get the subsidy ceiling for roofs in Ghent.
     *
     * @return int
     */
    public function getSubsidyCeilingRoofGent()
    {
        return $this->subsidyCeilingRoofGent;
    }

    /**
     * Set the subsidy ceiling for roofs in Ghent.
     *
     * @param int $subsidyCeilingRoofGent
     *   The subsidy ceiling for roofs in Ghent.
     *
     * @return $this
     */
    public function setSubsidyCeilingRoofGent($subsidyCeilingRoofGent)
    {
        $this->subsidyCeilingRoofGent = $subsidyCeilingRoofGent;
        return $this;
    }
}
