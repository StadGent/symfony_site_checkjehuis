<?php

namespace App\Controller;

use App\Entity\Content;
use App\Entity\House;
use App\Service\ContentService;
use App\Service\DefaultsService;
use App\Service\HouseService;
use App\Service\ParameterService;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/mijn-huis")
 */
class HouseController extends AbstractController
{
    /**
     * The defaults service.
     *
     * @var DefaultsService
     */
    protected $defaultsService;

    /**
     * The asset manager.
     *
     * @var Packages
     */
    protected $assetManager;

    /**
     * Controller constructor.
     *
     * @param HouseService $houseService
     *   The house service.
     * @param ContentService $contentService
     *   The content service.
     * @param ParameterService $parameterService
     *   The parameter service.
     * @param Packages $assetManager
     *   The asset manager.
     * @param DefaultsService $defaultsService
     *   The defaults service.
     */
    public function __construct(
        HouseService $houseService,
        ContentService $contentService,
        ParameterService $parameterService,
        Packages $assetManager,
        DefaultsService $defaultsService
    ) {
        parent::__construct($houseService, $contentService, $parameterService);
        $this->assetManager = $assetManager;
        $this->defaultsService = $defaultsService;
    }

    /**
     * @Route("/", name="my_house")
     * @Route("/type", name="house_building_type")
     */
    public function buildingTypeAction(Request $request)
    {
        $house = $this->getSessionHouse($request, true);

        if ($request->isMethod('post')) {

            if ($house->getBuildingType() != $request->get('building-type')) {
                $house->setBuildingType($request->get('building-type'));
                $this->houseService->saveHouse($house, true);
            }


            return $this->redirect($this->generateUrl('house_year'));
        }

        $buildingTypes = House::getBuildingTypes();
        $options = array();
        foreach ($buildingTypes as $type => $label) {
            $options[] = array(
                'value'     => $type,
                'label'     => $label,
                'icon'      => $this->getAsset('images/icons/house-' . $type . '.svg'),
                'active'    => $house->getBuildingType() == $type
            );
        }

        $this->saveHouseLastRoute($request);

        return $this->render('house/basics/building-type.html.twig', array(
            'house'         => $house,
            'options'       => $options,
            'content'       => $this->contentService->getContentBySlug(Content::ONE_TYPE),
        ));
    }

    /**
     * @Route("/jaar", name="house_year")
     */
    public function yearAction(Request $request)
    {
        $house = $this->getSessionHouse($request);

        if (!$house) {
            return $this->noHouseRedirect();
        }

        if ($request->isMethod('post')) {

            if ($house->getYear() != $request->get('year')) {
                $house->setYear($request->get('year'));
                $this->houseService->saveHouse($house, true);
            }

            return $this->redirect($this->generateUrl('house_roof'));
        }

        $options = House::getYears();

        $this->saveHouseLastRoute($request);

        return $this->render('house/basics/year.html.twig', array(
            'house'         => $house,
            'options'       => $options,
            'content'       => $this->contentService->getContentBySlug(Content::ONE_YEAR),
        ));
    }

    /**
     * @Route("/dak-type", name="house_roof")
     */
    public function roofTypeAction(Request $request)
    {
        $house = $this->getSessionHouse($request);

        if (!$house) {
            return $this->noHouseRedirect();
        }

        if ($request->isMethod('post')) {

            if ($house->getRoofType() != $request->get('roof-type')) {
                $house->setRoofType($request->get('roof-type'));
                $this->houseService->saveHouse($house, true);
            }

            return $this->redirect($this->generateUrl('house_surface'));
        }

        $roofTypes = House::getRoofTypes();
        $options = array();
        $icons = array(
            House::ROOF_TYPE_INCLINED   => 'inclined',
            House::ROOF_TYPE_FLAT       => 'flat',
            House::ROOF_TYPE_MIXED      => 'mixed',
        );
        foreach ($roofTypes as $type => $label) {
            $options[] = array(
                'value'     => $type,
                'label'     => $label,
                'icon'      => $this->getAsset('images/icons/house-roof-' . $icons[$type] . '.svg'),
                'active'    => $house->getRoofType() == $type
            );
        }

        $this->saveHouseLastRoute($request);

        return $this->render('house/basics/roof-type.html.twig', array(
            'house'         => $house,
            'options'       => $options,
            'content'       => $this->contentService->getContentBySlug(Content::ONE_ROOF),
        ));
    }

