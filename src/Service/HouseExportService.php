<?php

namespace App\Service;

use App\Calculator\CalculatorFactory;
use App\Entity\ConfigCategory;
use App\Entity\House;
use App\Entity\Renewable;

class HouseExportService
{

    protected $buildingTypes;
    protected $roofTypes;
    protected $houseSizes;
    protected $houseOwnerships;
    protected $years;
    protected $catRoof;
    protected $catFacade;
    protected $catFloor;
    protected $catWindows;
    protected $catVent;
    protected $catHeating;
    protected $catHeatingElec;
    protected $solarWater;
    protected $solarPanels;
    protected $greenPower;

    /**
     * The calculator factory.
     *
     * @var CalculatorFactory
     */
    protected $calculatorFactory;

    /**
     * Service constructor.
     *
     * @param ConfigService $configService
     *   The config service.
     * @param RenewablesService $renewablesService
     *   The renewables service.
     * @param CalculatorFactory $calculatorFactory
     *   The calculator factory.
     */
    public function __construct(ConfigService $configService, RenewablesService $renewablesService, CalculatorFactory $calculatorFactory)
    {
        $this->buildingTypes = House::getBuildingTypes();
        $this->roofTypes = House::getRoofTypes();
        $this->houseSizes = House::getSizes();
        $this->houseOwnerships = House::getOwnerships();
        $this->years = House::getYears();

        $this->catRoof = $configService->getCategoryBySlug(ConfigCategory::CAT_ROOF);
        $this->catFacade = $configService->getCategoryBySlug(ConfigCategory::CAT_FACADE);
        $this->catFloor = $configService->getCategoryBySlug(ConfigCategory::CAT_FLOOR);
        $this->catWindows = $configService->getCategoryBySlug(ConfigCategory::CAT_WINDOWS);
        $this->catVent = $configService->getCategoryBySlug(ConfigCategory::CAT_VENTILATION);
        $this->catHeating = $configService->getCategoryBySlug(ConfigCategory::CAT_HEATING);
        $this->catHeatingElec = $configService->getCategoryBySlug(ConfigCategory::CAT_HEATING_ELEC);

        $this->solarWater = $renewablesService->getRenewableBySlug(Renewable::RENEWABLE_SOLAR_WATER_HEATER);
        $this->solarPanels = $renewablesService->getRenewableBySlug(Renewable::RENEWABLE_SOLAR_PANELS);
        $this->greenPower = $renewablesService->getRenewableBySlug(Renewable::RENEWABLE_GREEN_POWER);

        $this->calculatorFactory = $calculatorFactory;
    }

    /**
     * Get the csv for a list of houses.
     *
     * @param House[] $houses
     *   The list of houses.
     *
     * @return string
     *   The resulting csv.
     */
    public function getCsv(iterable $houses)
    {
        $path = 'php://memory';
        $csv = fopen($path, "rw+");
        fputcsv($csv, $this->csvHeader());

        foreach ($houses as $house) {
            fputcsv($csv, $this->csvRow($house));
        }

        fseek($csv, 0);
        $contents = stream_get_contents($csv);
        fclose($csv);

        return $contents;
    }

    /**
     * Get the csv header as array.
     *
     * @return array
     *   The csv header as array.
     */
    protected function csvHeader()
    {
        return [
            'id', 'token', 'laatste update', 'email', 'nieuwsbrief', 'adres',
            'type gebouw', 'bouwjaar', 'type dak', 'grootte', 'BVO', 'eigenaar',
            'bewoners', 'verbruik gas', 'verbruik elektriciteit', 'verwarming electrisch',
            'dakisolatie', 'dakisolatie 2', 'winddicht onderdak', 'gevelisolatie',
            'vloerisolatie', 'ramen', 'ventilatie', 'verwarming', 'zonneboiler',
            'PV cellen', 'PV cellen m²', 'groene stroom', 'gewenste dakisolatie',
            'winddicht onderdak gewenst', 'opp. dakisolatie', 'gewenste dakisolatie 2',
            'opp. dakisolatie plat', 'gewenste gevelisolatie', 'opp. gevelisolatie',
            'gewenste vloerisolatie', 'opp. vloerisolatie', 'gewenste ramen',
            'opp. ramen vernieuwen', 'gewenste ventilatie', 'gewenste verwarming',
            'gewenste zonneboiler', 'gewenste PV cellen', 'gewenste groene stroom',
            'huidige verbruik gas', 'huidige verbruik electriciteit',
            'huidige verbruik per m²', 'verbruik gas na renovatie',
            'verbruik electriciteit na renovatie', 'verbruik na renovatie per m²',
        ];
    }

