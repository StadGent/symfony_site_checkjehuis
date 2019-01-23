<?php

namespace App\Calculator;

use App\Entity\Config;
use App\Entity\ConfigCategory;
use App\Entity\House;

/**
 * Calculates the different between the default configs and the current configs.
 *
 * @package App
 */
class CurrentEnergyCalculator extends EnergyCalculator
{
    /**
     * @var Config[]|array
     */
    protected $defaultConfigs = array();

    /**
     * Get the default configs.
     *
     * @return array|Config[]
     */
    public function getDefaultConfigs()
    {
        return $this->defaultConfigs;
    }

    /**
     * Set the default configs.
     *
     * @param array|Config[] $defaultConfigs
     *   The default configs.
     *
     * @return $this
     */
    public function setDefaultConfigs($defaultConfigs)
    {
        $this->defaultConfigs = $defaultConfigs;
        return $this;
    }

    /**
     * Calculate the state.
     *
     * @param State $state
     *   The state to calculate.
     */
    public function calculate(State $state)
    {
        $this->state = $state;

        if ($this->calculated || $this->house->hasCustomEnergy()) {
            if ($this->house->hasCustomEnergy()) {
                $this->log->add('energie manueel ingegeven');
            }
            return;
        }

        $configs = [];
        $defaultRoofConfig = null;

        foreach ($this->getDefaultConfigs() as $defaultConfig) {
            // Only allow 1 type of heating, elec or gas.
            if ($this->house->hasElectricHeating() && $defaultConfig->isCategory(ConfigCategory::CAT_HEATING)) {
                continue;
            }
            if (!$this->house->hasElectricHeating() && $defaultConfig->isCategory(ConfigCategory::CAT_HEATING_ELEC)) {
                continue;
            }
            if ($defaultConfig->isCategory(ConfigCategory::CAT_ROOF)) {
                $defaultRoofConfig = $defaultConfig;
            }

            $chosenConfig = $this->house->getConfig($defaultConfig->getCategory());
            if ($defaultConfig !== $chosenConfig) {
                $configs[$defaultConfig->getCategory()->getSlug()][100] = [ $defaultConfig, $chosenConfig ];
            }
        }

        $configs = $this->splitTheRoof($configs, $defaultRoofConfig, $this->house->getExtraConfigRoof());

        $this->diff($configs, $this->house->getRenewables());

        $this->calculated = true;
    }
}
