<?php

namespace App\Controller;

use App\Service\RenewablesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as FrameworkController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RenewableController extends FrameworkController
{
    /**
     * The renewables service.
     *
     * @var RenewablesService
     */
    protected $renewablesService;

    /**
     * Controller constructor.
     *
     * @param RenewablesService $renewablesService
     *   The renewables service.
     */
    public function __construct(RenewablesService $renewablesService)
    {
        $this->renewablesService = $renewablesService;
    }

    /**
     * @Route("/admin/renewables", name="admin_renewables")
     */
    public function indexAction()
    {

        return $this->render('renewable/index.html.twig', array(
            'renewables' => $this->renewablesService->getAll(),
        ));
    }

    /**
     * @Route("/admin/renewables/update", name="admin_renewables_update")
     */
    public function updateRenewablesAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $renewables = $request->get('renewable');

            foreach ($renewables as $id => $val) {
                if (is_numeric($val)) {
                    $renewable = $this->renewablesService->getRenewable($id);
                    $renewable->setValue($val);
                    $this->renewablesService->persist($renewable, false);
                }
            }

            $this->renewablesService->persist(true);
        }

        return $this->redirect($this->generateUrl('admin_renewables'));
    }
}
