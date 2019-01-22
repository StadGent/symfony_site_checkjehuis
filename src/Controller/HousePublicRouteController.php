<?php

namespace App\Controller;

use App\Entity\House;
use App\Form\HouseEmailType;
use App\Form\MailPlanType;
use App\Service\HouseService;
use App\Service\MailService;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class HousePublicRouteController extends AbstractController
{

    /**
     * @Route("/callback-update/{token}", name="house_update_callback")
     */
    public function updateHouseCallbackAction($token, Request $request)
    {
        $isLoaded = $this->houseService->loadHouseFromToken($token);
        $return = array();

        if ($isLoaded) {
            $house = $this->getSessionHouse($request);
            if ($house) {

                // Set the solar panels' available surface.
                if ($request->query->has('solar_panels_surface')) {
                    $solarPanels = $request->get('solar_panels_surface', 0);
                    $house->setSolarPanelsSurface($solarPanels);
                    $return['set_solar_panels_surface'] = $solarPanels;
                }

                // Set the solar panels' power.
                if ($request->query->has('solar_panels_power')) {
                    $solarPanels = $request->get('solar_panels_power', 0);
                    $house->setSolarPanelsKWHPiek($solarPanels);
                    $return['set_solar_panels_kwh'] = $solarPanels;
                }


                // Set the address.
                if ($request->query->has('address')) {
                    $address = $request->get('address');
                    if (strlen($address) > 2) {
                        $house->setAddress($address);
                        $return['set_address'] = $address;
                    }
                }

                // Save changes.
                $this->houseService->saveHouse($house);
            }
            $return['success'] = true;
        } else {
            $return['error'] = 'house not found';
            $return['success'] = false;
        }

        $response = new JsonResponse($return);
        if (isset($house)) {
            $this->setGentCookies($response, $house);
        }

        return $response;
    }

    /**
     * @Route("/poll", name="house_poll")
     */
    public function pollAction(Request $request)
    {
        $house = $this->getSessionHouse($request);
        if (!$house) {
            return $this->noHouseRedirect();
        }

        $return = array('success' => true);

        $return['solar_panels'] = $house->getSolarPanelsSurface();

        return new JsonResponse($return);
    }

    /**
     * @Route("/laden/{token}", name="house_load_token")
     */
    public function loadHouseFromTokenAction($token, Request $request, RouterInterface $router)
    {
        $isLoaded = $this->houseService->loadHouseFromToken($token);

        // If we have a house, try redirect to the last know route.
        if ($isLoaded) {
            $house = $this->getSessionHouse($request);
            if ($house) {

                $lastRoute = $house->getLastKnownRoute();

                // Do we have a valid route?
                if ($lastRoute && $router->getRouteCollection()->get($lastRoute)) {
                    return $this->redirect($this->generateUrl($lastRoute));
                }
            }
        }

        return $this->render('house/load-from-token.html.twig', array(
            'loaded' => $isLoaded
        ));
    }

    /**
     * @Route("/mail-url", name="house_mail_token")
     */
    public function mailTokenUrlAction(Request $request, MailService $mailer)
    {
        $house = $this->getSessionHouse($request);

        if (!$house) {
            return $this->noHouseRedirect();
        }

        $form = $this->createForm(HouseEmailType::class, $house);

        if ($request->isMethod('post')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $mailer->mailHouseToken($house);
            }
        }

        return $this->render('house/mail-token.html.twig', array(
            'house' => $house,
            'host'  => $request->getHttpHost(),
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/mail-pdf", name="house_mail_pdf")
     */
    public function mailPdfAction(Request $request, MailService $mailer)
    {
        $house = $this->getSessionHouse($request);
        if (!$house) {
            return $this->noHouseRedirect();
        }

        $download = false;
        $request->get('with-cookie');
        $setCookie = true;
        if (!$request->cookies->has(self::COOKIE_USER_ADDRESS)) {
            if ($request->get('with-cookie') || $request->get('without-cookie')) {
                $setCookie = $request->get('with-cookie', false);
            }
        }

        if ($request->getMethod() == 'POST') {

            $form = $this->createForm(MailPlanType::class, $house);
            $form->handleRequest($request);

            if ($form->isValid()) {

                $this->houseService->saveHouse($house);
                $download = true;

                try {

                    $pdf = $this->houseService->generatePdf($house);
                    $mailer->mailCalculatorPdf(
                        $house,
                        $pdf
                    );

                    // Don't show pdf, we redirect with download enabled
                    // return new Response($pdf, 200, array('Content-Type' => 'application/pdf'));

                } catch (\Exception $e) {
                    error_log($e->getMessage());
                    error_log($e->getTraceAsString());

                    return $this->render('pdf/error.html.twig');
                }

            }

        }

        $response = $this->redirect($this->generateUrl('app_plan', array('download' => $download)));

        // save or update the address in a cookie so other apps on this domain can use it
        if ($setCookie) {
            $this->setGentCookies($response, $house);
        }

        return $response;
    }

    /**
     * @Route("/mail-herinnering", name="mail_reminder")
     */
    public function mailReminderAction(Request $request, MailService $mailer)
    {
        if ($request->getMethod() == 'POST') {

            $email = $request->get('email');
            $mailer->mailReminder($email);

        }

        return $this->render('page/mobile.html.twig', array(
            'mailed' => true
        ));
    }

    /**
     * @Route("/reset", name="house_reset")
     */
    public function resetAction(Request $request)
    {
        /** @var HouseService $houseService */
        $house = $this->houseService->loadHouse();
        if ($house) {
            $this->houseService->getEntityManager()->remove($house);
        }

        $request->getSession()->set(HouseService::HOUSE_SESSION_KEY, null);

        return $this->redirect($this->generateUrl('app_index'));
    }

    /**
     * Sets the user address cookie.
     *
     * @param Response $response
     *   The response to set the cookie header on.
     * @param House $house
     *   The house to set the cookie for.
     */
    protected function setGentCookies(Response $response, House $house)
    {
        $domain = $this->getParameter('gent_cookie_domain');
        $response->headers->setCookie(
            new Cookie(self::COOKIE_USER_ADDRESS, $house->getAddress(), 0, '/', $domain)
        );
    }
}
