<?php

namespace App\Calculator;

use App\Entity\BuildCost;
use App\Entity\House;
use App\Entity\Parameter;
use App\Entity\Subsidy;
use App\Service\BuildCostService;
use App\Service\HouseService;
use App\Service\ParameterService;
use App\Service\SubsidyService;

class CalculatorFactory
{

    protected $parameterService;
    protected $houseService;
    protected $buildCostService;
    protected $subsidyService;

    function __construct(
        ParameterService $parameterService,
        HouseService $houseService,
        BuildCostService $buildCostService,
        SubsidyService $subsidyService
    ) {
        $this->parameterService = $parameterService;
        $this->houseService = $houseService;
        $this->buildCostService = $buildCostService;
        $this->subsidyService = $subsidyService;
    }

    /**
     * @return Parameters
     */
    public function createParameters()
    {
        return $this->parameterService->getCalculationParameters();
    }

    /**
     * @param House $house
     * @return CurrentEnergyCalculator
     */
    public function createEnergyCalculatorCurrent(House $house)
    {
        $calculator = new CurrentEnergyCalculator(
            $house,
            self::createParameters()
        );

        $calculator->setDefaultConfigs(
            $this->houseService->getDefaultConfigs($house)
        );

        return $calculator;
    }

    /**
     * @param House $house
     * @param State $startEnergy
     * @return UpgradeEnergyCalculator
     */
    public function createEnergyCalculatorUpgrade(House $house)
    {
        $calculator = new UpgradeEnergyCalculator(
            $house,
            self::createParameters()
        );

        return $calculator;
    }

    /**
     * @param House $house
     * @return BuildCostCalculator
     */
    public function createBuildCostCalculator(House $house)
    {
        $calculator = new BuildCostCalculator($house);

        $calculator->setWindRoofCost(
            $this->buildCostService->getCostBySlug(BuildCost::COST_WINDROOF)
        );

        return $calculator;
    }

    /**
     * @param House $house
     * @return SubsidyCalculator
     */
    public function createSubsidyCalculator(House $house)
    {
        $calculator = new SubsidyCalculator($house);

        $calculator->setWindRoofBuildCost(
            $this->buildCostService->getCostBySlug(BuildCost::COST_WINDROOF)
        );
        $calculator->setSolarHeaterBuildCost(
            $this->buildCostService->getCostBySlug(BuildCost::COST_SOLAR_WATER_HEATER)
        );
        $calculator->setWindRoofSubsidies(
            $this->subsidyService->getSubsidiesBySlug(Subsidy::SUBSIDY_WINDROOF)
        );
        $calculator->setSolarHeaterSubsidies(
            $this->subsidyService->getSubsidiesBySlug(Subsidy::SUBSIDY_SOLAR_HEATER)
        );
        $calculator->setSubsidyCeilingGentRoof(
            $this->parameterService->getParameterBySlug(Parameter::PARAM_SUBSIDY_GENT_ROOF)->getValue()
        );

        return $calculator;
    }

    /**
     * @param House $house
     * @param bool $skipUpgrade
     * @return CalculatorView
     */
    public function createCalculatorView(House $house, $skipUpgrade = false)
    {
        $state = State::createFormHouse($house);
        $current = $this->createEnergyCalculatorCurrent($house);
        $current->calculate($state);

        $upgrade = $this->createEnergyCalculatorUpgrade($house);
        $buildCosts = $this->createBuildCostCalculator($house);
        $subsidies = $this->createSubsidyCalculator($house);

        if (!$skipUpgrade) {
            $upgrade->calculate(clone $state);
            $buildCosts->calculate();
            $subsidies->calculate();
        }

        return new CalculatorView($house, $current, $upgrade, $buildCosts, $subsidies, $this->createParameters());
    }
}
