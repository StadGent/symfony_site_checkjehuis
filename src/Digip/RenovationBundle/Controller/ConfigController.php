<?php

namespace Digip\RenovationBundle\Controller;

use Digip\RenovationBundle\Entity\ConfigTransformation;
use Digip\RenovationBundle\Form\ConfigTransformationType;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Digip\RenovationBundle\Service\ConfigService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ConfigController extends Controller
{
    public function indexAction()
    {
        /** @var ConfigService $service */
        $service = $this->get('digip_reno.service.config');

        // ensure there are no duplicate configurations
        $service->removeDuplicateTransformations();

        return $this->render('DigipRenovationBundle:Config:index.html.twig', array(
            'categories' => $service->getAllCategories(),
        ));
    }

    public function updateCategoryPercentAction(Request $request)
    {
        $configService = $this->get('digip_reno.service.config');
        $data = array(
            'success' => true,
            'errors' => array(),
        );

        try {
            $configService->updateCategoryPercentBySlug(
                $request->get('slug'),
                $request->get('percent')
            );
        } catch (EntityNotFoundException $e) {
            error_log($e->getMessage());
            error_log($e->getTraceAsString());

            $data['success'] = false;
            $data['errors'][] = $e->getMessage();
        }

        return new JsonResponse($data);
    }

    public function updateConfigTransformationAction($configFrom, $configTo, $inverse = false, Request $request)
    {
        $configService = $this->get('digip_reno.service.config');

        $from = $configService->getConfig($configFrom);
        $to = $configService->getConfig($configTo);
        $transformation = $from->getTransformationFor($to, $inverse);

        if (!$transformation) {
            $transformation = new ConfigTransformation();
            $transformation
                ->setFromConfig($from)
                ->setToConfig($to)
                ->setInverse($inverse)
                ->setUnit('%');
        }

        $form = $this->createForm(new ConfigTransformationType(), $transformation);
        $success = true;

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $configService->updateConfigTransformation($transformation);
            } else {
                $success = false;
            }
        }

        $formTemplate = $this->renderView('DigipRenovationBundle:Config:update-config-transformation.html.twig', array(
            'form'          => $form->createView(),
            'configFrom'    => $from,
            'configTo'      => $to,
            'inverse'       => $inverse,
        ));

        // return a JSON response with the rendered form HTML as a property
        return new JsonResponse(array(
            'success'   => $success,
            'template'  => $formTemplate
        ));
    }

    public function labelsAction(Request $request)
    {
        /** @var ConfigService $service */
        $service = $this->get('digip_reno.service.config');
        $categories = $service->getAllCategories();

        if ($request->isMethod('POST')) {
            $data = $request->get('config');
            foreach ($categories as $cat) {
                foreach ($cat->getConfigs() as $c) {
                    if (isset($data[$c->getId()])) {
                        $c->setLabel($data[$c->getId()]);
                        $service->persist($c, false);
                    }
                }
            }
            $service->persist(true);
        }

        return $this->render('DigipRenovationBundle:Config:labels.html.twig', array(
            'categories' => $categories,
        ));
    }
}