    /**
     * @Route("/oppervlakte", name="house_surface")
     */
    public function surfaceAction(Request $request)
    {
        $house = $this->getSessionHouse($request);

        if (!$house) {
            return $this->noHouseRedirect();
        }

        if ($request->isMethod('post')) {

            $surface = $request->get('size');
            if ($surface == 'custom-input') {

                $house->setSurfaceLivingArea($request->get('square-meters'));

            } else {
                $house->setSurfaceLivingArea(null);
                $house->setSize($request->get('size'));
            }

            $this->houseService->saveHouse($house, true);

            return $this->redirect($this->generateUrl('house_ownership'));
        }

        $sizes = House::getSizes();
        $options = array();
        foreach ($sizes as $type => $label) {
            $options[] = array(
                'value'     => $type,
                'label'     => $label,
                'icon'      => $this->getAsset('images/icons/house-' . $type . '.svg'),
                'active'    => !$house->getSurfaceFloor(false) && $house->getSize() == $type
            );
        }

        $this->saveHouseLastRoute($request);

        return $this->render('house/basics/surface.html.twig', array(
            'house'         => $house,
            'options'       => $options,
            'content'       => $this->contentService->getContentBySlug(Content::ONE_SURFACE),
        ));
    }

    /**
     * @Route("/eigenaar", name="house_ownership")
     */
    public function ownershipAction(Request $request)
    {
        $house = $this->getSessionHouse($request);

        if (!$house) {
            return $this->noHouseRedirect();
        }

        if ($request->isMethod('post')) {

            if ($house->getOwnership() != $request->get('ownership')) {
                $house->setOwnership($request->get('ownership'));
                $this->houseService->saveHouse($house, true);
            }

            return $this->redirect($this->generateUrl('house_occupants'));
        }

        $this->saveHouseLastRoute($request);

        return $this->render('house/basics/ownership.html.twig', array(
            'house'         => $house,
            'owner'         => House::OWNERSHIP_OWNER,
            'renter'        => House::OWNERSHIP_RENTER,
            'content'       => $this->contentService->getContentBySlug(Content::ONE_OWNER),
        ));
    }

    /**
     * @Route("/bewoners", name="house_occupants")
     */
    public function occupantsAction(Request $request)
    {
        $house = $this->getSessionHouse($request);

        if (!$house) {
            return $this->noHouseRedirect();
        }

        if ($request->isMethod('post')) {

            if ($house->getOccupants() != $request->get('occupants')) {
                $house->setOccupants($request->get('occupants'));
                $this->houseService->saveHouse($house, true);
            }

            return $this->redirect($this->generateUrl('house_energy'));
        }

        $this->saveHouseLastRoute($request);

        return $this->render('house/basics/occupants.html.twig', array(
            'house'         => $house,
            'content'       => $this->contentService->getContentBySlug(Content::ONE_OCCUPANTS),
        ));
    }

    /**
     * @Route("/energie", name="house_energy")
     */
    public function energyAction(Request $request)
    {
        $house = $this->getSessionHouse($request);

        if (!$house) {
            return $this->noHouseRedirect();
        }

        if ($request->isMethod('post')) {

            $energyToUse = $request->get('energy');
            $electricHeating = (bool)$request->get('electric-heating', false);

            if ($energyToUse == 'custom') {

                $house->setConsumptionGas($request->get('gas'));
                $house->setConsumptionElec($request->get('elec'));

            } else {

                $house->setConsumptionGas(null);
                $house->setConsumptionElec(null);

            }

            $reset = $house->hasElectricHeating() != $electricHeating;
            $house->setElectricHeating($electricHeating);

            $this->houseService->saveHouse($house, $reset);

            return $this->redirect($this->generateUrl('house_config_roof'));
        }

        $electricHeating = $this->defaultsService->getEnergy($house->getBuildingType(), $house->getSize(), $house->getYear())->getElectricHeating();

        $energy = array(
            'non-elec' => array(
                'gas' => $house->getDefaultEnergy()->getGas(),
                'elec' => $house->getDefaultEnergy()->getElectricity(),
            ),
            'elec' => array(
                'gas' => 0,
                'elec' => $house->getDefaultEnergy()->getElectricity() + $electricHeating,
            ),
        );

        $this->saveHouseLastRoute($request);

        return $this->render('house/basics/energy.html.twig', array(
            'house'             => $house,
            'energy'            => $energy,
            'content_avg'       => $this->contentService->getContentBySlug(Content::ONE_ENERGY_AVG),
            'content_custom'    => $this->contentService->getContentBySlug(Content::ONE_ENERGY_CUSTOM),
        ));
    }

    /**
     * Get he url for an asset.
     *
     * @param string $path
     *   The path to the asset.
     *
     * @return string
     *   The url to the asset.
     */
    protected function getAsset($path)
    {
        $path = 'build/' . $path;
        return $this->assetManager->getUrl($path);
    }
}
