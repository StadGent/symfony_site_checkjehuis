<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @package App
 *
 * @ORM\Entity
 * @ORM\Table(name="renewables")
 */
class Renewable
{
    const RENEWABLE_SOLAR_WATER_HEATER = 'solar_water_heater';
    const RENEWABLE_SOLAR_PANELS = 'solar_panels';
    const RENEWABLE_GREEN_POWER = 'green_power';

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string")
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
     */
    protected $value;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $unit;

    /**
     * @var BuildCost
     * @ORM\ManyToOne(targetEntity="BuildCost", inversedBy="relatedRenewables")
     * @ORM\JoinColumn(name="relatedCost_id", referencedColumnName="id")
     */
    protected $cost;

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
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     * @return $this
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @param mixed $unit
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

    /**
     * @return BuildCost
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * @param mixed $cost
     * @return $this
     */
    public function setCost($cost)
    {
        $this->cost = $cost;
        return $this;
    }
}
