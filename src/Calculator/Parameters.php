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
     * @return float
     */
    public function getPriceGas()
    {
        return $this->priceGas;
    }

    /**
     * @param float $priceGas
     * @return $this
     */
    public function setPriceGas($priceGas)
    {
        $this->priceGas = $priceGas;
        return $this;
    }

    /**
     * @return float
     */
    public function getPriceElec()
    {
        return $this->priceElec;
    }

    /**
     * @param float $priceElec
     * @return $this
     */
    public function setPriceElec($priceElec)
    {
        $this->priceElec = $priceElec;
        return $this;
    }

    /**
     * @return float
     */
    public function getCo2PerKwh()
    {
        return $this->co2PerKwh;
    }

    /**
     * @param float $co2PerKwh
     * @return $this
     */
    public function setCo2PerKwh($co2PerKwh)
    {
        $this->co2PerKwh = $co2PerKwh;
        return $this;
    }

    /**
     * @return int
     */
    public function getSubsidyCeilingRoofGent()
    {
        return $this->subsidyCeilingRoofGent;
    }

    /**
     * @param int $subsidyCeilingRoofGent
     * @return $this
     */
    public function setSubsidyCeilingRoofGent($subsidyCeilingRoofGent)
    {
        $this->subsidyCeilingRoofGent = $subsidyCeilingRoofGent;
        return $this;
    }
}
