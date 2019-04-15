<?php

namespace App\Calculator;

use App\Entity\ConfigCategory;
use App\Entity\House;
use App\Entity\Renewable;

class CalculatorView
{
    /**
     * @var House
     */
    protected $house;

    /**
     * @var CurrentEnergyCalculator
     */
    protected $current;

    /**
     * @var UpgradeEnergyCalculator
     */
    protected $upgrade;

    /**
     * @var BuildCostCalculator
     */
    protected $buildCosts;

    /**
     * @var SubsidyCalculator
     */
    protected $subsidies;

    /**
     * @var Parameters
     */
    protected $parameters;

    /**
     * Calculator view constructor.
     *
     * @param House $house
     *   The house.
     * @param CurrentEnergyCalculator $current
     *   The current energy calculator.
     * @param UpgradeEnergyCalculator $upgrade
     *   The upgrade energy calculator.
     * @param BuildCostCalculator $buildCosts
     *   The build cost calculator.
     * @param SubsidyCalculator $subsidies
     *   The subsidy calculator.
     * @param Parameters $parameters
     *   The parameters.
     */
    public function __construct(
        House $house,
        CurrentEnergyCalculator $current,
        UpgradeEnergyCalculator $upgrade,
        BuildCostCalculator $buildCosts,
        SubsidyCalculator $subsidies,
        Parameters $parameters
    ) {
        $this->house = $house;
        $this->current = $current;
        $this->upgrade = $upgrade;
        $this->buildCosts = $buildCosts;
        $this->subsidies = $subsidies;
        $this->parameters = $parameters;
    }

    /**
     * Get the average score.
     *
     * @param bool $current
     *   Whether or not to use the current state or the upgrade state.
     *
     * @return float|int
     */
    public function getAvgScore($current = false)
    {
        $state = $current ? $this->current->getState(): $this->upgrade->getState();

        // If we base our average on electricity, we need to deduct the non-
        // heating electricity costs first.
        if ($state->isHeatingElectric() || $state->forceElectricity() || !$state->getGas()) {
            $base = $state->getElectricity();
            $base -= $state->getNonHeatingElectricity();
        } else {
            $base = $state->getGas();
            // Add any extra electric costs.
            $base += ($state->getElectricity() - $state->getNonHeatingElectricity());
        }

        if ($base < 0) {
            return 0;
        }

        return round($base / $this->house->getSurfaceLivingArea());
    }

    /**
     * Get the average score config (used for the slider display).
     *
     * @param bool $current
     *   Whether or not to use the current or the upgrade config.
     *
     * @return array
     */
    public function getAvgScoreConfig($current = false)
    {
        $score = $this->getAvgScore($current);

        $maxVal = 150;
        $centerVal = 70;
        $minVal = 30;

        $centerGutter = 0;

        $minLeft = 18;
        $maxLeft = 51;
        $center = 55.5;
        $minRight = 59;
        $maxRight = 94;

        $value = $score;
        if ($value > $maxVal) {
            $value = $maxVal;
        }
        if ($value < $minVal) {
            $value = $minVal;
        }
        if ($value < ($centerVal + $centerGutter) && $value > ($centerVal - $centerGutter)) {
            $value = $centerVal;
        }

        // Calculate the position.
        $position = $center;
        $isCentered = true;
        $align = 'right';

        $points = null;
        $maxPoints = null;

        if ($value > $centerVal) {
            $isCentered = false;
            $maxPoints = $maxVal - $centerVal;
            $points = $value - $centerVal;

            $position = $maxLeft - round(($maxLeft - $minLeft) * ($points / $maxPoints));
        }
        if ($value < $centerVal) {
            $isCentered = false;
            $align = 'left';
            $maxPoints = $centerVal - $minVal;
            $points = $centerVal - $value;

            $position = $minRight + round(($maxRight - $minRight) * ($points / $maxPoints));
        }

        return array(
            'score'     => $score,
            'position'  => $position,
            'centered'  => $isCentered,
            'align'     => $align,
            'label'     => $this->getAvgScoreLabel($current),
        );
    }

    /**
     * Get the average score label.
     *
     * @param bool $current
     *   Whether or not to use the current or the upgrade config.
     *
     * @return string
     */
    public function getAvgScoreLabel($current = false)
    {
        $score = $this->getAvgScore($current);
        if ($score <= 30) {
            return 'Zo wil ik wonen!';
        }
        if ($score <= 70) {
            return 'Ik ben goed bezig, wat kan ik nog doen?';
        }
        else {
            return 'Mijn huis verliest teveel warmte';
        }
    }

