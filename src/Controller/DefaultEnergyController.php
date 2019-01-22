<?php

namespace App\Controller;

use App\Entity\House;
use App\Form\DefaultEnergyType;
use App\Service\DefaultsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as FrameworkController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultEnergyController extends FrameworkController
{
    /**
     * The defaults service.
     *
     * @var DefaultsService
     */
    protected $defaultsService;

    /**
     * Controller constructor.
     *
     * @param DefaultsService $defaultsService
     *   The defaults service.
     */
    public function __construct(DefaultsService $defaultsService)
    {
        $this->defaultsService = $defaultsService;
    }

    /**
     * @Route("/admin/energy", name="admin_energy")
     */
    public function indexAction(Request $request)
    {

        $filter = $request->get('table-filter', array());

        return $this->render('default_energy/index.html.twig', array(
            'buildingTypes'     => House::getBuildingTypes(),
            'buildingSizes'     => House::getSizes(),
            'years'             => House::getYears(),
            'defaults'          => $this->defaultsService->getAllEnergy($filter),
            'filter'            => $filter,
        ));
    }

    /**
     * @Route("/admin/energy/{id}", name="admin_energy_update")
     */
    public function updateDefaultEnergyAction($id, Request $request)
    {
        $energy = $this->defaultsService->getEnergyById($id);

        $form = $this->createForm(DefaultEnergyType::class, $energy);
        $success = true;

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $this->defaultsService->persist($energy);
            } else {
                $success = false;
            }
        }

        $formTemplate = $this->renderView('default_energy/update-default-energy.html.twig', array(
            'form'          => $form->createView(),
            'energy'        => $energy,
        ));

        // Return a JSON response with the rendered form HTML as a property.
        return new JsonResponse(array(
            'success'   => $success,
            'template'  => $formTemplate
        ));
    }
}
