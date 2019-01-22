<?php

namespace App\Controller;

use App\Entity\Parameter;
use App\Service\ParameterService;
use App\Service\SubsidyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as FrameworkController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SubsidyController extends FrameworkController
{

    /**
     * The subsidy service.
     *
     * @var SubsidyService
     */
    protected $subsidyService;

    /**
     * The parameter service.
     *
     * @var ParameterService
     */
    protected $parameterService;

    /**
     * Controller constructor.
     *
     * @param SubsidyService $subsidyService
     *   The subsidy service.
     * @param ParameterService $parameterService
     *   The parameter service.
     */
    public function __construct(SubsidyService $subsidyService, ParameterService $parameterService)
    {
        $this->subsidyService = $subsidyService;
        $this->parameterService = $parameterService;
    }

    /**
     * @Route("/admin/subsidies", name="admin_subsidies")
     */
    public function indexAction()
    {
        $gentRoofMax = $this->parameterService->getParameterBySlug(Parameter::PARAM_SUBSIDY_GENT_ROOF);

        return $this->render('subsidy/index.html.twig', array(
            'subsidyCategories' => $this->subsidyService->getAllSubsidyCategories(),
            'subsidies'         => $this->subsidyService->getAllSubsidies(),
            'gentRoofMax'       => $gentRoofMax->getValue(),
        ));
    }

    /**
     * @Route("/admin/subsidies/update", name="admin_subsidies_update")
     */
    public function updateAction(Request $request)
    {
        if ($request->getMethod() !== 'POST') {
            // Update category labels.
            foreach ($request->get('subsidy-cat-label') as $id => $label) {
                $cat = $this->subsidyService->getSubsidyCategory($id);
                $cat->setLabel($label);
                $this->subsidyService->persist($cat, false);
            }

            // Save subsidy configs.
            $maximums = $request->get('subsidy-max');
            $multipliers = $request->get('subsidy-multiplier');

            foreach ($request->get('subsidy-value') as $id => $val) {
                $max = $maximums[$id] ?: 0;
                $multiplier = $multipliers[$id];
                if (is_numeric($val) && (is_numeric($max) || is_null($max))) {
                    $subsidy = $this->subsidyService->getSubsidy($id);
                    $subsidy->setValue($val);
                    $subsidy->setMax($max);
                    $subsidy->setMultiplier($multiplier);
                    $this->subsidyService->persist($subsidy, false);
                }
            }
            $this->subsidyService->persist(true);
        }

        return $this->redirect($this->generateUrl('admin_subsidies'));
    }
}
