<?php

namespace Digip\RenovationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Digip\RenovationBundle\Service\ParameterService;
use Digip\RenovationBundle\Form\ParameterType;

class ParameterController extends Controller
{
    public function indexAction()
    {
        /** @var ParameterService $service */
        $service = $this->get('digip_reno.service.parameter');

        return $this->render('DigipRenovationBundle:Parameter:index.html.twig', array(
            'parameters' => $service->getAll(),
        ));
    }

    public function updateParametersAction(Request $request)
    {
        $paramService = $this->get('digip_reno.service.parameter');

        if ($request->getMethod() == 'POST') {

            $parameters = $request->get('parameter');

            foreach ($parameters as $id => $val) {
                if (is_numeric($val)) {
                    $parameter = $paramService->getParameter($id);
                    $parameter->setValue($val);
                    $paramService->persist($parameter, false);
                }
            }

            $paramService->persist(true);
        }

        return $this->redirect($this->generateUrl('admin_parameters'));
    }
}
