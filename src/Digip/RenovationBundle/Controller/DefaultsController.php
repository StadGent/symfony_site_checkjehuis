<?php

namespace Digip\RenovationBundle\Controller;

use Digip\RenovationBundle\Entity\House;
use Digip\RenovationBundle\Form\DefaultRoofSurfaceType;
use Digip\RenovationBundle\Form\DefaultSurfacesType;
use Digip\RenovationBundle\Service\DefaultsService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultsController extends Controller
{
    public function indexAction(Request $request)
    {
        /** @var DefaultsService $service */
        $service = $this->get('digip_reno.service.defaults');

        $filter = $request->get('table-filter', array());

        return $this->render('DigipRenovationBundle:Defaults:index.html.twig', array(
            'buildingTypes'     => House::getBuildingTypes(),
            'roofTypes'         => House::getRoofTypes(),
            'buildingSizes'     => House::getSizes(),
            'defaults'          => $service->getAllSurfaces($filter),
            'defaultsRoof'      => $service->getAllRoofs($filter),
            'filter'            => $filter,
        ));
    }

    public function updateDefaultsAction($id, Request $request)
    {
        $service = $this->get('digip_reno.service.defaults');

        $surface = $service->getSurfaceById($id);

        $form = $this->createForm(new DefaultSurfacesType(), $surface);
        $success = true;

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $service->persist($surface);
            } else {
                $success = false;
            }
        }

        $formTemplate = $this->renderView('DigipRenovationBundle:Defaults:update-default-surface.html.twig', array(
            'form'          => $form->createView(),
            'surface'        => $surface,
        ));

        // return a JSON response with the rendered form HTML as a property
        return new JsonResponse(array(
            'success'   => $success,
            'template'  => $formTemplate
        ));
    }

    public function updateDefaultRoofAction($id, Request $request)
    {
        $service = $this->get('digip_reno.service.defaults');

        $surface = $service->getRoofById($id);

        $form = $this->createForm(new DefaultRoofSurfaceType(), $surface);
        $success = true;

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $service->persist($surface);
            } else {
                $success = false;
            }
        }

        $formTemplate = $this->renderView('DigipRenovationBundle:Defaults:update-default-roof.html.twig', array(
            'form'          => $form->createView(),
            'surface'        => $surface,
        ));

        // return a JSON response with the rendered form HTML as a property
        return new JsonResponse(array(
            'success'   => $success,
            'template'  => $formTemplate
        ));
    }
}
