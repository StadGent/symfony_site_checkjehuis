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
     * @param $subject
     */
    public function __construct($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @param State $state
     */
    public function start(State $state)
    {
        $this->gas  = $state->getGas();
        $this->elec = $state->getElectricity();
        $this->co2  = $state->getCo2();
    }

    /**
     * @param State $state
     */
    public function end(State $state)
    {
        $this->gas  -= $state->getGas();
        $this->elec -= $state->getElectricity();
        $this->co2  -= $state->getCo2();
    }

    /**
     * @return ConfigCategory|Renewable
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return float
     */
    public function getGas()
    {
        return $this->gas;
    }

    /**
     * @return float
     */
    public function getElec()
    {
        return $this->elec;
    }

    /**
     * @return float
     */
    public function getTotal()
    {
        return $this->getGas() + $this->getElec();
    }

    /**
     * @return float
     */
    public function getCo2()
    {
        return $this->co2;
    }
}
