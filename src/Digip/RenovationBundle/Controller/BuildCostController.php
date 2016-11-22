<?php

namespace Digip\RenovationBundle\Controller;

use Digip\RenovationBundle\Service\BuildCostService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class BuildCostController extends Controller
{
    public function indexAction()
    {
        /** @var BuildCostService $service */
        $service = $this->get('digip_reno.service.buildcost');

        return $this->render('DigipRenovationBundle:BuildCost:index.html.twig', array(
            'costs' => $service->getAll(),
        ));
    }

    public function updateBuildCostsAction(Request $request)
    {
        $service = $this->get('digip_reno.service.buildcost');

        if ($request->getMethod() == 'POST') {

            $costs = $request->get('cost');

            foreach ($costs as $id => $val) {
                if (is_numeric($val)) {
                    $cost = $service->getCost($id);
                    $cost->setValue($val);
                    $service->persist($cost, false);
                }
            }

            $service->persist(true);
        }

        return $this->redirect($this->generateUrl('admin_buildcosts'));
    }
}
