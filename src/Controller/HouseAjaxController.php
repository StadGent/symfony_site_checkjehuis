<?php

namespace App\Controller;

use App\Calculator\CalculatorFactory;
use App\Calculator\CalculatorView;
use App\Entity\Config;
use App\Entity\ConfigCategory;
use App\Entity\House;
use App\Factory\HouseFactory;
use App\Service\ConfigService;
use App\Service\ContentService;
use App\Service\HouseService;
use App\Service\ParameterService;
use App\Service\RenewablesService;
use App\Utility\Format;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class HouseAjaxController extends AbstractController
{
    /**
     * The calculator view.
     *
     * @var CalculatorView
     */
    protected $calculatorView;

    /**
     * The config service.
     *
     * @var ConfigService
     */
    protected $configService;

    /**
     * The renewables service.
     *
     * @var RenewablesService
     */
    protected $renewablesService;

    /**
     * The calculator factory.
     *
     * @var CalculatorFactory
     */
    protected $calculatorFactory;

    /**
     * Controller constructor.
     *
     * @param HouseService $houseService
     *   The house service.
     * @param ContentService $contentService
     *   The content service.
     * @param ParameterService $parameterService
     *   The parameter service.
     * @param HouseFactory $houseFactory
     *   The house factory.
     * @param ConfigService $configService
     *   The config service.
     * @param RenewablesService $renewableService
     *   The renewables service.
     * @param CalculatorFactory $calculatorFactory
     *   The calculator factory.
     */
    public function __construct(
        HouseService $houseService,
        ContentService $contentService,
        ParameterService $parameterService,
        HouseFactory $houseFactory,
        ConfigService $configService,
        RenewablesService $renewableService,
        CalculatorFactory $calculatorFactory
    ) {
        parent::__construct($houseService, $contentService, $parameterService, $houseFactory);
        $this->configService = $configService;
        $this->renewablesService = $renewableService;
        $this->calculatorFactory = $calculatorFactory;
    }

    /**
     * @Route("/configuratie/update", name="house_config_update_category")
     *
     * This method is an abomination with a cognitive complexity of 79 (!!!). It
     * should be refactored but I don't have the time, budget or courage to
     * touch it right now.
     */
    public function updateSingleConfigAction(Request $request)
    {
        $house = $this->getSessionHouse($request);
        if (!$house) {
            return $this->noHouseRedirect();
        }

        $success = true;
        $errors = array();
        $data = array();

        $configId   = (int)$request->get('config');
        $categoryId = (int)$request->get('category');
        $options    = $request->get('options');
        $category   = null;
        $config     = null;

        if ($house) {

            $category = $this->configService->getCategory($categoryId);

            // if $config == 0 we need to remove the configuration for the given category
            if (!$configId) {
                if (!$category) {
                    $success = false;
                    $errors[] = 'invalid category id given';
                } else {

                    // get the selected upgrade config and remove it
                    $config = $house->getUpgradeConfig($category);
                    if ($config) {
                        if ($options === 'extra' && $category->getSlug() === ConfigCategory::CAT_ROOF) {
                            $house->setExtraUpgradeRoof(null);
                        } else {
                            // if we are removing attic floor insulation, reset any custom surface area
                            if ($config->getId() !== Config::CONFIG_ATTIC_FLOOR &&
                                $config->getCategory()->getSlug() === ConfigCategory::CAT_ROOF &&
                                $house->getUpgradeConfig($config->getCategory()) === Config::CONFIG_ATTIC_FLOOR
                            ) {
                                $house->setSurfaceRoof(null);
                            }
                            $house->removeUpgradeConfig($config);
                        }
                    }

                    $this->houseService->saveHouse($house);

                }

            } else {

                $config = $this->configService->getConfig($configId);

                // is the config from the correct category?
                if ($config && $categoryId === $config->getCategory()->getId()) {

                    // do we need to set the extra roof config?
                    if ($options === 'extra' && $category->getSlug() === ConfigCategory::CAT_ROOF) {

                        if ($config === $house->getExtraConfigRoof()) {
                            $house->setExtraUpgradeRoof(null);
                        } else {
                            $house->setExtraUpgradeRoof($config);
                        }

                    } else {
                        $currentConfig = $house->getUpgradeConfig($category);

                        // the config can't be the current config...
                        if (!$currentConfig || $house->getConfig($category) !== $config) {

                            // if we are changing away from or to attic floor insulation, reset any custom surface area
                            if ($config->isCategory(ConfigCategory::CAT_ROOF) &&
                                (($config->getId() !== Config::CONFIG_ATTIC_FLOOR && $currentConfig && $currentConfig->getId() === Config::CONFIG_ATTIC_FLOOR) ||
                                ($config->getId() === Config::CONFIG_ATTIC_FLOOR && (!$currentConfig || $currentConfig->getId() !== Config::CONFIG_ATTIC_FLOOR)))
                            ) {
                                $house->setSurfaceRoof(null);
                            }
                            $house->addUpgradeConfig($config);

                        } else {

                            if ($currentConfig) {
                                // if we are changing away from or to attic floor insulation, reset any custom surface area
                                if ($config->getCategory()->getSlug() === ConfigCategory::CAT_ROOF &&
                                    (($config->getId() !== Config::CONFIG_ATTIC_FLOOR && $currentConfig && $currentConfig->getId() === Config::CONFIG_ATTIC_FLOOR) ||
                                    ($config->getId() === Config::CONFIG_ATTIC_FLOOR && (!$currentConfig || $currentConfig->getId() !== Config::CONFIG_ATTIC_FLOOR)))
                                ) {
                                    $house->setSurfaceRoof(null);
                                }
                                $house->removeUpgradeConfig($currentConfig);
                            }

                        }
                    }

                    $this->houseService->saveHouse($house);

                } else {
                    $success = false;
                    $errors[] = 'invalid config id given';
                }

            }

        } else {
            $success = false;
            $errors[] = 'no house found in session';
        }

        if ($success) {
            $data = $this->getUpdatedCalculatorInfo($house, $data);
            $data['good'] = $config && $config->isPossibleUpgrade();
        }

        return new JsonResponse(array(
            'success'   => $success,
            'errors'    => $errors,
            'data'      => $data,
        ));
    }

    /**
     * @Route("/dak/onderdak/{current}", name="house_toggle_windroof", defaults={"current"=false})
     */
    public function toggleWindroofAction($current = false)
    {
        $success = true;
        $errors = array();
        $data = array(
            'active' => false
        );

        $house = $this->houseService->loadHouse();
        if ($house) {

            if ($current) {
                $house->setHasWindRoof(!$house->hasWindRoof());
                $data['active'] = $house->hasWindRoof();
            } else {
                $house->setPlaceWindroof(!$house->getPlaceWindroof());
                $data['active'] = $house->getPlaceWindroof();
            }
            $this->houseService->saveHouse($house);

        } else {
            $success = false;
            $errors[] = 'no house found in session';
        }

        $data = $this->getUpdatedCalculatorInfo($house, $data);

        return new JsonResponse(
            array(
                'success'   => $success,
                'errors'    => $errors,
                'data'      => $data
            )
        );
    }

    /**
     * @Route("/oppervlakte/dak", name="house_update_surface_roof")
     */
    public function updateSurfaceRoofAction(Request $request)
    {
        $success = true;
        $errors = array();
        $data = array();

        $house = $this->houseService->loadHouse();
        if ($house) {

            $surface = $request->get('surface');
            if (is_numeric($surface)) {
                $house->setSurfaceRoof((float)$surface);
            } else {
                $house->setSurfaceRoof(null);
            }
            $this->houseService->saveHouse($house);

        } else {
            $success = false;
            $errors[] = 'no house found in session';
        }

        $data = $this->getUpdatedCalculatorInfo($house, $data);

        return new JsonResponse(
            array(
                'success'   => $success,
                'errors'    => $errors,
                'data'      => $data
            )
        );
    }

    /**
     * @Route("/oppervlakte/dak-plat", name="house_update_surface_roof_extra")
     */
    public function updateSurfaceRoofExtraAction(Request $request)
    {
        $success = true;
        $errors = array();
        $data = array();

        $house = $this->houseService->loadHouse();
        if ($house) {

            $surface = $request->get('surface');
            if ($house->getRoofType() == House::ROOF_TYPE_MIXED && is_numeric($surface)) {
                $house->setSurfaceRoofExtra((float)$surface);
            } else {
                $house->setSurfaceRoofExtra(null);
            }
            $this->houseService->saveHouse($house);

        } else {
            $success = false;
            $errors[] = 'no house found in session';
        }

        $data = $this->getUpdatedCalculatorInfo($house, $data);

        return new JsonResponse(
            array(
                'success'   => $success,
                'errors'    => $errors,
                'data'      => $data
            )
        );
    }

    /**
     * @Route("/oppervlakte/grond", name="house_update_surface_floor")
     */
    public function updateSurfaceFloorAction(Request $request)
    {
        $success = true;
        $errors = array();
        $data = array();

        $house = $this->houseService->loadHouse();
        if ($house) {

            $surface = $request->get('surface');
            if (is_numeric($surface)) {
                $house->setSurfaceFloor((float)$surface);
            } else {
                $house->setSurfaceFloor(null);
            }
            $this->houseService->saveHouse($house);

        } else {
            $success = false;
            $errors[] = 'no house found in session';
        }

        $data = $this->getUpdatedCalculatorInfo($house, $data);

        return new JsonResponse(
            array(
                'success'   => $success,
                'errors'    => $errors,
                'data'      => $data
            )
        );
    }

    /**
     * @Route("/oppervlakte/gevel", name="house_update_surface_facade")
     */
    public function updateSurfaceFacadeAction(Request $request)
    {
        $success = true;
        $errors = array();
        $data = array();

        $house = $this->houseService->loadHouse();
        if ($house) {

            $surface = $request->get('surface');
            if (is_numeric($surface)) {
                $house->setSurfaceFacade((float)$surface);
            } else {
                $house->setSurfaceFacade(null);
            }
            $this->houseService->saveHouse($house);

        } else {
            $success = false;
            $errors[] = 'no house found in session';
        }

        $data = $this->getUpdatedCalculatorInfo($house, $data);

        return new JsonResponse(
            array(
                'success'   => $success,
                'errors'    => $errors,
                'data'      => $data
            )
        );
    }

    /**
     * @Route("/oppervlakte/ramen", name="house_update_surface_window")
     */
    public function updateSurfaceWindowAction(Request $request)
    {
        $success = true;
        $errors = array();
        $data = array();

        $house = $this->houseService->loadHouse();
        if ($house) {

            $surface = $request->get('surface');
            if (is_numeric($surface)) {
                $house->setSurfaceWindow((float)$surface);
            } else {
                $house->setSurfaceWindow(null);
            }
            $this->houseService->saveHouse($house);

        } else {
            $success = false;
            $errors[] = 'no house found in session';
        }

        $data = $this->getUpdatedCalculatorInfo($house, $data);

        return new JsonResponse(
            array(
                'success'   => $success,
                'errors'    => $errors,
                'data'      => $data
            )
        );
    }

    /**
     * @Route("/dak/pv-wp", name="house_update_solar_wp")
     */
    public function updateSolarWPAction(Request $request)
    {
        $success = true;
        $errors = array();
        $data = array();

        $house = $this->houseService->loadHouse();
        if ($house) {

            $wp = $request->get('wp');
            if (is_numeric($wp)) {
                $house->setSolarPanelsSurface((float)$wp);
            }
            $this->houseService->saveHouse($house);

        } else {
            $success = false;
            $errors[] = 'no house found in session';
        }

        $data = $this->getUpdatedCalculatorInfo($house, $data);

        return new JsonResponse(
            array(
                'success'   => $success,
                'errors'    => $errors,
                'data'      => $data
            )
        );
    }

    /**
     * @Route("/hernieuwbaar", name="house_toggle_renewable")
     */
    public function toggleRenewableConfigAction(Request $request)
    {
        $success = true;
        $errors = array();
        $data = array(
            'active' => false
        );

        $house = $this->houseService->loadHouse();
        if ($house) {

            $renewable = $this->renewablesService->getRenewable($request->get('renewable'));
            if ($renewable) {

                if ($house->hasRenewable($renewable)) {
                    $house->removeRenewable($renewable);
                } else {
                    $house->addRenewable($renewable);
                    $data['active'] = true;
                }

                $this->houseService->saveHouse($house);

            } else {
                $success = false;
                $errors[] = 'renewable not found';
            }

        } else {
            $success = false;
            $errors[] = 'no house found in session';
        }

        return new JsonResponse(
            array(
                'success'   => $success,
                'errors'    => $errors,
                'data'      => $data
            )
        );
    }

    /**
     * @Route("/hernieuwbaar/gewenst", name="house_toggle_renewable_upgrade")
     */
    public function toggleRenewableAction(Request $request)
    {
        $success = true;
        $errors = array();
        $data = array(
            'active' => false
        );

        $house = $this->houseService->loadHouse();
        if ($house) {

            $renewable = $this->renewablesService->getRenewable($request->get('renewable'));
            if ($renewable) {

                if ($house->hasUpgradeRenewable($renewable)) {
                    $house->removeUpgradeRenewable($renewable);
                } else {
                    $house->addUpgradeRenewable($renewable);
                    $data['active'] = true;
                }

                $this->houseService->saveHouse($house);

            } else {
                $success = false;
                $errors[] = 'renewable not found';
            }

        } else {
            $success = false;
            $errors[] = 'no house found in session';
        }

        $data = $this->getUpdatedCalculatorInfo($house, $data);

        return new JsonResponse(
            array(
                'success'   => $success,
                'errors'    => $errors,
                'data'      => $data
            )
        );
    }

    /**
     * @Route("/huis-data/{token}", name="house_data")
     */
    public function exportAction(Request $request, $token)
    {

        $isLoaded = $this->houseService->loadHouseFromToken($token);

        $data = [];

        if ($isLoaded) {

            $house = $this->getSessionHouse($request);
            $calc = $this->getCalculatorView($house);
            $current = $calc->getCurrent()->getState();
            $upgrade = $calc->getUpgrade()->getState();

            $data = [
                'type' => $house->getBuildingType(),
                'year' => (int)$house->getYear(),
                'roof' => $house->getRoofType(),
                'surface' => $house->getSurfaceLivingArea(),
                'occupants' => (int)$house->getOccupants(),
                'energy' => [
                    'start' => [
                        'elec' => $house->getConsumptionElec(),
                        'gas' => $house->getConsumptionGas(),
                        'electric_heating' => $house->hasElectricHeating(),
                    ],
                    'current' => [
                        'elec' => $current->getElectricity(),
                        'gas' => $current->getGas(),
                        'electric_heating' => $current->isHeatingElectric(),
                    ],
                    'upgrade' => [
                        'elec' => $upgrade->getElectricity(),
                        'gas' => $upgrade->getGas(),
                        'electric_heating' => $upgrade->isHeatingElectric(),
                    ],
                ],
                'solar_panels' => $house->getSolarPanelsSurface(),
                'energy_custom' => $house->hasCustomEnergy(),
                'surface_custom' => $house->hasCustomSurfaces(),
                'upgrade_details' => [
                    'energy_diff' => $calc->getEnergyDiff(),
                    'price_diff' => $calc->getPriceDiff(),
                    'co2' => $calc->getCo2Diff(),
                    'subsidies' => $calc->getSubsidyTotal(),
                    'cost' => $calc->getBuildCostTotal(),
                ],
            ];

            foreach ($house->getConfigs() as $config) {
                $upgrade = $house->getUpgradeConfig($config->getCategory());
                $data['categories'][$config->getCategory()->getSlug()] = [
                    'current' => $config->getLabel(),
                    'upgrade' => $upgrade ? $upgrade->getLabel(): null,
                    'energy_diff' => $calc->getEnergyDiffForCategory($config->getCategory()),
                    'co2_diff' => $calc->getCo2DiffForCategory($config->getCategory()),
                    'price_diff' => $calc->getPriceDiffForCategory($config->getCategory()),
                ];
            }

            foreach ($house->getAllRenewables() as $renewable) {
                $data['renewables'][$renewable->getSlug()] = [
                    'current' => $house->hasRenewable($renewable),
                    'upgrade' => $house->hasUpgradeRenewable($renewable),
                    'energy_diff' => $calc->getEnergyDiffForRenewable($renewable),
                    'co2_diff' => $calc->getCo2DiffForCategory($renewable),
                    'price_diff' => $calc->getPriceDiffForRenewable($renewable),
                ];
            }

        } else {
            $data = [
                'success' => false,
                'code' => 'HOUSE_NOT_FOUND',
                'error' => 'no house found for token',
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * Get the updated calculator info for a house.
     *
     * @param House $house
     *   The house to get the calculator info for.
     * @param array $data
     *   (optional) The current data.
     *
     * @return array
     *   The updated data.
     */
    protected function getUpdatedCalculatorInfo(House $house, array $data = array())
    {
        $view = $this->getCalculatorView($house);

        // Totals.

        $data['renewable_diff'] = Format::energy($view->getEnergyDiffForRenewables());
        $data['renewable_price'] = Format::price($view->getPriceDiffForRenewables());
        $data['energy_diff'] = Format::energy($view->getEnergyDiff());
        $data['price_diff'] = Format::price($view->getPriceDiff());
        $data['subsidies'] = Format::price($view->getSubsidyTotal());
        $data['cost'] = Format::price($view->getBuildCostTotal());
        $data['co2'] = Format::CO2($view->getCo2Diff());
        $data['score_config'] = $view->getAvgScoreConfig();

        // roof settings and surfaces
        $data['roof_windroof_possible'] = $house->canHaveWindRoof();
        $data['roof_surface'] = $house->getSurfaceRoof(true, $house->getUpgradeConfig(ConfigCategory::CAT_ROOF));

        // heat pump allowed?
        $data['heat_pump_allowed'] = $house->isHeatPumpAllowed();

        // category specific

        foreach ($house->getConfigs() as $config) {
            $data['categories'][$config->getCategory()->getSlug()] = array(
                'diff'  => Format::energy(
                    $view->getEnergyDiffForCategory($config->getCategory())
                ),
                'price' => Format::price(
                    $view->getPriceDiffForCategory($config->getCategory())
                ),
            );
        }

        // renewables

        $renewables = array(
            'diff'  => 0,
            'price' => 0,
        );
        foreach ($house->getUpgradeRenewables() as $renewable) {
            $renewables['diff']     += $view->getEnergyDiffForRenewable($renewable);
            $renewables['price']    += $view->getPriceDiffForRenewable($renewable);
        }
        $renewables['diff']     = Format::energy($renewables['diff']);
        $renewables['price']    = Format::price($renewables['price']);
        $data['renewables']     = $renewables;

        return $data;
    }

    /**
     * Get the calculator view.
     *
     * @param House $house
     *   The house to get the calculator view for.
     *
     * @return CalculatorView
     */
    protected function getCalculatorView(House $house)
    {
        if (!$this->calculatorView) {
            $this->calculatorView = $this->calculatorFactory->createCalculatorView($house);
        }

        return $this->calculatorView;
    }
}
