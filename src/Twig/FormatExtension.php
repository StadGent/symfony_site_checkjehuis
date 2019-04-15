<?php

namespace App\Twig;

use App\Utility\Format;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class FormatExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new TwigFilter('f_price', array(Format::class, 'price')),
            new TwigFilter('f_energy', array(Format::class, 'energy')),
            new TwigFilter('f_co2', array(Format::class, 'co2')),
        );
    }
}