    /**
     * Get the csv row for a house as array.
     *
     * @param House $house
     *   The house to get the row for.
     *
     * @return array
     *   The csv row as array.
     */
    protected function csvRow(House $house) {
        $view = $this->calculatorFactory->createCalculatorView($house);

        return [
            $house->getId(), $house->getToken(),
            $house->getLastUpdate()->format('Y-m-d H:i:s'),
            $house->getEmail(),
            $house->getNewsletter() ? 1: 0,
            $house->getAddress(),
            $this->buildingTypes[$house->getBuildingType()],
            $this->years[$house->getYear()],
            $this->roofTypes[$house->getRoofType()],
            $this->houseSizes[$house->getSize()],
            $house->getSurfaceLivingArea(),
            $this->houseOwnerships[$house->getOwnership()],
            $house->getOccupants(),
            $house->getConsumptionGas(),
            $house->getConsumptionElec(),
            $house->hasElectricHeating() ? 1: 0,
            $house->getConfig($this->catRoof)->getLabel(),
            $house->getExtraConfigRoof() ? $house->getExtraConfigRoof()->getLabel(): '',
            $house->hasWindRoof() ? 1: 0,
            $house->getConfig($this->catFacade)->getLabel(),
            $house->getConfig($this->catFloor)->getLabel(),
            $house->getConfig($this->catWindows)->getLabel(),
            $house->getConfig($this->catVent)->getLabel(),
            $house->hasElectricHeating() ?
                $house->getConfig($this->catHeatingElec)->getLabel():
                $house->getConfig($this->catHeating)->getLabel(),
            $house->hasRenewable($this->solarWater) ? 1: 0,
            $house->hasRenewable($this->solarPanels) ? 1: 0,
            $house->getSolarPanelsSurface($this->solarPanels),
            $house->hasRenewable($this->greenPower) ? 1: 0,
            $house->getUpgradeConfig($this->catRoof) ? $house->getUpgradeConfig($this->catRoof)->getLabel(): '',
            $house->getSurfaceRoof(),
            $house->getExtraUpgradeRoof() ? $house->getExtraUpgradeRoof()->getLabel(): '',
            $house->getSurfaceRoofExtra(),
            $house->getPlaceWindroof() ? 1: 0,
            $this->getUpgradeConfigLabel($house, $this->catFacade),
            $house->getSurfaceFacade(),
            $this->getUpgradeConfigLabel($house, $this->catFloor),
            $house->getSurfaceFloor(),
            $this->getUpgradeConfigLabel($house, $this->catWindows),
            $house->getSurfaceWindow(),
            $this->getUpgradeConfigLabel($house, $this->catVent),
            $house->hasElectricHeating() ?
                $this->getUpgradeConfigLabel($house, $this->catHeatingElec):
                $this->getUpgradeConfigLabel($house, $this->catHeating),
            $house->hasUpgradeRenewable($this->solarWater) ? 1: 0,
            $house->hasUpgradeRenewable($this->solarPanels) ? 1: 0,
            $house->hasUpgradeRenewable($this->greenPower) ? 1: 0,
            $view->getCurrent()->getState()->getGas(),
            $view->getCurrent()->getState()->getElectricity(),
            $view->getAvgScore(true),
            $view->getUpgrade()->getState()->getGas(),
            $view->getUpgrade()->getState()->getElectricity(),
            $view->getAvgScore(),
        ];
    }

    /**
     * Get the upgrade config category label for an upgrade category for a house.
     *
     * @param House $house
     *   The house to get the config category label for.
     * @param ConfigCategory|string $category
     *   The config category to get the label for.
     *
     * @return string
     *   The label.
     */
    protected function getUpgradeConfigLabel(House $house, $category)
    {
        if ($house->getUpgradeConfig($category)) {
            return $house->getUpgradeConfig($category)->getLabel();
        }

        return $house->getConfig($category)->getLabel();
    }
}
