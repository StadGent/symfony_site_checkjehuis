<?php

namespace Digip\RenovationBundle\Controller;

use Digip\RenovationBundle\Entity\House;
use Digip\RenovationBundle\Entity\Parameter;
use Digip\RenovationBundle\Form\DefaultEnergyType;
use Digip\RenovationBundle\Service\DefaultsService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SubsidyController extends Controller
{
    public function indexAction()
    {
        $service = $this->get('digip_reno.service.subsidy');
        $gentRoofMax = $this->get('digip_reno.service.parameter')->getParameterBySlug(Parameter::PARAM_SUBSIDY_GENT_ROOF);

        return $this->render('DigipRenovationBundle:Subsidy:index.html.twig', array(
            'subsidyCategories' => $service->getAllSubsidyCategories(),
            'subsidies'         => $service->getAllSubsidies(),
            'gentRoofMax'       => $gentRoofMax->getValue(),
        ));
    }

    public function updateAction(Request $request)
    {
        $service = $this->get('digip_reno.service.subsidy');

        if ($request->getMethod() == 'POST') {

            // update category labels
            foreach ($request->get('subsidy-cat-label') as $id => $label) {
                $cat = $service->getSubsidyCategory($id);
                $cat->setLabel($label);
                $service->persist($cat, false);
            }

            // save subsidy configs

            $values = $request->get('subsidy-value');
            $maximums = $request->get('subsidy-max');
            $multipliers = $request->get('subsidy-multiplier');

            foreach ($values as $id => $val) {

                $max = $maximums[$id];
                $max = $max ?: 0;

                $multiplier = $multipliers[$id];

                if (is_numeric($val) && (is_numeric($max) || is_null($max))) {
                    $subsidy = $service->getSubsidy($id);
                    $subsidy->setValue($val);
                    $subsidy->setMax($max);
                    $subsidy->setMultiplier($multiplier);
                    $service->persist($subsidy, false);
                }

            }

            $service->persist(true);
        }

        return $this->redirect($this->generateUrl('admin_subsidies'));
    }
}
