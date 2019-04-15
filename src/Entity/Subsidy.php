<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @package App
 *
 * @ORM\Entity
 * @ORM\Table(name="subsidies")
 */
class Subsidy
{
    const MULTIPLIER_SURFACE = 'surface';
    const MULTIPLIER_COST = 'cost';
    const MULTIPLIER_NONE = 'none';

    const SUBSIDY_WINDROOF = 'roof_wind';
    const SUBSIDY_SOLAR_HEATER = 'solar_heater';

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
     * @var SubsidyCategory
     * @ORM\ManyToOne(targetEntity="SubsidyCategory", inversedBy="subsidies")
     */
    protected $subsidyCategory;

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
    protected $multiplier;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $max;

    /**
     * @var Config[]
     * @ORM\ManyToMany(targetEntity="Config", mappedBy="subsidies")
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
     * @return SubsidyCategory
     */
    public function getCategory()
    {
        return $this->subsidyCategory;
    }

    /**
     * @param SubsidyCategory $subsidyCategory
     * @return $this
     */
    public function setCategory($subsidyCategory)
    {
        $this->subsidyCategory = $subsidyCategory;
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
     * @param Config[] $configs
     * @return $this
     */
    public function setConfigs($configs)
    {
        $this->configs = $configs;
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
     * @return int
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * @param int $max
     * @return $this
     */
    public function setMax($max)
    {
        $this->max = $max;
        return $this;
    }

    /**
     * @return string
     */
    public function getMultiplier()
    {
        return $this->multiplier;
    }

    /**
     * @param string $multiplier
     * @return $this
     */
    public function setMultiplier($multiplier)
    {
        $this->multiplier = $multiplier;
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

    public function getPrice(House $house, BuildCost $cost = null, array $options = array())
    {
        $amount = 1;
        $subsidy = 0;

        switch ($this->multiplier) {
            case 'surface':
                $subsidy = $this->getValue();
                switch ($this->slug) {
                    case 'roof_18':
                    case 'roof_24':
                    case 'roof_30':
                        $amount = $house->getSurfaceRoof();
                        if ($house->getRoofType() === House::ROOF_TYPE_MIXED) {
                            if ($options['roof-type'] === House::ROOF_TYPE_INCLINED) {
                                $amount = $house->getSurfaceRoof();
                            } elseif ($options['roof-type'] === House::ROOF_TYPE_FLAT) {
                                $amount = $house->getSurfaceRoofExtra();
                            } else {
                                throw new \InvalidArgumentException(
                                    'Invalid roof-type config for a house with a mixed roof'
                                );
                            }
                        }
                        break;
                    case 'roof_wind':
                        $amount = 0;
                        if ($house->getRoofType() !== House::ROOF_TYPE_FLAT) {
                            $amount = $house->getSurfaceRoof();
                        }
                        break;
                    case 'attic_floor':
                        $amount = $house->getSurfaceRoof(true, $house->getUpgradeConfig(ConfigCategory::CAT_ROOF));
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
                }
                break;
            case 'cost':
                if (!$cost) {
                    throw new \InvalidArgumentException('This subsidy requires a BuildCost: ' . $this->getId());
                }
                $subsidy = $cost->getPrice($house, $options);
                $amount = str_replace('%', '', $this->getValue()) / 100;
                break;
            case 'none':
                $subsidy = $this->getValue();
                break;
        }

        $price = $subsidy * $amount;

        if ($this->getMax() && $price > $this->getMax()) {
            return $this->getMax();
        }

        return $price;
    }

    /**
     * @return bool
     */
    public function isRoofRelated()
    {
        if ($this->getSlug() === Subsidy::SUBSIDY_WINDROOF) {
            return true;
        }

        foreach ($this->configs as $c) {
            if ($c->getCategory()->getSlug() === ConfigCategory::CAT_ROOF) {
                return true;
            }
        }

        return false;
    }
}
