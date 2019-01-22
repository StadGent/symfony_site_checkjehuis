<?php

namespace App\Service;

use App\Calculator\Parameters;
use App\Entity\Parameter;

class ParameterService extends AbstractService
{
    /**
     * Load all parameters.
     *
     * @return Parameter[]
     *   The parameters.
     */
    public function getAll()
    {
        return $this->entityManager
            ->getRepository(Parameter::class)
            ->findAll();
    }

    /**
     * Load a parameter by its id.
     *
     * @param int $id
     *   The parameter id.
     *
     * @return Parameter
     *   The parameter.
     */
    public function getParameter($id)
    {
        return $this->entityManager
            ->getRepository(Parameter::class)
            ->find($id);
    }

    /**
     * Load a parameter by its slug.
     *
     * @param string $slug
     *   The slug to load the parameter for.
     *
     * @return Parameter
     *   The parameter.
     */
    public function getParameterBySlug($slug)
    {
        return $this->entityManager
            ->getRepository(Parameter::class)
            ->findOneBy(array('slug' => $slug));
    }

    /**
     * Get the calculation parameters.
     *
     * @return Parameters
     *   The calculation parameters.
     */
    public function getCalculationParameters()
    {
        $params = new Parameters();

        $params->setPriceGas(
            $this->getParameterBySlug(Parameter::PARAM_PRICE_GAS)->getValue()
        );
        $params->setPriceElec(
            $this->getParameterBySlug(Parameter::PARAM_PRICE_ELEC)->getValue()
        );
        $params->setCo2PerKwh(
            $this->getParameterBySlug(Parameter::PARAM_CO2_KWH)->getValue()
        );

        return $params;
    }
}
