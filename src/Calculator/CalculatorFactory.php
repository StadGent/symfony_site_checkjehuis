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
    /**
     * The parameter service.
     *
     * @var ParameterService
     */
    protected $parameterService;

    /**
     * The house service.
     *
     * @var HouseService
     */
    protected $houseService;

    /**
     * The build cost service.
     *
     * @var BuildCostService
     */
    protected $buildCostService;

    /**
     * The subsidy service.
     *
     * @var SubsidyService
     */
    protected $subsidyService;

    /**
     * Calculator factory constructor.
     *
     * @param ParameterService $parameterService
     *   The parameter service.
     * @param HouseService $houseService
     *   The house service.
     * @param BuildCostService $buildCostService
     *   The build cost service.
     * @param SubsidyService $subsidyService
     *   The subsidy service.
     */
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
     * Create the calculation parameters.
     *
     * @return Parameters
     */
    public function createParameters()
    {
        return $this->parameterService->getCalculationParameters();
    }

    /**
     * Create the calculator for the current state.
     *
     * @param House $house
     *   The house with its current configuration.
     *
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
     * Create the calculator for the upgraded state.
     *
     * @param House $house
     *   The house with its upgraded configuration.
     *
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
     * Create the build cost calculator.
     *
     * @param House $house
     *   The house.
     *
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
     * Create the subsidy calculator.
     *
     * @param House $house
     *   The house.
     *
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
     * Create the calculator view.
     *
     * @param House $house
     *   The house.
     * @param bool $skipUpgrade
     *   Whether or not to skip the upgrade.
     *
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
