<?php

namespace Digip\RenovationBundle\Calculator;

use Digip\RenovationBundle\Entity\Config;
use Digip\RenovationBundle\Entity\ConfigCategory;
use Digip\RenovationBundle\Entity\House;
use Digip\RenovationBundle\Entity\Renewable;

abstract class EnergyCalculator
{
    /**
     * @var House
     */
    protected $house;

    /**
     * @var Parameters
     */
    protected $parameters;

    /**
     * @var bool
     */
    protected $calculated = false;

    /**
     * @var State
     */
    protected $state;

    /**
     * @var Log
     */
    protected $log;

    public function __construct(House $house, Parameters $parameters)
    {
        $this->house        = $house;
        $this->parameters   = $parameters;
        $this->log = new Log();
    }

    /**
     * @return boolean
     */
    public function isCalculated()
    {
        return $this->calculated;
    }

    /**
     * @return State
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Performs the transformation from one config to another
     *
     * @param Config $from
     * @param Config|null $to
     * @param int $percent
     * @param bool $forceElectricity
     * @return float|int
     */
    public function transform(Config $from, Config $to = null, $percent = 100, $forceElectricity = false)
    {
        $cat            = $from->getCategory();
        $fromActual     = $cat->isFromActual();
        $base           = $this->state->getCalculationBaseFormCategory($cat);

        if ($to) {

            $this->log->add(sprintf(
                '%s op basis van %s%% van %s',
                $cat->getLabel(),
                $cat->getPercent(),
                $fromActual ? 'subtotaal': 'startverbruik'
            ));
            if ($percent !== 100) {
                $this->log->add(sprintf('rekenbasis gereduceert tot %s%%', $percent));
            }

            $transformation = $from->getTransformationFor($to);

            if ($transformation) {

                // energy
                $diff = $transformation->getDiff($base, $formula);
                $diff = $diff * ($percent / 100);
                $this->log->add($formula);

                // if we have a category for which the user can enter surfaces
                // adapt the diff relative to the default surface
                if (in_array($to->getCategory()->getSlug(), array(
                    ConfigCategory::CAT_ROOF,
                    ConfigCategory::CAT_FACADE,
                    ConfigCategory::CAT_FLOOR,
                    ConfigCategory::CAT_WINDOWS,
                ), true)) {
                    $surfaceDiff = $this->house->getSurfaceDiffPercentage($to->getCategory()->getSlug(), $percent);
                    if ($surfaceDiff !== 1) {
                        $diff = $diff * $surfaceDiff;
                        $this->log->add(sprintf('oppervlakte correctie van %s%%', $surfaceDiff * 100));
                    }
                }

                $this->log->add(sprintf('= %s besparing', $diff));

                // subtract energy
                $this->state->subtractEnergy($diff, $fromActual);

                // CO2
                $co2Diff = $diff * $this->parameters->getCo2PerKwh();
                $this->state->subtractCo2($co2Diff);
            }

            // extra electricity cost

            if ($cat->hasInverseMatrix()) {

                $this->log->add('berekening extra kost');

                $diffInverse = 0;
                $transformation = $from->getTransformationFor($to, true);
                if ($transformation) {
                    $diffInverse = $transformation->getDiff($this->state->getBaseElectricity(), $formula);
                    $diffInverse = $diffInverse * ($percent / 100);
                    $this->log->add($formula);
                }

                $this->state->subtractEnergy($diffInverse, $fromActual, true);

                // CO2
                $co2DiffInverse = $diffInverse * $this->parameters->getCo2PerKwh();
                $this->state->subtractCo2($co2DiffInverse);

                $this->log->add(sprintf('= %s besparing', $diffInverse));
            }

        }

        if ($forceElectricity) {
            $this->log->add(sprintf('move to electricity'));
            $this->log->add(sprintf('%s gas > elec', $this->state->getGas()));
            $this->state->setForceElectricity();
            $this->log->add(sprintf('elec = %s', $this->state->getElectricity()));
        }
    }

    /**
     * @param State $state
     */
    public function calculate(State $state)
    {
        if ($this->calculated) {
            return;
        }

        $this->state = $state;
        $this->calculated = true;
    }

