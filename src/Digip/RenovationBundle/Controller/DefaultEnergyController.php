<?php

namespace Digip\RenovationBundle\Controller;

use Digip\RenovationBundle\Entity\House;
use Digip\RenovationBundle\Form\DefaultEnergyType;
use Digip\RenovationBundle\Service\DefaultsService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultEnergyController extends Controller
{
    public function indexAction(Request $request)
    {
        /** @var DefaultsService $service */
        $service = $this->get('digip_reno.service.defaults');

        $filter = $request->get('table-filter', array());

        return $this->render('DigipRenovationBundle:DefaultEnergy:index.html.twig', array(
            'buildingTypes'     => House::getBuildingTypes(),
            'buildingSizes'     => House::getSizes(),
            'years'             => House::getYears(),
            'defaults'          => $service->getAllEnergy($filter),
            'filter'            => $filter,
        ));
    }

    public function updateDefaultEnergyAction($id, Request $request)
    {
        $service = $this->get('digip_reno.service.defaults');

        $energy = $service->getEnergyById($id);

        $form = $this->createForm(new DefaultEnergyType(), $energy);
        $success = true;

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $service->persist($energy);
            } else {
                $success = false;
            }
        }

        $formTemplate = $this->renderView('DigipRenovationBundle:DefaultEnergy:update-default-energy.html.twig', array(
            'form'          => $form->createView(),
            'energy'        => $energy,
        ));

        // return a JSON response with the rendered form HTML as a property
        return new JsonResponse(array(
            'success'   => $success,
            'template'  => $formTemplate
        ));
    }
}