    /**
     * Get the price difference.
     *
     * @return float|int
     */
    public function getPriceDiff()
    {
        $total = 0;

        $from = $this->current->getState();
        $to = $this->upgrade->getState();

        $total += ($from->getGas() - $to->getGas()) * $this->parameters->getPriceGas();
        $total += ($from->getElectricity() - $to->getElectricity()) * $this->parameters->getPriceElec();

        return $total;
    }

    /**
     * Get the energy difference.
     *
     * @return float
     */
    public function getEnergyDiff()
    {
        $from = $this->current->getState();
        $to = $this->upgrade->getState();

        return ($from->getGas() - $to->getGas()) + ($from->getElectricity() - $to->getElectricity());
    }

    /**
     * Get the energy difference for a category.
     *
     * @param string|ConfigCategory $cat
     *   The category.
     *
     * @return float
     */
    public function getEnergyDiffForCategory($cat)
    {
        if ($cat instanceof ConfigCategory) {
            $cat = $cat->getSlug();
        }

        $total = 0;

        foreach ($this->upgrade->getDiffs() as $diff) {
            if ($diff->getSubject()->getSlug() === $cat) {
                $total += $diff->getGas() + $diff->getElec();
            }
        }

        return $total;
    }

    /**
     * Get the price difference for a category.
     *
     * @param string|ConfigCategory $cat
     *   The category.
     *
     * @return float
     */
    public function getPriceDiffForCategory($cat)
    {
        if ($cat instanceof ConfigCategory) {
            $cat = $cat->getSlug();
        }

        $total = 0;

        foreach ($this->upgrade->getDiffs() as $diff) {
            if ($diff->getSubject()->getSlug() === $cat) {
                $total +=
                    ($diff->getGas() * $this->parameters->getPriceGas())
                    +
                    ($diff->getElec() * $this->parameters->getPriceElec())
                ;
                if ($cat === ConfigCategory::CAT_ROOF) {
                    $total += $this->getPriceDiffForCategory(ConfigCategory::CAT_WIND_ROOF);
                }
            }
        }

        return $total;
    }

    /**
     * Get the CO2 difference.
     *
     * @return float
     */
    public function getCo2Diff()
    {
        return $this->upgrade->getState()->getCo2();
    }

    /**
     * Get the CO2 difference for a category.
     *
     * @param string|ConfigCategory $cat
     *   The category.
     *
     * @return float
     */
    public function getCo2DiffForCategory($cat)
    {
        return $this->getEnergyDiffForCategory($cat) * $this->parameters->getCo2PerKwh();
    }

    /**
     * Get the energy difference for renewables.
     *
     * @return float
     */
    public function getEnergyDiffForRenewables()
    {
        $total = 0;

        foreach ($this->upgrade->getDiffs() as $diff) {
            if ($diff->getSubject() instanceof Renewable) {
                $total += $diff->getGas() + $diff->getElec();
            }
        }

        return $total;
    }

    /**
     * Get the energy difference for renewables.
     *
     * @param string|Renewable $renewable
     *   The renewable.
     *
     * @return float
     */
    public function getEnergyDiffForRenewable($renewable)
    {
        if ($renewable instanceof Renewable) {
            $renewable = $renewable->getSlug();
        }

        $total = 0;

        foreach ($this->upgrade->getDiffs() as $diff) {
            if ($diff->getSubject() instanceof Renewable && $diff->getSubject()->getSlug() === $renewable) {
                $total += $diff->getGas() + $diff->getElec();
            }
        }

        return $total;
    }

    /**
     * Get the price difference for renewables.
     *
     * @return float
     */
    public function getPriceDiffForRenewables()
    {
        $total = 0;

        foreach ($this->upgrade->getDiffs() as $diff) {
            if ($diff->getSubject() instanceof Renewable) {
                $total += ($diff->getGas() * $this->parameters->getPriceGas()) + ($diff->getElec() * $this->parameters->getPriceElec());
            }
        }

        return $total;
    }

    /**
     * Get the price difference for a renewable.
     *
     * @param string|Renewable $renewable
     *   The renewable.
     *
     * @return float
     */
    public function getPriceDiffForRenewable($renewable)
    {
        if ($renewable instanceof Renewable) {
            $renewable = $renewable->getSlug();
        }

        $total = 0;

        foreach ($this->upgrade->getDiffs() as $diff) {
            if ($diff->getSubject() instanceof Renewable && $diff->getSubject()->getSlug() === $renewable) {
                $total += ($diff->getGas() * $this->parameters->getPriceGas()) + ($diff->getElec() * $this->parameters->getPriceElec());
            }
        }

        return $total;
    }