    /**
     * @param array|Config[][][] $configs
     * @param array|Renewable[] $renewables
     */
    public function diff($configs, $renewables)
    {
        // solar water heater affects the base values, not the actual. so process them first
        foreach ($renewables as $r) {
            if ($r->getSlug() === Renewable::RENEWABLE_SOLAR_WATER_HEATER) {
                $this->subtractRenewable($r);
                break;
            }
        }

        // all the others
        $didSolarPanels = false;
        $heatCategory = $this->house->hasElectricHeating() ? ConfigCategory::CAT_HEATING_ELEC: ConfigCategory::CAT_HEATING;
        $hasHeatPump = false;
        $heatConfigs = [];

        // check if we're installing a heat pump
        if (array_key_exists($heatCategory, $configs)) {
            $heatConfigs = $configs[$heatCategory][100];
            $hasHeatPump = $heatConfigs[1]->isHeatPumpConfig();
        }

        /**
         * @var ConfigCategory $cat
         */
        foreach ($configs as $cat => $configSplit) {
            // special heat pump cases
            if ($hasHeatPump && $cat === $heatCategory) {

                // if we're going to a heat pump, remove gas and force everything to elec
                if ($hasHeatPump) {
                    $this->log->add('warmtepomp: gas verwijderen en verder gaan op elec');

                    // force all to electricity AFTER transforming everything from the heating matrices
                    $this->transform($heatConfigs[0], $heatConfigs[1], 100, true);

                    // remove heating config so it doesn't get processed again
                    unset($configs[$heatCategory]);
                }

            } else {
                foreach ($configSplit as $percentage => $configCouple) {
                    if (count($configCouple) === 2) {
                        $this->transform($configCouple[0], $configCouple[1], $percentage);
                    }
                }
            }
        }

        // other renewable energy

        foreach ($renewables as $r) {
            if ($r->getSlug() === Renewable::RENEWABLE_SOLAR_WATER_HEATER) continue; // already added
            if ($didSolarPanels && $r->getSlug() === Renewable::RENEWABLE_SOLAR_PANELS) continue; // already added

            $this->subtractRenewable($r);
        }
    }

    public function subtractRenewable(Renewable $renewable)
    {
        $fromActual = true;
        $forceElectric = true;
        $greenPower = false;
        $amount = $renewable->getValue();

        switch ($renewable->getSlug()) {
            case Renewable::RENEWABLE_SOLAR_WATER_HEATER:
                $amount = $amount * $this->house->getOccupants();
                $fromActual = false;
                $forceElectric = false;
                break;
            case Renewable::RENEWABLE_SOLAR_PANELS:
                $amount = $amount * $this->house->getSolarPanelsSurface();
                break;
            case Renewable::RENEWABLE_GREEN_POWER:
                $greenPower = true;
                break;
        }

        $forceElectric = $forceElectric || $this->state->forceElectricity();
        $co2 = $amount * $this->parameters->getCo2PerKwh();

        $this->state->subtractEnergy($amount, $fromActual, $forceElectric);

        // green power removes CO2 for all electricity
        if ($greenPower) {
            $co2 += $this->state->getElectricity() * $this->parameters->getCo2PerKwh();
        }

        $this->state->subtractCo2($co2);
    }

    /**
     * @param array|Config[][][] $configs
     * @param Config $extraFrom
     * @param Config $extraTo
     * @return array|\Digip\RenovationBundle\Entity\Config[][][]
     */
    protected function splitTheRoof(array $configs = array(), Config $extraFrom = null, Config $extraTo = null)
    {
        // if we have a mixed roof, split the configs 70/30
        if ($this->house->getRoofType() === House::ROOF_TYPE_MIXED) {

            if (isset($configs[ConfigCategory::CAT_ROOF])) {
                $roofMain = $configs[ConfigCategory::CAT_ROOF][100];
                unset($configs[ConfigCategory::CAT_ROOF][100]);
                if ($roofMain[1] !== null && $roofMain[0] !== $roofMain[1]) {
                    $configs[ConfigCategory::CAT_ROOF][70] = $roofMain;
                }
            }

            if ($extraTo && $extraFrom !== $extraTo) {
                $configs[ConfigCategory::CAT_ROOF][30] = [ $extraFrom, $extraTo ];
            }
        }

        return $configs;
    }

    /**
     * @return Log
     */
    public function getLog()
    {
        return $this->log;
    }
}
