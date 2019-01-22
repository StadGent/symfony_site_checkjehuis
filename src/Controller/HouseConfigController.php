<?php

namespace App\Controller;

use App\Calculator\CalculatorFactory;
use App\Entity\ConfigCategory;
use App\Entity\Content;
use App\Entity\House;
use App\Service\ConfigService;
use App\Service\ContentService;
use App\Service\HouseService;
use App\Service\ParameterService;
use App\Service\RenewablesService;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/zo-woon-ik")
 */
class HouseConfigController extends AbstractController
{
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
     * @param ConfigService $configService
     *   The config service.
     * @param RenewablesService $renewablesService
     *   The renewables service.
     * @param CalculatorFactory $calculatorFactory
     *   The calculator factory.
     */
    public function __construct(
        HouseService $houseService,
        ContentService $contentService,
        ParameterService $parameterService,
        ConfigService $configService,
        RenewablesService $renewablesService,
        CalculatorFactory $calculatorFactory
    ) {
        parent::__construct($houseService, $contentService, $parameterService);
        $this->configService = $configService;
        $this->renewablesService = $renewablesService;
        $this->calculatorFactory = $calculatorFactory;
    }

    /**
     * @Route("/", name="house_config")
     * @Route("/dakisolatie", name="house_config_roof")
     */
    public function roofConfigAction(Request $request)
    {
        $house = $this->getSessionHouse($request);

        if (!$house) {
            return $this->noHouseRedirect();
        }

        $category = $this->configService->getCategoryBySlug(ConfigCategory::CAT_ROOF);

        if ($request->isMethod('post')) {

            $house->addConfig(
                $this->configService->getConfig($request->get('roof'))
            );

            if ($house->getRoofType() == House::ROOF_TYPE_MIXED) {
                $house->setExtraConfigRoof(
                    $this->configService->getConfig($request->get('roof-extra'))
                );
            } else {
                $house->setExtraConfigRoof(null);
            }

            $this->houseService->saveHouse($house);

            return $this->redirect($this->generateUrl('house_config_facade'));
        }

        $this->saveHouseLastRoute($request);

        return $this->render('house/config/roof.html.twig', array(
            'house'             => $house,
            'category'          => $category,
            'configBad'         => $this->configService->getCategory(2),
            'configModerate'    => $this->configService->getCategory(4),
            'configGood'        => $this->configService->getCategory(5),
            'content'           => $this->contentService->getContentBySlug(Content::TWO_ROOF),
            'contentHeatMap'    => $this->contentService->getContentBySlug(Content::TWO_HEAT_MAP),
            'urlHeatMap'        => $this->houseService->parseUrl(
                $this->houseService->getUrlHeatMap(),
                $house,
                $this->generateUrl('house_roof', array(), UrlGeneratorInterface::ABSOLUTE_URL)
            )
        ));
    }

    /**
     * @Route("/gevelisolatie", name="house_config_facade")
     */
    public function facadeConfigAction(Request $request)
    {
        $house = $this->getSessionHouse($request);

        if (!$house) {
            return $this->noHouseRedirect();
        }

        $category = $this->configService->getCategoryBySlug(ConfigCategory::CAT_FACADE);

        if ($request->isMethod('post')) {

            $house->addConfig(
                $this->configService->getConfig($request->get('facade'))
            );

            $this->houseService->saveHouse($house);

            return $this->redirect($this->generateUrl('house_config_floor'));
        }

        $this->saveHouseLastRoute($request);

        return $this->render('house/config/facade.html.twig', array(
            'house'         => $house,
            'category'      => $category,
            'content'       => $this->contentService->getContentBySlug(Content::TWO_FACADE),
        ));
    }

    /**
     * @Route("/vloerisolatie", name="house_config_floor")
     */
    public function floorConfigAction(Request $request)
    {
        $house = $this->getSessionHouse($request);

        if (!$house) {
            return $this->noHouseRedirect();
        }

        $category = $this->configService->getCategoryBySlug(ConfigCategory::CAT_FLOOR);

        if ($request->isMethod('post')) {

            $house->addConfig(
                $this->configService->getConfig($request->get('floor'))
            );

            $this->houseService->saveHouse($house);

            return $this->redirect($this->generateUrl('house_config_window'));
        }

        $this->saveHouseLastRoute($request);

        return $this->render('house/config/floor.html.twig', array(
            'house'             => $house,
            'category'          => $category,
            'content'           => $this->contentService->getContentBySlug(Content::TWO_FLOOR),
        ));
    }

