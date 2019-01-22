<?php

namespace App\Controller;

use App\Calculator\CalculatorFactory;
use App\Entity\ConfigCategory;
use App\Entity\Content;
use App\Service\ConfigService;
use App\Service\ContentService;
use App\Service\HouseService;
use App\Service\ParameterService;
use App\Service\RenewablesService;
use App\Service\SubsidyService;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/zo-wil-ik-wonen")
 */
class HouseCalculatorController extends AbstractController
{
    /**
     * The calculator factory.
     *
     * @var CalculatoFactory
     */
    protected $calculatorFactory;

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
     * The subsidy service.
     *
     * @var SubsidyService
     */
    protected $subsidyService;

    /**
     * Controller constructor.
     *
     * @param HouseService $houseService
     *   The house service.
     * @param ContentService $contentService
     *   The content service.
     * @param ParameterService $parameterService
     *   The parameter service.
     * @param CalculatorFactory $calculatorFactory
     *   The calculator factory.
     * @param ConfigService $configService
     *   The config service.
     * @param RenewablesService $renewablesService
     *   The renewables service.
     * @param SubsidyService $subsidyService
     *   The subsidy service.
     */
    public function __construct(
        HouseService $houseService,
        ContentService $contentService,
        ParameterService $parameterService,
        CalculatorFactory $calculatorFactory,
        ConfigService $configService,
        RenewablesService $renewablesService,
        SubsidyService $subsidyService
    ) {
        parent::__construct($houseService, $contentService, $parameterService);
        $this->calculatorFactory = $calculatorFactory;
        $this->configService = $configService;
        $this->renewablesService = $renewablesService;
        $this->subsidyService = $subsidyService;
    }

    /**
     * @Route("/", name="house_calculator")
     */
    public function calculatorAction(Request $request)
    {
        $house = $this->getSessionHouse($request);
        if (!$house) {
            return $this->noHouseRedirect();
        }

        $this->saveHouseLastRoute($request);

        $view = $this->calculatorFactory->createCalculatorView($house);

        $categories = $this->configService->getAllCategoriesForHouse($house);
        $categoryContent = array();
        $categoryContent[ConfigCategory::CAT_ROOF]          = $this->contentService->getContentBySlug(Content::THREE_ROOF);
        $categoryContent[ConfigCategory::CAT_FACADE]        = $this->contentService->getContentBySlug(Content::THREE_FACADE);
        $categoryContent[ConfigCategory::CAT_FLOOR]         = $this->contentService->getContentBySlug(Content::THREE_FLOOR);
        $categoryContent[ConfigCategory::CAT_WINDOWS]       = $this->contentService->getContentBySlug(Content::THREE_WINDOW);
        $categoryContent[ConfigCategory::CAT_VENTILATION]   = $this->contentService->getContentBySlug(Content::THREE_VENTILATION);
        $categoryContent[ConfigCategory::CAT_HEATING]       = $this->contentService->getContentBySlug(Content::THREE_HEATING);
        $categoryContent[ConfigCategory::CAT_HEATING_ELEC]  = $categoryContent[ConfigCategory::CAT_HEATING];

        $categoryPremium = array();
        $categoryPremium[ConfigCategory::CAT_ROOF]          = $this->contentService->getContentBySlug(Content::PREMIUM_ROOF);
        $categoryPremium[ConfigCategory::CAT_FACADE]        = $this->contentService->getContentBySlug(Content::PREMIUM_FACADE);
        $categoryPremium[ConfigCategory::CAT_FLOOR]         = $this->contentService->getContentBySlug(Content::PREMIUM_FLOOR);
        $categoryPremium[ConfigCategory::CAT_WINDOWS]       = $this->contentService->getContentBySlug(Content::PREMIUM_WINDOW);
        $categoryPremium[ConfigCategory::CAT_VENTILATION]   = $this->contentService->getContentBySlug(Content::PREMIUM_VENTILATION);
        $categoryPremium[ConfigCategory::CAT_HEATING]       = $this->contentService->getContentBySlug(Content::PREMIUM_HEATING);
        $categoryPremium[ConfigCategory::CAT_HEATING_ELEC]  = $categoryPremium[ConfigCategory::CAT_HEATING];

        $renewableContent   = $this->contentService->getContentBySlug(Content::THREE_RENEWABLE);
        $renewablePremium   = $this->contentService->getContentBySlug(Content::PREMIUM_RENEWABLES);

        return $this->render('house/calculator.html.twig', array(
            'house'             => $house,
            'calculator'        => $view,
            'configCategories'  => $categories,
            'categoryContent'   => $categoryContent,
            'categoryPremium'   => $categoryPremium,
            'renewables'        => $this->renewablesService->getAll(),
            'renewableContent'  => $renewableContent,
            'renewablePremium'  => $renewablePremium,
            'modalHeatpumpNotAllowed' => $this->contentService->getContentBySlug(Content::HEAT_PUMP_NOT_ALLOWED),
            'showDetails'       => $this->getParameter('calculation_debug_show'),
            'urlSolarMap'       => $this->houseService->parseUrl(
                $this->houseService->getUrlSolarMap(),
                $house,
                $this->generateUrl('house_calculator', array(), UrlGeneratorInterface::ABSOLUTE_URL)
            )
        ));
    }

    /**
     * @Route("/detail", name="house_calc_detail")
     */
    public function calculationDetailAction(Request $request)
    {
        $house = $this->getSessionHouse($request);
        if (!$house) {
            return $this->noHouseRedirect();
        }

        $view = $this->calculatorFactory->createCalculatorView($house);

        return $this->render('house/calculation-detail.html.twig', array(
            'house'             => $house,
            'calculator'        => $view,
        ));
    }

    /**
     * @Route("/pdf/template/{token}", name="house_calc_pdf_template")
     */
    public function calculationPdfTemplateAction(Request $request, $token)
    {
        if ($this->container->has('profiler')) {
            $this->container->get('profiler')->disable();
        }

        $this->houseService->loadHouseFromToken($token);
        $house = $this->getSessionHouse($request);

        if (!$house) {
            return $this->render('pdf/error.html.twig');
        }

        $view = $this->calculatorFactory->createCalculatorView($house);

        return $this->render('pdf/plan.html.twig', array(
            'house'             => $house,
            'calculator'        => $view,
            'configCategories'  => $this->configService->getAllCategories(),
            'renewables'        => $this->renewablesService->getAll(),
            'subsidies'         => iterator_to_array($this->idsAsKeys($this->subsidyService->getAllSubsidyCategories())),
        ));
    }

    /**
     * @Route("/pdf", name="house_calc_pdf")
     */
    public function calculationPdfAction(Request $request)
    {
        $house = $this->getSessionHouse($request);
        if (!$house) {
            return $this->noHouseRedirect();
        }

        try {

            // disable toolbar when displaying the pdf, even in dev
            error_log((int)$this->container->has('profiler'));
            if ($this->container->has('profiler')) {
                $this->container->get('profiler')->disable();
            }

            return new Response(
                $this->houseService->generatePdf($house),
                200,
                array(
                    'Content-Type'          => 'application/pdf',
                    // uncomment to download pdf instead of view it in browser
                    // 'Content-Disposition'   => 'attachment; filename="gent-klimaatstad.pdf"'
                )
            );

        } catch (\Exception $e) {
            error_log($e->getMessage());
            error_log($e->getTraceAsString());
        }

        return $this->render('pdf/error.html.twig');
    }

    /**
     * @param $entities
     * @return \Generator|array
     */
    protected function idsAsKeys($entities)
    {
        foreach ($entities as $e) {
            yield $e->getId() => $e;
        }
    }
}
