<?php

namespace Digip\RenovationBundle\Controller;

use Digip\RenovationBundle\Service\RenewablesService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RenewableController extends Controller
{
    public function indexAction()
    {
        /** @var RenewablesService $service */
        $service = $this->get('digip_reno.service.renewables');

        return $this->render('DigipRenovationBundle:Renewable:index.html.twig', array(
            'renewables' => $service->getAll(),
        ));
    }

    public function updateRenewablesAction(Request $request)
    {
        $service = $this->get('digip_reno.service.renewables');

        if ($request->getMethod() == 'POST') {

            $renewables = $request->get('renewable');

            foreach ($renewables as $id => $val) {
                if (is_numeric($val)) {
                    $renewable = $service->getRenewable($id);
                    $renewable->setValue($val);
                    $service->persist($renewable, false);
                }
            }

            $service->persist(true);
        }

        return $this->redirect($this->generateUrl('admin_renewables'));
    }
}
