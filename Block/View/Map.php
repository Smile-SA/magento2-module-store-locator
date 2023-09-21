<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Block\View;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\Store;
use Smile\Map\Api\Data\GeoPointInterface;
use Smile\Map\Api\MapInterface;
use Smile\Map\Api\MapProviderInterface;
use Smile\Map\Model\AddressFormatter;
use Smile\Retailer\Api\Data\RetailerExtensionInterface;
use Smile\Retailer\Api\Data\RetailerInterface;
use Smile\Retailer\Model\ResourceModel\Retailer\Collection as RetailerCollection;
use Smile\Retailer\Model\ResourceModel\Retailer\CollectionFactory as RetailerCollectionFactory;
use Smile\StoreLocator\Api\Data\RetailerAddressInterface;
use Smile\StoreLocator\Block\AbstractView;
use Smile\StoreLocator\Helper\Data;
use Smile\StoreLocator\Helper\Schedule;
use Smile\StoreLocator\Model\Retailer\ScheduleManagement;

/**
 * Map rendering block.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Map extends AbstractView
{
    private MapInterface $map;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        MapProviderInterface $mapProvider,
        private Data $storeLocatorHelper,
        private AddressFormatter $addressFormatter,
        private Schedule $scheduleHelper,
        private ScheduleManagement $scheduleManagement,
        private RetailerCollectionFactory $retailerCollectionFactory,
        private UrlInterface $urlBuilder,
        array $data = []
    ) {
        parent::__construct($context, $coreRegistry, $data);
        $this->map = $mapProvider->getMap();
    }

    /**
     * Returns current store address.
     */
    public function getAddress(): ?RetailerAddressInterface
    {
        return $this->getRetailer()->getData('address');
    }

    /**
     * Return the retailer ID.
     */
    public function getId(): ?int
    {
        return (int) $this->getRetailer()->getId();
    }

    /**
     * Returns current store coordinates.
     */
    public function getCoordinates(): ?GeoPointInterface
    {
        return $this->getAddress()->getCoordinates();
    }

    /**
     * @inheritdoc
     */
    public function getJsLayout()
    {
        $jsLayout = $this->jsLayout;

        $jsLayout['components']['store-locator-store-view']['provider']  = $this->map->getIdentifier();
        $jsLayout['components']['store-locator-store-view']['markers'] = $this->getMarkerData();
        $jsLayout['components']['store-locator-store-view'] = array_merge(
            $jsLayout['components']['store-locator-store-view'],
            $this->map->getConfig()
        );
        $jsLayout['components']['store-locator-store-view']['children']['geocoder']['provider'] = $this->map
            ->getIdentifier();
        $jsLayout['components']['store-locator-store-view']['children']['geocoder'] = array_merge(
            $jsLayout['components']['store-locator-store-view']['children']['geocoder'],
            $this->map->getConfig()
        );

        return json_encode($jsLayout);
    }

    /**
     * Create full marker data for store view.
     */
    public function getMarkerData(): ?array
    {
        $result = null;
        $mediaPath = $this->getMediaPath();
        $imageUrlRetailer = $this->getImageUrl() . 'seller/';
        $image = $mediaPath ? $imageUrlRetailer . $mediaPath : false;
        $retailer = $this->getRetailer();
        /** @var RetailerExtensionInterface $retailerExtensionAttr */
        $retailerExtensionAttr = $retailer->getExtensionAttributes();

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
                'calendar' => $this->scheduleManagement->getCalendar($retailer),
                'openingHours' => $this->scheduleManagement->getWeekOpeningHours($retailer),
                'specialOpeningHours' => $retailerExtensionAttr->getSpecialOpeningHours(),
            ]
        );

        $result[] = $storeMarkerData;

        return $result;
    }

    /**
     * Get current url for button "return to stores list".
     */
    public function getStoreListUrl(): string
    {
        return $this->storeLocatorHelper->getHomeUrl();
    }

    /**
     * Get address formatted in HTML.
     */
    public function getAddressHtml(): string
    {
        return $this->getAddress()
            ? $this->addressFormatter->formatAddress($this->getAddress(), AddressFormatter::FORMAT_HTML)
            : '';
    }

    /**
     * Get URL used to redirect user to the direction API.
     */
    public function getDirectionUrl(): string
    {
        return $this->map->getDirectionUrl($this->getCoordinates());
    }

    /**
     * Returns retailer description.
     */
    public function getDescription(): ?string
    {
        return $this->getRetailer()->getData('description');
    }

    /**
     * * Get base media url.
     *
     * @throws NoSuchEntityException
     */
    public function getImageUrl(): string
    {
        /** @var Store $currentStore */
        $currentStore = $this->_storeManager->getStore();

        return $currentStore->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * Get image name.
     */
    protected function getMediaPath(): string|bool
    {
        return $this->getRetailer()->getMediaPath() ?: false;
    }

    /**
     * Get full image url.
     */
    public function getImage(): string|bool
    {
        $mediaPath = $this->getMediaPath();
        $imageUrlRetailer = $this->getImageUrl() . 'seller/';

        return $mediaPath ? $imageUrlRetailer . $mediaPath : false;
    }

    /**
     * Get store name.
     */
    public function getStoreName(): string
    {
        return $this->getRetailer()->getName();
    }

    /**
     * Get phone number.
     */
    public function getPhone(): string|bool
    {
        return $this->getRetailer()->getData('contact_phone') ?: false;
    }

    /**
     * Get email address.
     */
    public function getContactMail(): string|bool
    {
        return $this->getRetailer()->getData('contact_mail') ?: false;
    }

    /**
     * Get all existing markers.
     */
    public function getAllMarkers(): RetailerCollection
    {
        $retailerCollection = $this->retailerCollectionFactory->create();
        $retailerCollection->addAttributeToSelect(['name', 'contact_mail', 'contact_phone', 'contact_mail', 'image']);
        $retailerCollection->addFieldToFilter('is_active', 1);
        $retailerCollection->addOrder('name', 'asc');

        return $retailerCollection;
    }

    /**
     * Create full collection for nearby stores with full data.
     */
    public function collectionFull(): ?array
    {
        $collection = $this->getAllMarkers();
        $imageUrlRetailer = $this->getImageUrl() . 'seller/';
        $markers  = [];

        /** @var RetailerInterface $retailer */
        foreach ($collection as $retailer) {
            /** @var RetailerExtensionInterface $retailerExtensionAttr */
            $retailerExtensionAttr = $retailer->getExtensionAttributes();
            $address = $retailerExtensionAttr->getAddress();
            $image = $retailer->getMediaPath() ? $imageUrlRetailer . $retailer->getMediaPath() : false;
            $markerData = [
                'id' => $retailer->getId(),
                'latitude' => $address->getCoordinates()->getLatitude(),
                'longitude' => $address->getCoordinates()->getLongitude(),
                'name' => $retailer->getName(),
                'address' => $this->addressFormatter->formatAddress($address, AddressFormatter::FORMAT_ONELINE),
                'url' => $this->storeLocatorHelper->getRetailerUrl($retailer),
                'directionUrl' => $this->map->getDirectionUrl($address->getCoordinates()),
                'setStoreData' => $this->getSetStorePostData($retailer),
                'image' => $image,
            ];

            foreach (['contact_mail', 'contact_phone', 'contact_mail'] as $contactAttribute) {
                $markerData[$contactAttribute] = $retailer->getData($contactAttribute) ?: '';
            }

            //phpcs:ignore Magento2.Performance.ForeachArrayMerge.ForeachArrayMerge
            $markerData['schedule'] = array_merge(
                $this->scheduleHelper->getConfig(),
                [
                    'calendar' => $this->scheduleManagement->getCalendar($retailer),
                    'openingHours' => $this->scheduleManagement->getWeekOpeningHours($retailer),
                    'specialOpeningHours' => $retailerExtensionAttr->getSpecialOpeningHours(),
                ]
            );

            $markers[] = $markerData;
        }

        return $markers;
    }

    /**
     * Get the JSON post data used to build the set store link.
     */
    private function getSetStorePostData(RetailerInterface $retailer): array
    {
        $setUrl   = $this->urlBuilder->getUrl('storelocator/store/set', ['_secure' => true]);
        $postData = ['id' => $retailer->getId()];

        return ['action' => $setUrl, 'data' => $postData];
    }
}
