<?php

namespace App\Controller;

use App\Entity\ConfigCategory;
use App\Entity\Content;
use App\Entity\House;
use App\Entity\Parameter;
use App\Service\ContentService;
use App\Service\HouseService;
use App\Service\ParameterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as FrameworkController;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * Controller constructor.
     *
     * @param HouseService $houseService
     *   The house service.
     * @param ContentService $contentService
     *   The content service.
     * @param ParameterService $parameterService
     *   The parameter service.
     */
    public function __construct(
        HouseService $houseService,
        ContentService $contentService,
        ParameterService $parameterService
    ) {
        $this->houseService = $houseService;
        $this->contentService = $contentService;
        $this->parameterService = $parameterService;
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
