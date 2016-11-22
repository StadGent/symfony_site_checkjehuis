<?php

namespace Digip\RenovationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @package Digip\RenovationBundle
 *
 * @ORM\Entity
 * @ORM\Table(name="parameters")
 */
class Parameter
{
    const PARAM_PRICE_ELEC          = 'price_electricity';
    const PARAM_PRICE_GAS           = 'price_gas';
    const PARAM_SOLAR_SURFACE       = 'solar_panel_surface';
    const PARAM_CO2_KWH             = 'co2_kwh';
    const PARAM_SUBSIDY_GENT_ROOF   = 'subsidy_ceiling_roof_gent';

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", unique=true)
     */
    protected $slug;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $label;

    /**
     * @var float
     * @ORM\Column(type="float")
     * @Assert\Type(type="numeric", message = "dit is geen geldige numerieke waarde")
     */
    protected $value;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $unit;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     * @return $this
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @param string $unit
     * @return $this
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;
        return $this;
    }

    /**
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param float $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
} 