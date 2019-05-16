<?php

namespace App\Service;

use App\Entity\Config;
use App\Entity\ConfigCategory;
use App\Entity\DefaultEnergy;
use App\Entity\DefaultRoof;
use App\Entity\DefaultSurface;
use App\Entity\House;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class HouseService extends AbstractService
{
    const HOUSE_SESSION_KEY = 'current_house';

    /**
     * PDF generator.
     *
     * @var Pdf
     */
    protected $pdfGenerator;

    /**
     * The router.
     *
     * @var RouterInterface
     */
    protected $router;

    /**
     * The heatmap url.
     *
     * @var string
     */
    protected $urlHeatMap;

    /**
     * The solar map url.
     *
     * @var string
     */
    protected $urlSolarMap;

    /**
     * The config service.
     *
     * @var ConfigService
     */
    protected $configService;

    /**
     * The defaults service.
     *
     * @var DefaultsService
     */
    protected $defaultsService;

    /**
     * The current session.
     *
     * @var SessionInterface
     */
    protected $session;

    /**
     * Service constructor.
     *
     * @param EntityManagerInterface $entityManager
     *   The entity manager.
     * @param Pdf $pdfGenerator
     *   The pdf generator.
     * @param RouterInterface $router
     *   The router.
     * @param SessionInterface $session
     *   The current session.
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        Pdf $pdfGenerator,
        RouterInterface $router,
        ConfigService $configService,
        DefaultsService $defaultsService,
        SessionInterface $session,
        ParameterBagInterface $parameters
    ) {
        parent::__construct($entityManager);
        $this->pdfGenerator = $pdfGenerator;
        $this->router = $router;
        $this->configService = $configService;
        $this->defaultsService = $defaultsService;
        $this->session = $session;
        $this->urlHeatMap = $parameters->get('digip_reno.gks_url_map_heat');
        $this->urlSolarMap = $parameters->get('digip_reno.gks_url_map_solar');
    }

    /**
     * Get the url for the heatmap.
     *
     * @return string
     *   The url for the heatmap.
     */
    public function getUrlHeatMap()
    {
        return $this->urlHeatMap;
    }

    /**
     * Set the url for the heatmap.
     *
     * @param string $urlHeatMap
     *   The url for the heatmap.
     *
     * @return $this
     */
    public function setUrlHeatMap($urlHeatMap)
    {
        $this->urlHeatMap = $urlHeatMap;
        return $this;
    }

    /**
     * Get the url for the solar map.
     *
     * @return string
     *   The url for the solar map.
     */
    public function getUrlSolarMap()
    {
        return $this->urlSolarMap;
    }

    /**
     * Set the url for the solar map.
     *
     * @param string $urlSolarMap
     *   The url for the solar map.
     *
     * @return $this
     */
    public function setUrlSolarMap($urlSolarMap)
    {
        $this->urlSolarMap = $urlSolarMap;
        return $this;
    }

    /**
     * Replace tokens in a url.
     *
     * @param string $url
     *   The url to replace the tokens in.
     *   Supported tokens:
     *     - [[BB_TOKEN]]: The house token.
     *     - [[BB_ADDRESS]]: The house address.
     *     - [[BB_RETURN_URL: The $returnUrl parameter.
     * @param House $house
     *   The house.
     * @param string $returnUrl
     *   The return url.
     *
     * @return string
     *   The url with the tokens replaced.
     */
    public function parseUrl($url, House $house, $returnUrl)
    {
        $token = urlencode($house->getToken());
        $address = urlencode($house->getAddress());

        return str_replace(
            array(
                "[[BB_TOKEN]]",
                "[[BB_ADDRESS]]",
                "[[BB_RETURN_URL]]",
            ),
            array(
                $token,
                $address,
                urlencode($returnUrl),
            ),
            $url
        );
    }

    /**
     * Get all houses (optionally filtered).
     *
     * @param array $filter
     *   The filter criteria.
     *
     * @return House[]
     *   The matching houses.
     */
    public function getAllHouses(array $filter = array())
    {
        $repo = $this->entityManager->getRepository(House::class);

        $criteria = new Criteria();

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
     * Save house to the database and keep the id in the session.
     *
     * @param House $house
     *   The house to save.
     * @param bool $resetDefaults
     *   Whether or not to reset to default values.
     *
     * @return $this
     */
    public function saveHouse(House $house, $resetDefaults = false)
    {
        // Update defaults.
        if ($resetDefaults) {
            $house->setConfigs($this->getDefaultConfigs($house))
                ->setDefaultEnergy($this->getDefaultEnergy($house))
                ->setDefaultSurface($this->getDefaultSurface($house))
                ->setDefaultRoof($this->getDefaultRoof($house))
                ->setDefaultRoofIfFlat($this->getDefaultRoofIfFlat($house))
                ->setExtraConfigRoof($house->getConfig(ConfigCategory::CAT_ROOF))
                // Reset renewables.
                ->setRenewables(array());
        }

        // Save to db.
        $this->entityManager->persist($house);
        $this->entityManager->flush();

        // Keep track of ID in session.
        $this->session->set(self::HOUSE_SESSION_KEY, $house->getId());

        return $this;
    }

    /**
     * Load a house from the current session id as saved by self::saveHouse().
     *
     * @return bool|House
     */
    public function loadHouse()
    {
        $id = $this->session->get(self::HOUSE_SESSION_KEY);

        if (!$id) {
            return false;
        }

        return $this->entityManager->getRepository(House::class)->find($id);
    }

    /**
     * Load a house from a token.
     *
     * @param string $token
     *   The house token.
     *
     * @return House|false
     */
    public function loadHouseFromToken($token)
    {
        $repo = $this->entityManager->getRepository(House::class);
        $house = $repo->findOneBy(array(
            'token' => $token,
        ));

        if ($house) {
            $this->session->set(self::HOUSE_SESSION_KEY, $house->getId());
            return true;
        }

        return false;
    }

    /**
     * Generate a pdf report.
     *
     * @param House $house
     *   The house to generate the pdf for.
     * @return string
     *   The contents of the pdf document.
     */
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
                'disable-javascript' => true,
            )
        );
    }

    /**
     * Get the default configs based on the House's parameters.
     *
     * @param House $house
     *   The house to get the default configs for.
     *
     * @return Config[]
     */
    public function getDefaultConfigs(House $house)
    {
        $defaults = array();

        $categories = $this->configService->getAllCategories();
        foreach ($categories as $cat) {
            $defaults[$cat->getSlug()] = $this->getDefaultConfigForCategory($cat, $house->getYear());
        }

        return $defaults;
    }

    /**
     * Get the default configs for a category for a certain a year.
     *
     * @param ConfigCategory $configCategory
     *   The config category to get the default for.
     * @param int $year
     *   The year to get the defaults for.
     *
     * @return Config
     *   The default config.
     *
     * @throws \RuntimeException
     *   If no default config was found for the given category.
     */
    protected function getDefaultConfigForCategory(ConfigCategory $configCategory, $year)
    {
        /** @var Config $defaultConfig */
        $defaultConfig = null;
        foreach ($configCategory->getConfigs() as $conf) {
            if ($conf->isPossibleCurrent() && $conf->isDefault()
              && $conf->getDefaultUpToYear(true) >= $year
              && (!$defaultConfig || $conf->getDefaultUpToYear(true) < $defaultConfig->getDefaultUpToYear(true))
            ) {
                $defaultConfig = $conf;
            }
        }

        if (!$defaultConfig) {
            throw new \RuntimeException('No default config found for category: ' . $configCategory->getSlug());
        }
        return $defaultConfig;
    }

    /**
     * Get the default surface area based on the house's parameters.
     *
     * @param House $house
     *   The house to get the default surface area for.
     *
     * @return DefaultSurface
     */
    public function getDefaultSurface(House $house)
    {
        return $this->defaultsService->getSurface(
            $house->getBuildingType(),
            $house->getSize()
        );
    }

    /**
     * Get the default roof surface area based on the house's parameters.
     *
     * @param House $house
     *   The house to get the default roof surface area for.
     *
     * @return DefaultRoof
     */
    public function getDefaultRoof(House $house)
    {
        return $this->defaultsService->getRoof(
            $house->getBuildingType(),
            $house->getSize(),
            $house->getRoofType()
        );
    }

    /**
     * Get the default roof surface area based on the house's parameters.
     * Force the roof type to be flat.
     *
     * @param House $house
     *   The house to get the default roof surface area for.
     * @return DefaultRoof
     */
    public function getDefaultRoofIfFlat(House $house)
    {
        return $this->defaultsService->getRoof(
            $house->getBuildingType(),
            $house->getSize(),
            House::ROOF_TYPE_FLAT
        );
    }

    /**
     * Get the default energy usage based on the house's parameters.
     *
     * @param House $house
     *   The house to get the default energy usage for.
     *
     * @return DefaultEnergy
     */
    public function getDefaultEnergy(House $house)
    {
        return $this->defaultsService->getEnergy(
            $house->getBuildingType(),
            $house->getSize(),
            $house->getYear()
        );
    }
}
