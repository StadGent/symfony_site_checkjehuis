<?php

namespace App\Controller;

use App\Entity\Content;
use App\Form\MailPlanType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     */
    public function indexAction()
    {
        return $this->render('page/index.html.twig', array(
            'content' => $this->contentService->getContentBySlug(Content::INTRO),
        ));
    }

    /**
     * @Route("/meer-info", name="app_info")
     */
    public function moreInfoAction()
    {
        return $this->render('page/more-info.html.twig', array(
            'content' => $this->contentService->getContentBySlug(Content::INFO),
        ));
    }

    /**
     * @Route("/plan/{download}", name="app_plan", defaults={"page"=false})
     */
    public function planAction($download = false, Request $request)
    {
        $house = $this->getSessionHouse($request);

        if (!$house) {
            return $this->noHouseRedirect();
        }

        $form = $this->createForm(MailPlanType::class, $house);

        return $this->render('page/plan.html.twig', array(
            'form' => $form->createView(),
            'download' => $download,
            'hasAddressCookie' => $request->cookies->has(self::COOKIE_USER_ADDRESS),
        ));
    }

    /**
     * @Route("/mobile", name="app_mobile")
     */
    public function mobileAction()
    {
        return $this->render('page/mobile.html.twig');
    }
}
