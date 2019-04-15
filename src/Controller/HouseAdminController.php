<?php

namespace App\Controller;

use App\Calculator\CalculatorFactory;
use App\Factory\HouseFactory;
use App\Service\ConfigService;
use App\Service\ContentService;
use App\Service\HouseExportService;
use App\Service\HouseService;
use App\Service\ParameterService;
use App\Service\RenewablesService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HouseAdminController extends AbstractController
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
     * The house export service.
     *
     * @var HouseExportService
     */
    protected $houseExportService;

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
     * @param RenewablesService $renewablesService
     *   The renewables service.
     * @param CalculatorFactory $calculatorFactory
     *   The calculator factory.
     * @param HouseExportService $houseExportService
     *   The house export service.
     */
    public function __construct(
        HouseService $houseService,
        ContentService $contentService,
        ParameterService $parameterService,
        HouseFactory $houseFactory,
        ConfigService $configService,
        RenewablesService $renewablesService,
        CalculatorFactory $calculatorFactory,
        HouseExportService $houseExportService
    ) {
        parent::__construct($houseService, $contentService, $parameterService, $houseFactory);
        $this->configService = $configService;
        $this->renewablesService = $renewablesService;
        $this->calculatorFactory = $calculatorFactory;
        $this->houseExportService = $houseExportService;
    }

    /**
     * @Route("/admin/houses", name="admin_houses")
     */
    public function adminListAction(Request $request)
    {
        $filter = $this->getAdminHouseFilter($request);

        $houses = $this->houseService->getAllHouses($filter);

        return $this->render('house/admin-list.html.twig', array(
            'houses' => $houses,
            'filter' => $filter,
        ));
    }

    /**
     * @Route("/admin/houses/download", name="admin_houses_download")
     */
    public function adminListExportAction(Request $request)
    {
        $filter = $this->getAdminHouseFilter($request);
        $csv = $this->houseExportService->getCsv($this->houseService->getAllHouses($filter));

        $response = new Response(
            $csv,
            200,
            [
                'Content-Type' => 'text/csv',
                'Content-Description' => 'Gent klimaatstad - export',
                'Content-Disposition' => 'attachment; filename=klimaatstad-beslissingsboom-export.csv',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]
        );

        return $response;
    }

    /**
     * Get the filter for the admin page.
     *
     * @param Request $request
     *   The current request.
     *
     * @return array
     *   The filters.
     */
    protected function getAdminHouseFilter(Request $request)
    {
        $filter = $request->get('table-filter', array());

        $filter['from'] = (isset($filter['from'])) ?
            new \Datetime(date(\Datetime::ISO8601, $filter['from']/1000)):
            new \Datetime();
        $filter['from']->setTime(0, 0, 0);

        $filter['to'] = (isset($filter['to'])) ?
            new \Datetime(date(\Datetime::ISO8601, $filter['to']/1000)):
            new \Datetime();
        $filter['to']->setTime(23, 59, 59);

        return $filter;
    }
}
