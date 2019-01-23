<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @package App
 *
 * @ORM\Entity
 * @ORM\Table(name="config_transformations")
 */
class ConfigTransformation
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Config
     * @ORM\ManyToOne(targetEntity="Config", inversedBy="transformations")
     */
    protected $fromConfig;

    /**
     * @var Config
     * @ORM\ManyToOne(targetEntity="Config", inversedBy="transformationEnds")
     */
    protected $toConfig;

    /**
     * @var float
     * @ORM\Column(type="float")
     * @Assert\Type(type="numeric", message = "dit is geen geldige waarde")
     */
    protected $value;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $unit;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $inverse;

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
     * @return Config
     */
    public function getFromConfig()
    {
        return $this->fromConfig;
    }

    /**
     * @param mixed $fromChoice
     * @return $this
     */
    public function setFromConfig($fromChoice)
    {
        $this->fromConfig = $fromChoice;
        return $this;
    }

    /**
     * @return Config
     */
    public function getToConfig()
    {
        return $this->toConfig;
    }

    /**
     * @param mixed $toChoice
     * @return $this
     */
    public function setToConfig($toChoice)
    {
        $this->toConfig = $toChoice;
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
     * @return boolean
     */
    public function isInverse()
    {
        return $this->inverse;
    }

    /**
     * @param boolean $inverse
     * @return $this
     */
    public function setInverse($inverse)
    {
        $this->inverse = $inverse;
        return $this;
    }

    public function getDiff($base, &$formula = null)
    {
        switch (strtolower($this->unit)) {
            case '%':
                $relevantPart = $base * ($this->getFromConfig()->getCategory()->getPercent() / 100);
                $percent = $this->getValue() / 100;
                $diff = $relevantPart * $percent;
                $formula = sprintf('%s * %s%% * %s%%', $base, $this->getFromConfig()->getCategory()->getPercent(), $this->getValue());
                break;
            case 'kwh/jaar':
            case 'kwh':
                $diff = $this->getValue();
                $formula = sprintf('+ %s %s', $this->getValue(), $this->unit);
                break;
            default:
                $diff = 0;
        }
        return $this->inverse ? 0 - $diff : $diff;
    }

    public function transform($energy, $base)
    {
        $diff = $this->getDiff($base);

        return $energy - $diff;
    }
}
