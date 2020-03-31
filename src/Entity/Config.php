<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @package App
 *
 * @ORM\Entity
 * @ORM\Table(name="config_choices")
 */
class Config
{
    const CONFIG_ATTIC_FLOOR = 42;

    /**
     * Returns the possible unit options for matrix values.
     *
     * @return array
     */
    public static function getUnits()
    {
        return array(
            '%' => '%',
            'kWh/jaar' => 'kWh/jaar',
            'kWhPiek' => 'kWhPiek',
        );
    }

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var ConfigCategory
     * @ORM\ManyToOne(targetEntity="ConfigCategory", inversedBy="configs")
     */
    protected $category;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $ordering;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $label;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $default;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $defaultUpToYear;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $possibleCurrent;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $possibleUpgrade;

    /**
     * @var ConfigTransformation[]
     * @ORM\OneToMany(targetEntity="ConfigTransformation", mappedBy="fromConfig")
     */
    protected $transformations;

    /**
     * @var ConfigTransformation[]
     * @ORM\OneToMany(targetEntity="ConfigTransformation", mappedBy="toConfig")
     */
    protected $transformationEnds;

    /**
     * @var BuildCost
     * @ORM\ManyToOne(targetEntity="BuildCost", inversedBy="relatedConfigs")
     * @ORM\JoinColumn(name="relatedCost_id", referencedColumnName="id")
     */
    protected $cost;

    /**
     * @var Subsidy[]
     * @ORM\ManyToMany(targetEntity="Subsidy", inversedBy="configs")
     * @ORM\JoinTable(name="config_subsidies",
     *      joinColumns={@ORM\JoinColumn(name="config_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="subsidy_id", referencedColumnName="id")}
     *      )
     */
    protected $subsidies;

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
     * Replaces some string with abbreviations to shorten the length of the
     * label so it's less bulky in table headings etc.
     *
     * @return string
     */
    public function getLabelAdmin()
    {
        // Replace or remove.
        $label = str_replace(
            [
                "centrale verwarming met", "Slecht geïsoleerd: ", "Matig geïsoleerd: ",
                "Goed geïsoleerd: ", "Perfect geïsoleerd: ", "op de zoldervloer",
                "enkel glas", "dubbel glas", "gewoon dubbel", "ventilatie",
            ],
            [
                "CV", "", "", "", "", "zoldervloer", "enkel", "dubbel", "dubbel", "",
            ],
            $this->label
        );

        // For insulation, only keep cm.
        if ($this->getCategory()->getSlug() == ConfigCategory::CAT_ROOF) {
            // Magic!
            $label = preg_replace('/(?i)(\D*)(\d* *cm)( of R=)\d(([\.,])*\d)*(m²k\/W)/', '$2', $label);
        }

        return trim($label);
    }

    /**
     * @return int
     */
    public function getOrdering()
    {
        return $this->ordering;
    }

    /**
     * @param int $ordering
     * @return $this
     */
    public function setOrdering($ordering)
    {
        $this->ordering = $ordering;
        return $this;
    }

    /**
     * @return ConfigCategory
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param string $category
     * @return $this
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @param string|ConfigCategory $category
     * @return bool
     */
    public function isCategory($category)
    {
        if ($category instanceof ConfigCategory) {
            $category = $category->getSlug();
        }

        return $this->getCategory()->getSlug() === $category;
    }

    /**
     * @return boolean
     */
    public function isDefault()
    {
        return $this->default;
    }

    /**
     * @param boolean $default
     * @return $this
     */
    public function setDefault($default)
    {
        $this->default = $default;
        return $this;
    }

    /**
     * @param bool $convertNull
     * @return string
     */
    public function getDefaultUpToYear($convertNull = false)
    {
        if ($convertNull && $this->defaultUpToYear == null) {
            return 3000;
        }
        return $this->defaultUpToYear;
    }

    /**
     * @param string $defaultUpToYear
     * @return $this
     */
    public function setDefaultUpToYear($defaultUpToYear)
    {
        $this->defaultUpToYear = $defaultUpToYear;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isPossibleCurrent()
    {
        return $this->possibleCurrent;
    }

    /**
     * @param boolean $possibleCurrent
     * @return $this
     */
    public function setPossibleCurrent($possibleCurrent)
    {
        $this->possibleCurrent = $possibleCurrent;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isPossibleUpgrade()
    {
        return $this->possibleUpgrade;
    }

    /**
     * @param boolean $possibleUpgrade
     * @return $this
     */
    public function setPossibleUpgrade($possibleUpgrade)
    {
        $this->possibleUpgrade = $possibleUpgrade;
        return $this;
    }

    /**
     * @return ConfigTransformation[]
     */
    public function getTransformations()
    {
        return $this->transformations;
    }

    /**
     * @param ConfigTransformation $transformations
     * @return $this
     */
    public function setTransformations($transformations)
    {
        $this->transformations = $transformations;
        return $this;
    }

    /**
     * @param Config $config
     * @param bool $inverse
     * @return ConfigTransformation|null
     */
    public function getTransformationFor(Config $config, $inverse = false)
    {
        foreach ($this->transformations as $t) {
            if ($t->getToConfig()->getId() == $config->getId() && $t->isInverse() == $inverse) {
                return $t;
            }
        }

        return null;
    }

    /**
     * @return BuildCost
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * @return Subsidy[]
     */
    public function getSubsidies()
    {
        return $this->subsidies;
    }

    /**
     * Check if this is a heat pump config, hardcoded on IDs.
     * Hardcoded?! Yes, hardcoded.
     *
     * @return bool
     */
    public function isHeatPumpConfig()
    {
        return in_array($this->getId(), array(
            37, 38, 40, 41
        ), true);
    }
}
