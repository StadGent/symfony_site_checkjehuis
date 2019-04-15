<?php

namespace App\Controller;

use App\Service\BuildCostService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as FrameworkController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BuildCostController extends FrameworkController
{
    /**
     * The build cost service.
     *
     * @var BuildConstService
     */
    protected $buildCostService;

    /**
     * Controller constructor.
     *
     * @param BuildCostService $buildCostService
     *   The build cost service.
     */
    public function __construct(BuildCostService $buildCostService)
    {
        $this->buildCostService = $buildCostService;
    }

    /**
     * @Route("/admin/buildcosts", name="admin_buildcosts")
     */
    public function indexAction()
    {
        return $this->render('build_cost/index.html.twig', array(
            'costs' => $this->buildCostService->getAll(),
        ));
    }

    /**
     * @Route("/admin/buildcosts/update", name="admin_buildcosts_update")
     */
    public function updateBuildCostsAction(Request $request)
    {

        if ($request->getMethod() == 'POST') {

            $costs = $request->get('cost');

            foreach ($costs as $id => $val) {
                if (is_numeric($val)) {
                    $cost = $this->buildCostService->getCost($id);
                    $cost->setValue($val);
                    $this->buildCostService->persist($cost, false);
                }
            }

            $this->buildCostService->persist(true);
        }

        return $this->redirect($this->generateUrl('admin_buildcosts'));
    }
}
