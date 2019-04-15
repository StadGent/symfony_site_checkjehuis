<?php

namespace App\Calculator;

use App\Entity\ConfigCategory;
use App\Entity\Renewable;

class Diff
{
    /**
     * @var ConfigCategory|Renewable
     */
    protected $subject;

    /**
     * @var float
     */
    protected $gas;

    /**
     * @var float
     */
    protected $elec;

    /**
     * @var float
     */
    protected $co2;

    /**
     * Diff constructor.
     *
     * @param $subject
     */
    public function __construct($subject)
    {
        $this->subject = $subject;
    }

    /**
     * The start state.
     *
     * @param State $state
     */
    public function start(State $state)
    {
        $this->gas  = $state->getGas();
        $this->elec = $state->getElectricity();
        $this->co2  = $state->getCo2();
    }

    /**
     * The end state.
     *
     * @param State $state
     */
    public function end(State $state)
    {
        $this->gas  -= $state->getGas();
        $this->elec -= $state->getElectricity();
        $this->co2  -= $state->getCo2();
    }

    /**
     * Get the subject of this diff.
     *
     * @return ConfigCategory|Renewable
     */
    public function getSubject()
    {
        return $this->subject;
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
     * Get the electricity amount.
     *
     * @return float
     */
    public function getElec()
    {
        return $this->elec;
    }

    /**
     * Get the total.
     *
     * @return float
     */
    public function getTotal()
    {
        return $this->getGas() + $this->getElec();
    }

    /**
     * Get the CO2 amount.
     *
     * @return float
     */
    public function getCo2()
    {
        return $this->co2;
    }
}
