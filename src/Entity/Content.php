<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @package App
 *
 * @ORM\Entity
 * @ORM\Table(name="content")
 */
class Content
{
    const INTRO = 'intro';
    const INFO = 'info';

    const HEAT_PUMP_NOT_ALLOWED = 'heat_pump_not_allowed';

    const ONE_TYPE = '1_type';
    const ONE_YEAR = '1_year';
    const ONE_ROOF = '1_roof';
    const ONE_SURFACE = '1_surface';
    const ONE_OWNER = '1_owner';
    const ONE_OCCUPANTS = '1_occupants';
    const ONE_ENERGY_AVG = '1_energy_avg';
    const ONE_ENERGY_CUSTOM = '1_energy_custom';

    const TWO_ROOF = '2_roof';
    const TWO_HEAT_MAP = '2_heat_map';
    const TWO_FACADE = '2_facade';
    const TWO_FLOOR = '2_floor';
    const TWO_WINDOW = '2_window';
    const TWO_VENTILATION = '2_ventilation';
    const TWO_HEATING = '2_heating';
    const TWO_RENEWABLE = '2_renewable';

    const THREE_ROOF = '3_roof';
    const THREE_HEAT_MAP = '3_heat_map';
    const THREE_FACADE = '3_facade';
    const THREE_FLOOR = '3_floor';
    const THREE_WINDOW = '3_window';
    const THREE_VENTILATION = '3_ventilation';
    const THREE_HEATING = '3_heating';
    const THREE_RENEWABLE = '3_renewable';

    const PREMIUM_ROOF = 'premium_roof';
    const PREMIUM_FACADE = 'premium_facade';
    const PREMIUM_FLOOR = 'premium_floor';
    const PREMIUM_WINDOW = 'premium_window';
    const PREMIUM_VENTILATION = 'premium_ventilation';
    const PREMIUM_HEATING = 'premium_heating';
    const PREMIUM_RENEWABLES = 'premium_renewables';

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
     * @var string
     * @ORM\Column(type="text")
     */
    protected $value = ' ';

    /**
     * @var bool
     * @ORM\Column(type="boolean");
     */
    protected $canDeactivate;

    /**
     * @var bool
     * @ORM\Column(type="boolean");
     */
    protected $active;

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
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     * @return $this
     */
    public function setActive($active)
    {
        if ($this->canDeactivate) {
            $this->active = $active;
        }

        return $this;
    }

    /**
     * @return boolean
     */
    public function canDeactivate()
    {
        return $this->canDeactivate;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param mixed $label
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
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
}
