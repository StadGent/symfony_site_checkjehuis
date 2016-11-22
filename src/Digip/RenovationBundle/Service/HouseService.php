<?php

namespace Digip\RenovationBundle\Service;

use Digip\RenovationBundle\Entity\Config;
use Digip\RenovationBundle\Entity\ConfigCategory;
use Digip\RenovationBundle\Entity\DefaultEnergy;
use Digip\RenovationBundle\Entity\House;
use Digip\RenovationBundle\Entity\DefaultSurface;
use Digip\RenovationBundle\Entity\DefaultRoof;
use Digip\RenovationBundle\Entity\Parameter;
use Doctrine\ORM\EntityManager;
use Knp\Snappy\GeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class HouseService extends AbstractService
{
    const HOUSE_SESSION_KEY = 'current_house';

    /**
     * @var GeneratorInterface
     */
    protected $pdfGenerator;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var string
     */
    protected $urlHeatMap;

    /**
     * @var string
     */
    protected $urlSolarMap;

    /**
     * @param ContainerInterface $container
     * @param EntityManager $doctrine
     * @param GeneratorInterface $pdfGenerator
     * @param Router $router
     */
    public function __construct(
        ContainerInterface $container,
        EntityManager $doctrine,
        GeneratorInterface $pdfGenerator,
        Router $router
    ) {
        parent::__construct($container, $doctrine);
        $this->pdfGenerator = $pdfGenerator;
        $this->router = $router;
    }

    /**
     * @return string
     */
    public function getUrlHeatMap()
    {
        return $this->urlHeatMap;
    }

    /**
     * @param string $urlHeatMap
     * @return $this
     */
    public function setUrlHeatMap($urlHeatMap)
    {
        $this->urlHeatMap = $urlHeatMap;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrlSolarMap()
    {
        return $this->urlSolarMap;
    }

    /**
     * @param string $urlSolarMap
     * @return $this
     */
    public function setUrlSolarMap($urlSolarMap)
    {
        $this->urlSolarMap = $urlSolarMap;
        return $this;
    }

    public function parseUrl($url, House $house, $returnUrl)
    {
        $token = urlencode($house->getToken());
        $address = urlencode($house->getAddress());
        $returnUrl = urlencode($returnUrl);

        return str_replace(
            array(
                "[[BB_TOKEN]]",
                "[[BB_ADDRESS]]",
                "[[BB_RETURN_URL]]",
            ),
            array(
                $token,
                $address,
                $returnUrl,
            ),
            $url
        );
    }

    /**
     * @param array $filter
     * @return House[]
     */
    public function getAllHouses(array $filter = array())
    {
        $em = $this->getDoctrine();
        $repo = $em->getRepository('DigipRenovationBundle:House');

        $criteria = new \Doctrine\Common\Collections\Criteria();

        if (isset($filter['from'])) {
            $criteria->andWhere($criteria->expr()->gt('lastUpdate', $filter['from']));
        }
        if (isset($filter['to'])) {
            $criteria->andWhere($criteria->expr()->lte('lastUpdate', $filter['to']));
        }

        $criteria->orderBy(array('lastUpdate' => 'DESC'));

        return $repo->matching($criteria);
    }

    /**
     * Save house to the database and keep the id in the session
     *
     * @param House $house
     * @param bool $resetDefaults
     * @return $this
     */
    public function saveHouse(House $house, $resetDefaults = false)
    {
        // update defaults
        if ($resetDefaults) {
            $house->setConfigs($this->getDefaultConfigs($house));
            $house->setDefaultEnergy($this->getDefaultEnergy($house));
            $house->setDefaultSurface($this->getDefaultSurface($house));
            $house->setDefaultRoof($this->getDefaultRoof($house));
            $house->setDefaultRoofIfFlat($this->getDefaultRoofIfFlat($house));
            $house->setExtraConfigRoof($house->getConfig(ConfigCategory::CAT_ROOF));
            // reset renewables
            $house->setRenewables(array());
        }

        // save to db
        $this->doctrine->persist($house);
        $this->doctrine->flush();

        // keep track of ID in session
        $this->container->get('session')->set(self::HOUSE_SESSION_KEY, $house->getId());

        return $this;
    }

    /**
     * Load a house from the current session id as saved by self::saveHouse()
     *
     * @return bool|House
     */
    public function loadHouse()
    {
        $id = $this->container->get('session')->get(self::HOUSE_SESSION_KEY);

        if (!$id) {
            return false;
        }

        return $this->doctrine->getRepository('DigipRenovationBundle:House')->find($id);
    }

    /**
     * @param $token
     * @return House|false
     */
    public function loadHouseFromToken($token)
    {
        $repo = $this->doctrine->getRepository('DigipRenovationBundle:House');
        $house = $repo->findOneBy(array(
            'token' => $token,
        ));

        if ($house) {
            $this->container->get('session')->set(self::HOUSE_SESSION_KEY, $house->getId());
            return true;
        }

        return false;
    }

    public function generatePdf(House $house)
    {
        $url = $this->router->generate(
            'house_calc_pdf_template',
            array('token' => $house->getToken()),
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return $this->pdfGenerator->getOutput(
            $url,
            array(
                'margin-top' => 0,
                'margin-bottom' => 0,
                'margin-left' => 0,
                'margin-right' => 0,
            )
        );
    }

    /**
     * Get the default configs based on the House's parameters
     *
     * @param House $house
     * @return Config[]
     */
    public function getDefaultConfigs(House $house)
    {
        $defaults = array();

        /** @var ConfigService $configService */
        $configService = $this->container->get('digip_reno.service.config');

        $categories = $configService->getAllCategories();
        foreach ($categories as $cat) {

            /** @var Config $defaultConfig */
            $defaultConfig = null;
            foreach ($cat->getConfigs() as $conf) {
                if ($conf->isPossibleCurrent() && $conf->isDefault()) {
                    if ($conf->getDefaultUpToYear(true) >= $house->getYear() &&
                        (!$defaultConfig || $conf->getDefaultUpToYear(true) < $defaultConfig->getDefaultUpToYear(true))
                    ) {
                        $defaultConfig = $conf;
                    }
                }
            }

            if (!$defaultConfig) {
                throw new \RuntimeException('No default config found for category: ' . $cat->getSlug());
            }

            $defaults[$cat->getSlug()] = $defaultConfig;

        }

        return $defaults;
    }

    /**
     * Get the default surface area based on the house's parameters
     *
     * @param House $house
     * @return DefaultSurface
     */
    public function getDefaultSurface(House $house)
    {
        /** @var DefaultsService $measurementService */
        $measurementService = $this->container->get('digip_reno.service.defaults');

        return $measurementService->getSurface(
            $house->getBuildingType(),
            $house->getSize()
        );
    }

    /**
     * Get the default roof surface area based on the house's parameters
     *
     * @param House $house
     * @return DefaultRoof
     */
    public function getDefaultRoof(House $house)
    {
        /** @var DefaultsService $measurementService */
        $measurementService = $this->container->get('digip_reno.service.defaults');

        return $measurementService->getRoof(
            $house->getBuildingType(),
            $house->getSize(),
            $house->getRoofType()
        );
    }

    /**
     * Get the default roof surface area based on the house's parameters
     * Force the roof type to be flat
     *
     * @param House $house
     * @return DefaultRoof
     */
    public function getDefaultRoofIfFlat(House $house)
    {
        /** @var DefaultsService $measurementService */
        $measurementService = $this->container->get('digip_reno.service.defaults');

        return $measurementService->getRoof(
            $house->getBuildingType(),
            $house->getSize(),
            House::ROOF_TYPE_FLAT
        );
    }

    /**
     * Get the default energy usage based on the house's parameters
     *
     * @param House $house
     * @return DefaultEnergy
     */
    public function getDefaultEnergy(House $house)
    {
        /** @var DefaultsService $measurementService */
        $measurementService = $this->container->get('digip_reno.service.defaults');

        return $measurementService->getEnergy(
            $house->getBuildingType(),
            $house->getSize(),
            $house->getYear()
        );
    }

    /**
     * Calculate the estimated energy usage based on the house's parameters
     * If an actual energy usage was configured, return this.
     *
     * @param House $house
     * @return Calculation
     */
    public function calculateCurrent(House $house)
    {
        $nonHeatingElec = $this->getDefaultEnergy($house)->getElectricity();

        $costService = $this->container->get('digip_reno.service.buildcost');
        $subsidyService = $this->container->get('digip_reno.service.subsidy');
        $defaultsService = $this->container->get('digip_reno.service.defaults');
        $paramService = $this->container->get('digip_reno.service.parameter');

        $priceGas = $paramService->getParameterBySlug(Parameter::PARAM_PRICE_GAS)->getValue();
        $priceElec = $paramService->getParameterBySlug(Parameter::PARAM_PRICE_ELEC)->getValue();
        $co2PerKwh = $paramService->getParameterBySlug(Parameter::PARAM_CO2_KWH)->getValue();
        $subsidyCeilingGentRoof = $paramService->getParameterBySlug(Parameter::PARAM_SUBSIDY_GENT_ROOF)->getValue();
        $electricHeating = $defaultsService->getEnergy($house->getBuildingType(), $house->getSize(), $house->getYear())->getElectricHeating();

        $view = $this->get('digip_reno.calculator.factory')->createCalculatorView($house);

        var_dump($view);

        return $calculation;
    }

    /**
     * Calculate the estimated energy usage based on the house's parameters and the configured upgrades
     *
     * @param House $house
     * @return Calculation
     */
    public function calculateUpgrade(House $house)
    {
        // start from current
        $calculation = $this->calculateCurrent($house);
        $calculation->calculate(true);

        return $calculation;
    }
}
