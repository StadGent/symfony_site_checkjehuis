<?php

namespace App\Controller;

use App\Factory\HouseFactory;
use App\Service\ContentService;
use App\Service\HouseService;
use App\Service\ParameterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as FrameworkController;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractController extends FrameworkController
{
    /**
     * The user address cookie name.
     */
    const COOKIE_USER_ADDRESS = 'user-address';

    /**
     * The house service.
     *
     * @var HouseService
     */
    protected $houseService;

    /**
     * The content service.
     *
     * @var ContentService
     */
    protected $contentService;

    /**
     * The parameter service.
     *
     * @var ParameterService
     */
    protected $parameterService;

    /**
     * The house factory.
     *
     * @var HouseFactory
     */
    protected $houseFactory;

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
     */
    public function __construct(
        HouseService $houseService,
        ContentService $contentService,
        ParameterService $parameterService,
        HouseFactory $houseFactory
    ) {
        $this->houseService = $houseService;
        $this->contentService = $contentService;
        $this->parameterService = $parameterService;
        $this->houseFactory = $houseFactory;
    }

    /**
     * Get the house for the current session.
     *
     * @param bool $create
     *   Whether or not to create one if no house exists for this session.
     * @return bool|House
     *   False if no house exists and $create is false, a house instance
     *   otherwise.
     */
    protected function getSessionHouse(Request $request, $create = false)
    {
        $house = $this->houseService->loadHouse();

        $save = false;
        $cookieAddress = $request->cookies->get('user-address');

        if (!$house) {
            if ($create) {
                $house = $this->houseFactory->create();
                $save = true;
            }
        }

        // Sentinel to prevent exceptions when sending $house the getAddress()
        // message.
        if (!$house) {
            return false;
        }

        if ($cookieAddress && $cookieAddress != $house->getAddress()) {
            $house->setAddress($cookieAddress);
            $save = true;
        }

        if ($save) {
            $this->houseService->saveHouse($house);
        }

        return $house;
    }

    /**
     * Redirect to execute when no house is set for the current session.
     *
     * @return RedirectResponse
     *   The redirect response to send.
     */
    protected function noHouseRedirect()
    {
        return $this->redirect($this->generateUrl('house_building_type'));
    }

    /**
     * Save the last route for a house.
     *
     * @param Request $request
     *   The current request.
     */
    protected function saveHouseLastRoute(Request $request)
    {
        $house = $this->getSessionHouse($request);
        $currentRout = $request->get('_route');
        $house->setLastKnownRoute($currentRout);
        $this->houseService->saveHouse($house);
    }
}