    /**
     * Get the CO2 difference for a renewable.
     *
     * @param string|Renewable $renewable
     *   The renewable.
     *
     * @return float
     */
    public function getCo2DiffForRenewable($renewable)
    {
        if ($renewable instanceof Renewable) {
            $renewable = $renewable->getSlug();
        }

        $total = 0;

        foreach ($this->upgrade->getDiffs() as $diff) {
            if ($diff->getSubject() instanceof Renewable && $diff->getSubject()->getSlug() === $renewable) {
                $total += $diff->getCo2();
            }
        }

        return $total;
    }

    /**
     * Get the build cost total.
     *
     * @return float
     */
    public function getBuildCostTotal()
    {
        return $this->buildCosts->getTotalPrice();
    }

    /**
     * Get the build cost total for a category.
     *
     * @param string|ConfigCategory $cat
     *   The category.
     *
     * @return float
     */
    public function getBuildCostTotalForCategory($cat)
    {
        if ($cat instanceof ConfigCategory) {
            $cat = $cat->getSlug();
        }

        $total = 0;

        foreach ($this->buildCosts->getCategories() as $category => $price) {
            if ($category === $cat) {
                $total += $price;
            }

            if ($cat === ConfigCategory::CAT_ROOF && $category === $cat) {
                $total += $price;
            }
        }

        return $total;
    }

    /**
     * Get the build cost for a renewable.
     *
     * @param string|Renewable $renewable
     *   The renewable.
     *
     * @return float
     */
    public function getBuildCostForRenewable($renewable)
    {
        if ($renewable instanceof Renewable) {
            $renewable = $renewable->getSlug();
        }

        $total = 0;

        foreach ($this->buildCosts->getRenewables() as $slug => $price) {
            if ($renewable === $slug) {
                $total += $price;
            }
        }

        return $total;
    }

    /**
     * Get the subsidy total.
     *
     * @return float
     */
    public function getSubsidyTotal()
    {
        return $this->subsidies->getTotalPrice();
    }

    /**
     * Get the subsidy total for a category.
     *
     * @param string|ConfigCategory $cat
     *   The category.
     *
     * @return float
     */
    public function getSubsidyTotalForCategory($cat)
    {
        if ($cat instanceof ConfigCategory) {
            $cat = $cat->getSlug();
        }

        $total = 0;

        foreach ($this->subsidies->getCategories() as $category => $subsidies) {
            if ($category === $cat) {
                $total += array_sum($subsidies);
            }

            if ($cat === ConfigCategory::CAT_ROOF && $category === ConfigCategory::CAT_WIND_ROOF) {
                $total += array_sum($subsidies);
            }
        }

        return $total;
    }

    /**
     * Get subsidies for a category.
     *
     * @param string|ConfigCategory $cat
     *   The category.
     *
     * @return \Generator
     */
    public function getSubsidiesForCategory($cat)
    {
        if ($cat instanceof ConfigCategory) {
            $cat = $cat->getSlug();
        }

        foreach ($this->subsidies->getCategories() as $category => $subsidies) {
            if ($category === $cat) {
                foreach ($subsidies as $id => $price) {
                    yield $id => $price;
                }
            }
        }
    }

    /**
     * Get subsidies for a renewable.
     *
     * @param string|Renewable $renewable
     *   The renewable.
     *
     * @return float[]
     */
    public function getSubsidiesForRenewable($renewable)
    {
        if ($renewable instanceof Renewable) {
            $renewable = $renewable->getSlug();
        }

        $result = [];

        foreach ($this->subsidies->getRenewables() as $category => $subsidies) {
            if ($category === $renewable) {
                foreach ($subsidies as $id => $price) {
                    $result[$id] = $price;
                }
            }
        }

        return $result;
    }

    /**
     * Get the subsidy total for a renewable.
     *
     * @param string|Renewable $renewable
     *   The renewable.
     *
     * @return float
     */
    public function getSubsidyTotalForRenewable($renewable)
    {
        return array_sum($this->getSubsidiesForRenewable($renewable));
    }

    /**
     * Get the current state.
     *
     * @return CurrentEnergyCalculator
     */
    public function getCurrent()
    {
        return $this->current;
    }

    /**
     * Get the upgraded state.
     *
     * @return UpgradeEnergyCalculator
     */
    public function getUpgrade()
    {
        return $this->upgrade;
    }
}
