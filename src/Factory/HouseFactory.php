<?php

namespace App\Factory;

use App\Entity\ConfigCategory;
use App\Entity\House;
use App\Entity\Parameter;
use App\Service\HouseService;
use App\Service\ParameterService;

class HouseFactory
{
    protected $houseService;
    protected $parameterService;

    public function __construct(HouseService $houseService, ParameterService $parameterService)
    {
        $this->houseService = $houseService;
        $this->parameterService = $parameterService;
    }

    public function create()
    {
        $house = new House();
        $house->setConfigs($this->houseService->getDefaultConfigs($house));
        $house->setExtraConfigRoof($house->getConfig(ConfigCategory::CAT_ROOF));
        $house->setDefaultSurfaces(
            $this->houseService->getDefaultSurface($house),
            $this->houseService->getDefaultRoof($house)
        );
        $house->setDefaultRoofIfFlat($this->houseService->getDefaultRoofIfFlat($house));
        $house->setDefaultEnergy($this->houseService->getDefaultEnergy($house));
        $house->setSolarPanelsSurface(
            $this->parameterService->getParameterBySlug(Parameter::PARAM_SOLAR_SURFACE)->getValue()
        );

        return $house;
    }
}
