<?php

namespace Digip\RenovationBundle\Controller;

use Digip\RenovationBundle\Entity\Content;
use Digip\RenovationBundle\Form\ContentType;
use Symfony\Component\HttpFoundation\Request;

class ContentController extends AbstractController
{
    public function indexAction()
    {
        $contentService = $this->get('digip_reno.service.content');
        $contents = $contentService->getAllContent();
        $configService = $this->get('digip_reno.service.config');
        $categories = $configService->getAllCategories();

        return $this->render('DigipRenovationBundle:Content:index.html.twig', array(
            'contents' => $contents,
            'categories' => $categories,
        ));
    }

    public function editBySlugAction(Request $request)
    {
        $service = $this->get('digip_reno.service.content');
        $content = $service->getContentBySlug($request->get('slug'));

        $form = $this->createForm(new ContentType($content->canDeactivate()), $content);

        if ($request->isMethod('post')) {
            $form->handleRequest($request);
            if ($form->isValid()) {

                $service->persist($form->getData());

                return $this->redirect($this->generateUrl('admin_content'));
            }
        }

        return $this->render('DigipRenovationBundle:Content:edit.html.twig', array(
            'form' => $form->createView(),
            'content' => $content,
        ));
    }
}
