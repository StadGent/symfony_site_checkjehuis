<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as FrameworkController;
use Symfony\Component\HttpFoundation\Request;
use App\Service\ParameterService;
use Symfony\Component\Routing\Annotation\Route;

class ParameterController extends FrameworkController
{
    /**
     * The parameter service.
     *
     * @var ParameterService
     */
    protected $parameterService;

    /**
     * Controller constructor.
     *
     * @param ParameterService $parameterService
     *   The parameter service.
     */
    public function __construct(ParameterService $parameterService)
    {
        $this->parameterService = $parameterService;
    }

    /**
     * @Route("/admin/parameters", name="admin_parameters")
     */
    public function indexAction()
    {

        return $this->render('parameter/index.html.twig', array(
            'parameters' => $this->parameterService->getAll(),
        ));
    }

    /**
     * @Route("/admin/parameters/update", name="admin_parameters_update")
     */
    public function updateParametersAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {

            $parameters = $request->get('parameter');

            foreach ($parameters as $id => $val) {
                if (is_numeric($val)) {
                    $parameter = $this->parameterService->getParameter($id);
                    $parameter->setValue($val);
                    $this->parameterService->persist($parameter, false);
                }
            }

            $this->parameterService->persist(true);
        }

        return $this->redirect($this->generateUrl('admin_parameters'));
    }
}
