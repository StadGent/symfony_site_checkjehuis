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
     * @param EntityManagerInterface $entityManager
     * @param Pdf $pdfGenerator
     * @param RouterInterface $router
     * @param SessionInterface $session
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        Pdf $pdfGenerator,
        RouterInterface $router,
        ConfigService $configService,
        DefaultsService $defaultsService,
        SessionInterface $session
    ) {
        parent::__construct($entityManager);
        $this->pdfGenerator = $pdfGenerator;
        $this->router = $router;
        $this->configService = $configService;
        $this->defaultsService = $defaultsService;
        $this->session = $session;
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
     * @param array $filter
     * @return House[]
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
     * Save house to the database and keep the id in the session
     *
     * @param House $house
     * @param bool $resetDefaults
     * @return $this
     */
    public function saveHouse(House $house, $resetDefaults = false)
    {
        // Update defaults.
        if ($resetDefaults) {
            $house->setConfigs($this->getDefaultConfigs($house));
            $house->setDefaultEnergy($this->getDefaultEnergy($house));
            $house->setDefaultSurface($this->getDefaultSurface($house));
            $house->setDefaultRoof($this->getDefaultRoof($house));
            $house->setDefaultRoofIfFlat($this->getDefaultRoofIfFlat($house));
            $house->setExtraConfigRoof($house->getConfig(ConfigCategory::CAT_ROOF));
            // Reset renewables.
            $house->setRenewables(array());
        }

        // Save to db.
        $this->entityManager->persist($house);
        $this->entityManager->flush();

        // Keep track of ID in session
        $this->session->set(self::HOUSE_SESSION_KEY, $house->getId());

        return $this;
    }

    /**
     * Load a house from the current session id as saved by self::saveHouse()
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
     * @param $token
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

        $categories = $this->configService->getAllCategories();
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
        return $this->defaultsService->getSurface(
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
        return $this->defaultsService->getRoof(
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
        return $this->defaultsService->getRoof(
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
        return $this->defaultsService->getEnergy(
            $house->getBuildingType(),
            $house->getSize(),
            $house->getYear()
        );
    }

}