    /**
     * @Route("/ramen", name="house_config_window")
     */
    public function windowConfigAction(Request $request)
    {
        $house = $this->getSessionHouse($request);

        if (!$house) {
            return $this->noHouseRedirect();
        }

        $category = $this->configService->getCategoryBySlug(ConfigCategory::CAT_WINDOWS);

        if ($request->isMethod('post')) {

            $house->addConfig(
                $this->configService->getConfig($request->get('window'))
            );

            $this->houseService->saveHouse($house);

            return $this->redirect($this->generateUrl('house_config_ventilation'));
        }

        $this->saveHouseLastRoute($request);

        return $this->render('house/config/window.html.twig', array(
            'house'         => $house,
            'category'      => $category,
            'content'       => $this->contentService->getContentBySlug(Content::TWO_WINDOW),
        ));
    }

    /**
     * @Route("/ventilatie", name="house_config_ventilation")
     */
    public function ventilationConfigAction(Request $request)
    {
        $house = $this->getSessionHouse($request);

        if (!$house) {
            return $this->noHouseRedirect();
        }

        $category = $this->configService->getCategoryBySlug(ConfigCategory::CAT_VENTILATION);

        if ($request->isMethod('post')) {

            $house->addConfig(
                $this->configService->getConfig($request->get('ventilation'))
            );

            $this->houseService->saveHouse($house);

            return $this->redirect($this->generateUrl('house_config_heating'));
        }

        $this->saveHouseLastRoute($request);

        return $this->render('house/config/ventilation.html.twig', array(
            'house'         => $house,
            'category'      => $category,
            'content'       => $this->contentService->getContentBySlug(Content::TWO_VENTILATION),
        ));
    }

    /**
     * @Route("/verwarming", name="house_config_heating")
     */
    public function heatingConfigAction(Request $request)
    {
        $house = $this->getSessionHouse($request);

        if (!$house) {
            return $this->noHouseRedirect();
        }

        $slug = ($house->hasElectricHeating()) ? ConfigCategory::CAT_HEATING_ELEC: ConfigCategory::CAT_HEATING;
        $category = $this->configService->getCategoryBySlug($slug);

        if ($request->isMethod('post')) {

            $house->addConfig(
                $this->configService->getConfig($request->get('heating'))
            );

            $this->houseService->saveHouse($house);

            return $this->redirect($this->generateUrl('house_config_renewable'));
        }

        $this->saveHouseLastRoute($request);

        return $this->render('house/config/heating.html.twig', array(
            'house'         => $house,
            'category'      => $category,
            'content'       => $this->contentService->getContentBySlug(Content::TWO_HEATING),
        ));
    }

    /**
     * @Route("/hernieuwebare-energie", name="house_config_renewable")
     */
    public function renewableConfigAction(Request $request)
    {
        $house = $this->getSessionHouse($request);

        if (!$house) {
            return $this->noHouseRedirect();
        }

        $this->saveHouseLastRoute($request);

        return $this->render('house/config/renewable.html.twig', array(
            'house'         => $house,
            'renewables'    => $this->renewablesService->getAll(),
            'content'       => $this->contentService->getContentBySlug(Content::TWO_RENEWABLE),
        ));
    }

    /**
     * @Route("/energie", name="house_energy_summary")
     */
    public function energySummaryAction(Request $request)
    {
        $house = $this->getSessionHouse($request);

        if (!$house) {
            return $this->noHouseRedirect();
        }

        $this->saveHouseLastRoute($request);

        $params = $this->calculatorFactory->createParameters();
        $view = $this->calculatorFactory->createCalculatorView($house, true);

        return $this->render('house/config/energy-summary.html.twig', array(
            'house'         => $house,
            'calculator'    => $view,
            'params'        => $params,
        ));
    }
}
