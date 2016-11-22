<?php

namespace Digip\RenovationBundle\Controller;

use Digip\RenovationBundle\Entity\ConfigCategory;
use Digip\RenovationBundle\Entity\Content;
use Digip\RenovationBundle\Entity\Parameter;
use Digip\RenovationBundle\Service\HouseService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Digip\RenovationBundle\Entity\House;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractController extends Controller
{
    const COOKIE_USER_ADDRESS = 'user-address';

    /**
     * @param bool $create
     * @return bool|House
     */
    protected function getSessionHouse($create = false)
    {
        $houseService = $this->getHouseService();
        $house = $houseService->loadHouse();

        $save = false;
        $cookieAddress = $this->get('request')->cookies->get('user-address');

        if (!$house) {
            if ($create) {
                $paramService = $this->get('digip_reno.service.parameter');

                $house = new House();
                $house->setConfigs($houseService->getDefaultConfigs($house));
                $house->setExtraConfigRoof($house->getConfig(ConfigCategory::CAT_ROOF));
                $house->setDefaultSurfaces(
                    $houseService->getDefaultSurface($house),
                    $houseService->getDefaultRoof($house)
                );
                $house->setDefaultRoofIfFlat($houseService->getDefaultRoofIfFlat($house));
                $house->setDefaultEnergy($houseService->getDefaultEnergy($house));
                $house->setSolarPanelsSurface(
                    $paramService->getParameterBySlug(Parameter::PARAM_SOLAR_SURFACE)->getValue()
                );

                $save = true;
            }
        }

        if ($cookieAddress && $cookieAddress != $house->getAddress()) {
            $house->setAddress($cookieAddress);
            $save = true;
        }

        if ($save) $houseService->saveHouse($house);

        return $house;
    }

    protected function setGentCookies(Response $response, House $house)
    {
        $domain = $this->container->getParameter('gent_cookie_domain');
        $response->headers->setCookie(
            new Cookie(self::COOKIE_USER_ADDRESS, $house->getAddress(), 0, '/', $domain)
        );
    }

    /**
     * @return RedirectResponse
     */
    protected function noHouseRedirect()
    {
        return $this->redirect($this->generateUrl('house_building_type'));
    }

    /**
     * @return HouseService
     */
    protected function getHouseService()
    {
        return $houseService = $this->get('digip_reno.service.house');
    }

    /**
     * @param string $path
     * @param string $packageName
     * @return string
     */
    protected function getAsset($path)
    {
        $path = 'bundles/digiprenovation/' . $path;
        return $this->container->get('templating.helper.assets')->getUrl($path);
    }

    /**
     * @param $slug
     * @return Content
     */
    protected function getContentBySlug($slug)
    {
        return $this->get('digip_reno.service.content')->getContentBySlug($slug);
    }
}
