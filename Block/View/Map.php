<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\StoreLocator
 * @author    Aurelien FOUCRET <aurelien.foucret@smile.fr>
 * @author    Ihor KVASNYTSKYI <ihor.kvasnytskyi@smile-ukraine.com>
 * @copyright 2019 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\StoreLocator\Block\View;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Smile\Map\Api\Data\GeoPointInterface;
use Smile\Map\Api\MapInterface;
use Smile\Map\Api\MapProviderInterface;
use Smile\Map\Model\AddressFormatter;
use Smile\Retailer\Api\Data\RetailerInterface;
use Smile\Retailer\Model\ResourceModel\Retailer\CollectionFactory as RetailerCollectionFactory;
use Smile\StoreLocator\Api\Data\RetailerAddressInterface;
use Smile\StoreLocator\Block\AbstractView;
use Smile\StoreLocator\Helper\Data;
use Smile\StoreLocator\Helper\Schedule;
use Smile\StoreLocator\Model\Retailer\ScheduleManagement;

/**
 * Map rendering block.
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class Map extends AbstractView
{
    /**
     * @var MapInterface
     */
    private MapInterface $map;

    /**
     * @var Data
     */
    private Data $storeLocatorHelper;

    /**
     * @var AddressFormatter
     */
    private AddressFormatter $addressFormatter;

    /**
     * @var Schedule
     */
    private Schedule $scheduleHelper;

    /**
     * @var ScheduleManagement
     */
    private ScheduleManagement $scheduleManager;

    /**
     * @var RetailerCollectionFactory
     */
    private RetailerCollectionFactory $retailerCollectionFactory;

    /**
     * Constructor.
     *
     * @param Context                   $context                    Application context.
     * @param Registry                  $coreRegistry               Application registry.
     * @param MapProviderInterface      $mapProvider                Map configuration provider.
     * @param Data                      $storeLocatorHelper         Store locacator helper.
     * @param AddressFormatter          $addressFormatter           Address formatter.
     * @param Schedule                  $scheduleHelper             Schedule Helper
     * @param ScheduleManagement        $scheduleManagement         Schedule Management
     * @param RetailerCollectionFactory $retailerCollectionFactory  The retailer collection factory.
     * @param array                     $data                       Additional data.
     */

    public function __construct(
        Context $context,
        Registry $coreRegistry,
        MapProviderInterface $mapProvider,
        Data $storeLocatorHelper,
        AddressFormatter $addressFormatter,
        Schedule $scheduleHelper,
        ScheduleManagement $scheduleManagement,
        RetailerCollectionFactory $retailerCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $coreRegistry, $data);

        $this->map                = $mapProvider->getMap();
        $this->addressFormatter   = $addressFormatter;
        $this->storeLocatorHelper = $storeLocatorHelper;
        $this->scheduleHelper     = $scheduleHelper;
        $this->scheduleManager    = $scheduleManagement;
        $this->retailerCollectionFactory = $retailerCollectionFactory;
    }

    /**
     * Returns current store address.
     *
     * @return ?RetailerAddressInterface
     */
    public function getAddress(): ?RetailerAddressInterface
    {
        return $this->getRetailer()->getAddress();
    }

    /**
     * Return the retailer ID.
     *
     * @return int|null
     */
    public function getId(): int|null
    {
        return $this->getRetailer()->getId();
    }

    /**
     * Returns current store coordinates.
     *
     * @return GeoPointInterface
     */
    public function getCoordinates(): GeoPointInterface
    {
        return $this->getAddress()->getCoordinates();
    }

    /**
     * {@inheritDoc}
     */
    public function getJsLayout(): string
    {
        $jsLayout = $this->jsLayout;

        $jsLayout['components']['store-locator-store-view']['provider']  = $this->map->getIdentifier();
        $jsLayout['components']['store-locator-store-view']['markers'] = $this->getMarkerData();
        $jsLayout['components']['store-locator-store-view'] = array_merge(
            $jsLayout['components']['store-locator-store-view'],
            $this->map->getConfig()
        );
        $jsLayout['components']['store-locator-store-view']['children']['geocoder']['provider'] = $this->map->getIdentifier();
        $jsLayout['components']['store-locator-store-view']['children']['geocoder'] = array_merge(
            $jsLayout['components']['store-locator-store-view']['children']['geocoder'],
            $this->map->getConfig()
        );

        return json_encode($jsLayout);
    }

    /**
     * Create full marker data for store view.
     *
     * @return array|null
     */
    public function getMarkerData(): array|null
    {
        $result = null;
        $mediaPath = $this->getMediaPath();
        $imageUrlRetailer = $this->getImageUrl() . 'seller/';
        $image = $mediaPath ? $imageUrlRetailer . $mediaPath : false;
        $retailer = $this->getRetailer();

        $storeMarkerData = [
            'latitude'  => $this->getCoordinates()->getLatitude(),
            'longitude' => $this->getCoordinates()->getLongitude(),
            'image' => $image,
            'id' => $this->getId(),
            'phone' => $this->getPhone(),
            'mail' => $this->getContactMail(),
            'closestShops' => $this->collectionFull(),
        ];
        $storeMarkerData['schedule'] = array_merge(
            $this->scheduleHelper->getConfig(),
            [
                'calendar' => $this->scheduleManager->getCalendar($retailer),
                'openingHours' => $this->scheduleManager->getWeekOpeningHours($retailer),
                'specialOpeningHours' => $retailer->getExtensionAttributes()->getSpecialOpeningHours(),
            ]
        );

        $result[] = $storeMarkerData;

        return $result;
    }

    /**
     * Get current url for button "return to stores list".
     *
     * @return string
     */
    public function getStoreListUrl(): string
    {
        return $this->storeLocatorHelper->getHomeUrl();
    }

    /**
     * Get address formatted in HTML.
     *
     * @return string
     */
    public function getAddressHtml(): string
    {
        return $this->getAddress() ? $this->addressFormatter->formatAddress($this->getAddress(), AddressFormatter::FORMAT_HTML) : '';
    }

    /**
     * Get URL used to redirect user to the direction API.
     *
     * @return string
     */
    public function getDirectionUrl(): string
    {
        return $this->map->getDirectionUrl($this->getCoordinates());
    }

    /**
     * Returns retailer description.
     *
     * @return null|string
     */
    public function getDescription(): null|string
    {
        return $this->getRetailer()->getDescription();
    }

    /**
     * * Get media part.
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getImageUrl(): mixed
    {
        $currentStore = $this->_storeManager->getStore();

        return $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * Get image name.
     *
     * @return bool|string
     */
    protected function getMediaPath(): bool|string
    {
        return $this->getRetailer()->getMediaPath() ?: false;
    }

    /**
     * Get full image url.
     *
     * @return bool|string
     */
    public function getImage(): bool|string
    {
        $mediaPath = $this->getMediaPath();
        $imageUrlRetailer = $this->getImageUrl() . 'seller/';

        return $mediaPath ? $imageUrlRetailer . $mediaPath : false;
    }

    /**
     * Get store name.
     *
     * @return ?string
     */
    public function getStoreName(): string
    {
        return $this->getRetailer()->getName();
    }

    /**
     * Get phone number.
     *
     * @return bool|string
     */
    public function getPhone(): bool|string
    {
        return $this->getRetailer()->getContactPhone() ?: false;
    }

    /**
     * Get email address.
     *
     * @return bool|string
     */
    public function getContactMail(): bool|string
    {
        return $this->getRetailer()->getContactMail() ?: false;
    }

    /**
     * Get all exist markers.
     *
     * @return mixed
     */
    public function getAllMarkers(): mixed
    {
        $retailerCollection = $this->retailerCollectionFactory->create();
        $retailerCollection->addAttributeToSelect(['name', 'contact_mail', 'contact_phone', 'contact_mail', 'image']);
        $retailerCollection->addFieldToFilter('is_active', (int) true);
        $retailerCollection->addOrder('name', 'asc');

        return $retailerCollection;
    }

    /**
     * Create full collection for nearby stores with full data.
     *
     * @return array|null
     */
    public function collectionFull(): array|null
    {
        $collection = $this->getAllMarkers();

        $markers  = null;

        if (!$markers) {
            /** @var RetailerInterface $retailer */
            $imageUrlRetailer = $this->getImageUrl() . 'seller/';
            foreach ($collection as $retailer) {
                $address = $retailer->getExtensionAttributes()->getAddress();
                $image = $retailer->getMediaPath() ? $imageUrlRetailer . $retailer->getMediaPath() : false;
                $markerData = [
                    'id'           => $retailer->getId(),
                    'latitude'     => $address->getCoordinates()->getLatitude(),
                    'longitude'    => $address->getCoordinates()->getLongitude(),
                    'name'         => $retailer->getName(),
                    'address'      => $this->addressFormatter->formatAddress($address, AddressFormatter::FORMAT_ONELINE),
                    'url'          => $this->storeLocatorHelper->getRetailerUrl($retailer),
                    'directionUrl' => $this->map->getDirectionUrl($address->getCoordinates()),
                    'setStoreData' => $this->getSetStorePostData($retailer),
                    'image'        => $image,
                ];

                foreach (['contact_mail', 'contact_phone', 'contact_mail'] as $contactAttribute) {
                    $markerData[$contactAttribute] = $retailer->getData($contactAttribute) ?: '';
                }

                $markerData['schedule'] = array_merge(
                    $this->scheduleHelper->getConfig(),
                    [
                        'calendar'            => $this->scheduleManager->getCalendar($retailer),
                        'openingHours'        => $this->scheduleManager->getWeekOpeningHours($retailer),
                        'specialOpeningHours' => $retailer->getExtensionAttributes()->getSpecialOpeningHours(),
                    ]
                );


                $markers[] = $markerData;
            }
        }

        return $markers;
    }
}
