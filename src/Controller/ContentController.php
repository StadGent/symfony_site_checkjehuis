<?php

namespace App\Controller;

use App\Factory\HouseFactory;
use App\Form\ContentType;
use App\Service\ConfigService;
use App\Service\ContentService;
use App\Service\HouseService;
use App\Service\ParameterService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ContentController extends AbstractController
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
     * @param HouseService $houseService
     *   The house service.
     * @param ContentService $contentService
     *   The content service.
     * @param ParameterService $parameterService
     *   The parameter service.
     * @param HouseFactory $houseFactory
     *   The house factory.
     * @param ConfigService $configService
     *   The config service.
     */
    public function __construct(
        HouseService $houseService,
        ContentService $contentService,
        ParameterService $parameterService,
        HouseFactory $houseFactory,
        ConfigService $configService
    ) {
        parent::__construct($houseService, $contentService, $parameterService, $houseFactory);
        $this->configService = $configService;
    }

    /**
     * @Route("/admin/content", name="admin_content")
     */
    public function indexAction()
    {
        $contents = $this->contentService->getAllContent();
        $categories = $this->configService->getAllCategories();

        return $this->render('content/index.html.twig', array(
            'contents' => $contents,
            'categories' => $categories,
        ));
    }

    /**
     * @Route("/admin/content/edit/{slug}", name="admin_content_edit")
     */
    public function editBySlugAction(Request $request, $slug)
    {
        $content = $this->contentService->getContentBySlug($slug);

        $form = $this->createForm(ContentType::class, $content, ['allow_deactivation' => $content->canDeactivate()]);

        if ($request->isMethod('post')) {
            $form->handleRequest($request);
            if ($form->isValid()) {

                $this->contentService->persist($form->getData());

                return $this->redirect($this->generateUrl('admin_content'));
            }
        }

        return $this->render('content/edit.html.twig', array(
            'form' => $form->createView(),
            'content' => $content,
        ));
    }
}
