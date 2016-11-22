<?php

namespace Digip\RenovationBundle\Service;

use Digip\RenovationBundle\Calculator\Parameters;
use Digip\RenovationBundle\Entity\Parameter;

class ParameterService extends AbstractService
{
    /**
     * @return Parameter[]
     */
    public function getAll()
    {
        $repo = $this->getDoctrine()->getRepository('DigipRenovationBundle:Parameter');

        return $repo->findAll();
    }

    /**
     * @param int $id
     * @return Parameter
     */
    public function getParameter($id)
    {
        $em = $this->getDoctrine();
        $repo = $em->getRepository('DigipRenovationBundle:Parameter');

        return $repo->find($id);
    }

    /**
     * @param string $slug
     * @return Parameter
     */
    public function getParameterBySlug($slug)
    {
        $em = $this->getDoctrine();
        $repo = $em->getRepository('DigipRenovationBundle:Parameter');

        return $repo->findOneBy(array('slug' => $slug));
    }

    /**
     * @return Parameters
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
