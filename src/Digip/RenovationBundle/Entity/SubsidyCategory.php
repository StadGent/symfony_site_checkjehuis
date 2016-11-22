<?php

namespace Digip\RenovationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @package Digip\RenovationBundle
 *
 * @ORM\Entity
 * @ORM\Table(name="subsidy_categories")
 */
class SubsidyCategory
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
    protected $label;

    /**
     * @var Subsidy[]
     * @ORM\OneToMany(targetEntity="Subsidy", mappedBy="subsidyCategory")
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
     * @return Subsidy[]
     */
    public function getSubsidies()
    {
        return $this->subsidies;
    }

    /**
     * @param Subsidy[] $subsidies
     * @return $this
     */
    public function setSubsidies($subsidies)
    {
        $this->subsidies = $subsidies;
        return $this;
    }
}