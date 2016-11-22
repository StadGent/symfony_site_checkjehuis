<?php

namespace Digip\RenovationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @package Digip\RenovationBundle
 *
 * @ORM\Entity
 * @ORM\Table(name="config_categories")
 */
class ConfigCategory
{
    const CAT_ROOF          = 'roof';
    const CAT_FACADE        = 'facade';
    const CAT_FLOOR         = 'floor';
    const CAT_WINDOWS       = 'window';
    const CAT_VENTILATION   = 'ventilation';
    const CAT_HEATING       = 'heating';
    const CAT_HEATING_ELEC  = 'heating_elec';

    /**
     * pseudo category, not in database
     */
    const CAT_WIND_ROOF     = 'wind_roof';

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
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $ordering;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $hasInverseMatrix;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $fromActual;

    /**
     * @var float
     * @ORM\Column(type="float")
     */
    protected $percent;

    /**
     * @var Config[]
     * @ORM\OneToMany(targetEntity="Config", mappedBy="category")
     * @ORM\OrderBy({"ordering" = "ASC"})
     */
    protected $configs;

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
     * @return Config[]
     */
    public function getConfigs()
    {
        return $this->configs;
    }

    /**
     * @return Config
     * @throws \RuntimeException
     */
    public function getBaseConfig()
    {
        foreach ($this->configs as $c) {
            if ($c->getOrdering() == 1) return $c;
        }
        throw new \RuntimeException('no base config for category: ' . $this->getSlug());
    }

    /**
     * @param mixed $configs
     * @return $this
     */
    public function setConfigs($configs)
    {
        $this->configs = $configs;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isFromActual()
    {
        return $this->fromActual;
    }

    /**
     * @param boolean $fromActual
     * @return $this
     */
    public function setFromActual($fromActual)
    {
        $this->fromActual = $fromActual;
        return $this;
    }

    /**
     * @return boolean
     */
    public function hasInverseMatrix()
    {
        return $this->hasInverseMatrix;
    }

    /**
     * @param boolean $hasInverseMatrix
     * @return $this
     */
    public function setHasInverseMatrix($hasInverseMatrix)
    {
        $this->hasInverseMatrix = $hasInverseMatrix;
        return $this;
    }

    /**
     * @return float
     */
    public function getPercent()
    {
        return $this->percent;
    }

    /**
     * @param float $percent
     * @return $this
     */
    public function setPercent($percent)
    {
        $this->percent = $percent;
        return $this;
    }

    public function getIconAsset(House $house = null)
    {
        $dir = 'bundles/digiprenovation/images/icons/';
        $standard = $dir . 'house-' . $this->getSlug() . '.svg';

        switch ($this->getSlug()) {
            case 'facade':
            case 'window':
            case 'floor':
            case 'ventilation':
            case 'heating':
            case 'renewable':
                return $standard;
            case 'roof':
                if ($house) {
                    if ($house->getRoofType() == House::ROOF_TYPE_FLAT)
                        return $dir . 'house-roof-flat.svg';
                    if ($house->getRoofType() == House::ROOF_TYPE_INCLINED)
                        return $dir . 'house-roof-inclined.svg';
                    if ($house->getRoofType() == House::ROOF_TYPE_MIXED) {
                        return $dir . 'house-roof-mixed.svg';
                    }
                }
                return $dir . 'house-roof.svg';
            case 'heating_elec':
                return $dir . 'house-heating.svg';
        }

        return $dir . 'house.svg';
    }
}
