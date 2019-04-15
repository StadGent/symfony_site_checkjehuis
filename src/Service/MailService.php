<?php

namespace App\Service;

use App\Entity\House;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\Templating\EngineInterface;

class MailService
{
    /**
     * Mailer service.
     *
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * Template engine.
     *
     * @var EngineInterface
     */
    protected $twig;

    /**
     * File locator.
     *
     * @var FileLocator
     */
    protected $fileLocator;

    /**
     * Mail service constructor.
     *
     * @param \Swift_Mailer $mailer
     *   Mailer service.
     * @param EngineInterface $twig
     *   Template engine.
     * @param FileLocator $fileLocator
     *   File locator.
     */
    public function __construct(\Swift_Mailer $mailer, EngineInterface $twig, FileLocator $fileLocator)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->fileLocator = $fileLocator;
    }

    /**
     * Get the contents of an asset.
     *
     * @param string $assetURI
     *   The uri to the asset.
     *
     * @return string
     *   The asset contents, base64 encoded.
     */
    protected function getAssetContent($assetURI)
    {
        return base64_encode(file_get_contents($this->getAssetPath(
            $assetURI
        )));
    }

    /**
     * Get the asset path.
     *
     * @param string $assetURI
     *   The asset uri.
     *
     * @return string
     *   The asset path.
     */
    protected function getAssetPath($assetURI)
    {
        return $this->fileLocator->locate(
            $assetURI
        );
    }

    /**
     * Mail the house token to the user.
     *
     * @param House $house
     *   The house to send the mail for.
     */
    public function mailHouseToken(House $house)
    {
        $mail = new \Swift_Message();

        $mail
            ->setContentType('text/html')
            ->setSubject('Check je huis')
            ->addFrom('energiecentrale@gent.be')
            ->setTo($house->getEmail())
            ->setBody(
                $this->twig->render('email/save-token.html.twig', array(
                    'house' => $house,
                ))
            )
        ;

        $this->mailer->send($mail);
    }

    /**
     * Mail the calculator pdf to the user.
     *
     * @param House $house
     *   The house to send the mail for.
     * @param string $pdf
     *   The binary string of the pdf.
     */
    public function mailCalculatorPdf(House $house, $pdf)
    {
        $mail = new \Swift_Message();

        $mail
            ->attach(new \Swift_Attachment($pdf, 'mijn-huis.pdf', 'application/pdf'))
            ->setSubject('check je huis: mijn stappenplan')
            ->addFrom('energiecentrale@gent.be')
            ->setTo($house->getEmail())
            ->setBody(
                $this->twig->render('email/plan-pdf.html.twig', array(
                    'house' => $house,
                ))
            )
            ->setContentType('text/html')
        ;

        $this->mailer->send($mail);
    }

    /**
     * Send a reminder mail.
     *
     * @param string $email
     *   The email address to send the reminder to.
     */
    public function mailReminder($email)
    {
        $mail = new \Swift_Message();

        $mail
            ->setContentType('text/html')
            ->setSubject('Check je huis')
            ->addFrom('energiecentrale@gent.be')
            ->setTo($email)
            ->setBody(
                $this->twig->render('email/reminder.html.twig')
            )
        ;

        $this->mailer->send($mail);
    }
}
