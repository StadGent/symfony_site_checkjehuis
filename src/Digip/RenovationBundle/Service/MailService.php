<?php

namespace Digip\RenovationBundle\Service;

use Digip\RenovationBundle\Entity\House;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Config\FileLocator;

class MailService extends AbstractService
{
    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var TwigEngine
     */
    protected $twig;

    /**
     * @var FileLocator
     */
    protected $fileLocator;

    public function __construct(\Swift_Mailer $mailer, TwigEngine $twig, FileLocator $fileLocator)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->fileLocator = $fileLocator;
    }

    protected function getAssetContent($assetURI)
    {
        return base64_encode(file_get_contents($this->getAssetPath(
            $assetURI
        )));
    }

    protected function getAssetPath($assetURI)
    {
        return $this->fileLocator->locate(
            $assetURI
        );
    }

    public function mailHouseToken(House $house)
    {
        $mail = \Swift_Message::newInstance();

        $mail
            ->setContentType('text/html')
            ->setSubject('Check je huis')
            ->addFrom('energiecentrale@gent.be')
            ->setTo($house->getEmail())
            ->setBody(
                $this->twig->render('DigipRenovationBundle:Email:save-token.html.twig', array(
                    'house'                 => $house,
                ))
            )
        ;

        $this->mailer->send($mail);
    }

    /**
     * @param House $house
     * @param string $pdf binary string
     */
    public function mailCalculatorPdf(House $house, $pdf)
    {
        $mail = \Swift_Message::newInstance();

        $mail
            ->attach(new \Swift_Attachment($pdf, 'mijn-huis.pdf', 'application/pdf'))
            ->setSubject('check je huis: mijn stappenplan')
            ->addFrom('energiecentrale@gent.be')
            ->setTo($house->getEmail())
            ->setBody(
                $this->twig->render('DigipRenovationBundle:Email:plan-pdf.html.twig', array(
                    'house'             => $house,
                ))
            )
            ->setContentType('text/html')
        ;

        $this->mailer->send($mail);
    }

    public function mailReminder($email)
    {
        $mail = \Swift_Message::newInstance();

        $mail
            ->setContentType('text/html')
            ->setSubject('Check je huis')
            ->addFrom('energiecentrale@gent.be')
            ->setTo($email)
            ->setBody(
                $this->twig->render('DigipRenovationBundle:Email:reminder.html.twig')
            )
        ;

        $this->mailer->send($mail);
    }
}