<?php

namespace App\Controller;

use App\Entity\House;
use App\Form\DefaultRoofSurfaceType;
use App\Form\DefaultSurfacesType;
use App\Service\DefaultsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as FrameworkController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultsController extends FrameworkController
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
     * @Route("/admin/surfaces", name="admin_surfaces")
     */
    public function indexAction(Request $request)
    {

        $filter = $request->get('table-filter', array());

        return $this->render('defaults/index.html.twig', array(
            'buildingTypes'     => House::getBuildingTypes(),
            'roofTypes'         => House::getRoofTypes(),
            'buildingSizes'     => House::getSizes(),
            'defaults'          => $this->defaultsService->getAllSurfaces($filter),
            'defaultsRoof'      => $this->defaultsService->getAllRoofs($filter),
            'filter'            => $filter,
        ));
    }

    /**
     * @Route("/admin/surfaces/{id}", name="admin_surfaces_update")
     */
    public function updateDefaultsAction($id, Request $request)
    {
        $surface = $this->defaultsService->getSurfaceById($id);

        $form = $this->createForm(DefaultSurfacesType::class, $surface);
        $success = true;

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $this->defaultsService->persist($surface);
            } else {
                $success = false;
            }
        }

        $formTemplate = $this->renderView('defaults/update-default-surface.html.twig', array(
            'form'          => $form->createView(),
            'surface'        => $surface,
        ));

        // Return a JSON response with the rendered form HTML as a property.
        return new JsonResponse(array(
            'success'   => $success,
            'template'  => $formTemplate
        ));
    }

    /**
     * @Route("/admin/surfaces-roof/{id}", name="admin_surface_roof_update")
     */
    public function updateDefaultRoofAction($id, Request $request)
    {
        $surface = $this->defaultsService->getRoofById($id);

        $form = $this->createForm(DefaultRoofSurfaceType::class, $surface);
        $success = true;

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $this->defaultsService->persist($surface);
            } else {
                $success = false;
            }
        }

        $formTemplate = $this->renderView('defaults/update-default-roof.html.twig', array(
            'form'          => $form->createView(),
            'surface'        => $surface,
        ));

        // Return a JSON response with the rendered form HTML as a property.
        return new JsonResponse(array(
            'success'   => $success,
            'template'  => $formTemplate
        ));
    }
}
