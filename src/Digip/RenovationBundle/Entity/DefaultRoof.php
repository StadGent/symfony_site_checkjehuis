<?php

namespace Digip\RenovationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @package Digip\RenovationBundle
 *
 * @ORM\Entity
 * @ORM\Table(name="default_roofs")
 */
class DefaultRoof
{
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
    protected $type;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $size;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $inclined;

    /**
     * @var float
     * @ORM\Column(type="float")
     * @Assert\Type(type="numeric", message = "dit is geen geldige waarde")
     */
    protected $surface;

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
    public function getInclined()
    {
        return $this->inclined;
    }

    /**
     * @param string $inclined
     * @return $this
     */
    public function setInclined($inclined)
    {
        $this->inclined = $inclined;
        return $this;
    }

    /**
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param string $size
     * @return $this
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return float
     */
    public function getSurface()
    {
        return $this->surface;
    }

    /**
     * @param float $surface
     * @return $this
     */
    public function setSurface($surface)
    {
        $this->surface = $surface;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function getInclinationString()
    {
        $types = House::getRoofTypes();

        if (array_key_exists($this->inclined, $types)) return $types[$this->inclined];

        return '';
    }
} 