<?php

namespace Digip\RenovationBundle\Twig;

class FormatExtension extends \Twig_Extension
{
    public function getFilters()
    {
        $formatter = 'Digip\RenovationBundle\Utility\Format';

        return array(
            new \Twig_SimpleFilter('f_price', array($formatter, 'price')),
            new \Twig_SimpleFilter('f_energy', array($formatter, 'energy')),
            new \Twig_SimpleFilter('f_co2', array($formatter, 'co2')),
        );
    }

    public function getName()
    {
        return 'digip_reno_format_extension';
    }
}