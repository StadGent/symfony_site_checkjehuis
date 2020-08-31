<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @package App
 *
 * @ORM\Entity
 * @ORM\Table(name="build_costs")
 */
class BuildCost
{
    const COST_WINDROOF = 'roof_wind';
    const COST_SOLAR_WATER_HEATER = Renewable::RENEWABLE_SOLAR_WATER_HEATER;
    const COST_SOLAR_PANELS = Renewable::RENEWABLE_SOLAR_PANELS;

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $ordering;

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
     * @ORM\Column(type="string", nullable=true)
     */
    protected $unit;

    /**
     * @var Config
     * @ORM\OneToMany(targetEntity="Config", mappedBy="cost")
     */
    protected $relatedConfigs;

    /**
     * @var Renewable
     * @ORM\OneToMany(targetEntity="Renewable", mappedBy="cost")
     */
    protected $relatedRenewables;

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
     * @param House $house
     * @param Config $config
     * @param array $options
     * @return float
     */
    public function getPrice(House $house, array $options = array())
    {
        $amount = 1;

        switch ($this->getSlug()) {
            case 'roof_18':
            case 'roof_24':
            case 'roof_30':
            case 'roof_wind':
                $amount = $house->getSurfaceRoof();
                if ($house->getRoofType() == House::ROOF_TYPE_MIXED) {
                    if ($options['roof-type'] == House::ROOF_TYPE_FLAT) {
                        $amount = $house->getSurfaceRoofExtra();
                    } elseif ($options['roof-type'] != House::ROOF_TYPE_INCLINED) {
                        throw new \InvalidArgumentException(
                            "Invalid roof-type config for a house with a mixed roof"
                        );
                    }
                }
                break;
            case 'attic':
                $amount = $house->getDefaultRoofIfFlat()->getSurface();
                if ($house->getRoofType() == House::ROOF_TYPE_MIXED) {
                    if ($options['roof-type'] == House::ROOF_TYPE_INCLINED) {
                        $amount = $amount * 0.7;
                    } elseif ($options['roof-type'] != House::ROOF_TYPE_INCLINED) {
                        throw new \InvalidArgumentException(
                            "Invalid roof-type config for a house with a mixed roof"
                        );
                    }
                }
                break;
            case 'facade':
            case 'facade_cavity':
            case 'facade_inner':
                $amount = $house->getSurfaceFacade();
                break;
            case 'floor':
            case 'basement':
                $amount = $house->getSurfaceFloor();
                break;
            case 'window_1_1':
            case 'window_0_8':
                $amount = $house->getSurfaceWindow();
                break;
            case 'solar_panels':
                $amount = $house->getSolarPanelsSurface();
                break;
        }

        return $this->getValue() * $amount;
    }
}
