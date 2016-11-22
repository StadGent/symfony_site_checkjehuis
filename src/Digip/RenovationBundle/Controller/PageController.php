<?php

namespace Digip\RenovationBundle\Controller;

use Digip\RenovationBundle\Entity\Content;
use Digip\RenovationBundle\Form\MailPlanType;
use Symfony\Component\HttpFoundation\Request;

class PageController extends AbstractController
{
    public function indexAction()
    {
        $service = $this->get('digip_reno.service.content');
        $content = $service->getContentBySlug(Content::INTRO);

        return $this->render('DigipRenovationBundle:Page:index.html.twig', array(
            'content' => $content,
        ));
    }

    public function moreInfoAction()
    {
        $service = $this->get('digip_reno.service.content');
        $content = $service->getContentBySlug(Content::INFO);

        return $this->render('DigipRenovationBundle:Page:more-info.html.twig', array(
            'content' => $content,
        ));
    }

    public function planAction($download = false, Request $request)
    {
        $house = $this->getSessionHouse();

        if (!$house) return $this->noHouseRedirect();

        $form = $this->createForm(new MailPlanType(), $house);

        return $this->render('DigipRenovationBundle:Page:plan.html.twig', array(
            'form' => $form->createView(),
            'download' => $download,
            'hasAddressCookie' => $request->cookies->has(self::COOKIE_USER_ADDRESS),
        ));
    }

    public function mobileAction()
    {
        return $this->render('DigipRenovationBundle:Page:mobile.html.twig');
    }
}
