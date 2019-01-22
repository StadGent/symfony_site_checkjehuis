<?php

namespace App\Controller;

use App\Entity\ConfigTransformation;
use App\Form\ConfigTransformationType;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as FrameworkController;
use App\Service\ConfigService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ConfigController extends FrameworkController
{
    /**
     * The config service.
     *
     * @var ConfigService
     */
    protected $configService;

    /**
     * Controller constructor.
     *
     * @param ConfigService $configService
     *   The config service.
     */
    public function __construct(ConfigService $configService)
    {
        $this->configService = $configService;
    }

    /**
     * @Route("/admin", name="admin")
     * @Route("/admin/config", name="admin_matrix")
     */
    public function indexAction()
    {
        // Ensure there are no duplicate configurations.
        $this->configService->removeDuplicateTransformations();

        return $this->render('config/index.html.twig', array(
            'categories' => $this->configService->getAllCategories(),
        ));
    }

    /**
     * @Route("/admin/config-category/percent", name="admin_matrix_category_percent_update")
     */
    public function updateCategoryPercentAction(Request $request)
    {
        $data = array(
            'success' => true,
            'errors' => array(),
        );

        try {
            $this->configService->updateCategoryPercentBySlug(
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

    /**
     * @Route("/admin/config-transformation/{configFrom}/{configTo}/{inverse}", name="admin_matrix_transformation_update", defaults={"inverse"=false})
     */
    public function updateConfigTransformationAction(Request $request, $configFrom, $configTo, $inverse = false)
    {
        $from = $this->configService->getConfig($configFrom);
        $to = $this->configService->getConfig($configTo);
        $transformation = $from->getTransformationFor($to, $inverse);

        if (!$transformation) {
            $transformation = new ConfigTransformation();
            $transformation
                ->setFromConfig($from)
                ->setToConfig($to)
                ->setInverse($inverse)
                ->setUnit('%');
        }

        $form = $this->createForm(ConfigTransformationType::class, $transformation);
        $success = true;

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $this->configService->updateConfigTransformation($transformation);
            } else {
                $success = false;
            }
        }

        $formTemplate = $this->renderView('config/update-config-transformation.html.twig', array(
            'form'          => $form->createView(),
            'configFrom'    => $from,
            'configTo'      => $to,
            'inverse'       => $inverse,
        ));

        // Return a JSON response with the rendered form HTML as a property.
        return new JsonResponse(array(
            'success'   => $success,
            'template'  => $formTemplate
        ));
    }

    /**
     * @Route("/admin/config-labels", name="admin_config_labels")
     */
    public function labelsAction(Request $request)
    {
        $categories = $this->configService->getAllCategories();

        if ($request->isMethod('POST')) {
            $data = $request->get('config');
            foreach ($categories as $cat) {
                foreach ($cat->getConfigs() as $c) {
                    if (isset($data[$c->getId()])) {
                        $c->setLabel($data[$c->getId()]);
                        $this->configService->persist($c, false);
                    }
                }
            }
            $this->configService->persist(true);
        }

        return $this->render('config/labels.html.twig', array(
            'categories' => $categories,
        ));
    }
}
