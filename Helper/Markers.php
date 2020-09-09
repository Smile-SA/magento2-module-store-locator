<?php

/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\StoreLocator
 * @author    Remy LESCALLIER <remy.lescallier@smile.fr>
 * @copyright 2020 Smile
 */

namespace Smile\StoreLocator\Helper;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Profiler;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\UrlInterface;
use Smile\Map\Api\MapInterface;
use Smile\Map\Api\MapProviderInterface;
use Smile\Map\Model\AddressFormatter;
use Smile\Retailer\Api\Data\RetailerInterface;
use Smile\Retailer\Model\ResourceModel\Retailer\Collection as RetailerCollection;
use Smile\Retailer\Model\ResourceModel\Retailer\CollectionFactory as RetailerCollectionFactory;
use Smile\StoreLocator\Helper\Data as StoreLocatorHelper;
use Smile\StoreLocator\Helper\Schedule as ScheduleHelper;
use Smile\StoreLocator\Model\Retailer\ScheduleManagement;

/**
 * Markers helper - get all marker for map
 *
 * @author    Remy Lescallier <remy.lescallier@smile.fr>
 * @copyright 2020 Smile
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Markers extends AbstractHelper
{
    const MARKERS_DATA_CACHE_KEY = 'smile_storelocator_search';
    const ADDRESS_FORMAT = AddressFormatter::FORMAT_ONELINE;
    const PROFILER_NAME = 'SmileStoreLocator';
    const CACHE_TAG = 'smile_store_locator_markers';

    /**
     * @var RetailerCollectionFactory
     */
    protected $retailerCollectionFactory;

    /**
     * @var CacheInterface
     */
    protected $cacheInterface;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var AddressFormatter
     */
    protected $addressFormatter;

    /**
     * @var Data
     */
    protected $storeLocatorHelper;

    /**
     * @var MapInterface
     */
    protected $map;

    /**
     * @var Schedule
     */
    protected $scheduleHelper;

    /**
     * @var ScheduleManagement
     */
    protected $scheduleManager;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * Markers constructor.
     *
     * @param Context                   $context                   Helper context
     * @param RetailerCollectionFactory $retailerCollectionFactory Retailer collection factory
     * @param DateTime                  $dateTime                  Stdlib datetime
     * @param CacheInterface            $cacheInterface            Cache interface
     * @param AddressFormatter          $addressFormatter          Address formatter
     * @param Data                      $storeLocatorHelper        Store locator helper
     * @param MapProviderInterface      $mapProvider               Map provider
     * @param Schedule                  $scheduleHelper            Schedule helper
     * @param ScheduleManagement        $scheduleManager           Schedule manager
     * @param SerializerInterface       $serializer                Serializer
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        RetailerCollectionFactory $retailerCollectionFactory,
        DateTime $dateTime,
        CacheInterface $cacheInterface,
        AddressFormatter $addressFormatter,
        StoreLocatorHelper $storeLocatorHelper,
        MapProviderInterface $mapProvider,
        ScheduleHelper $scheduleHelper,
        ScheduleManagement $scheduleManager,
        SerializerInterface $serializer
    ) {
        parent::__construct($context);
        $this->retailerCollectionFactory = $retailerCollectionFactory;
        $this->dateTime = $dateTime;
        $this->cacheInterface = $cacheInterface;
        $this->addressFormatter = $addressFormatter;
        $this->storeLocatorHelper = $storeLocatorHelper;
        $this->map = $mapProvider->getMap();
        $this->scheduleHelper = $scheduleHelper;
        $this->scheduleManager = $scheduleManager;
        $this->serializer = $serializer;
    }

    /**
     * Get markers data
     *
     * @param array $additionalAttributesToSelect Additional attributes to select
     *
     * @return array|bool|float|int|string|null
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function getMarkersData($additionalAttributesToSelect = [])
    {
        $defaultAttributesToSelect = ['name', 'url_key', 'contact_mail', 'contact_phone', 'contact_fax'];
        $attributesToSelect = array_values(array_merge($defaultAttributesToSelect, $additionalAttributesToSelect));

        $collection = $this->getRetailerCollection($attributesToSelect);
        $markers = $this->getMarkersFromCache($collection->getStoreId());

        if (!$markers) {
            Profiler::start(self::PROFILER_NAME . ':STORES');

            foreach ($collection as $retailer) {
                $markers[] = $this->getMarkerData($retailer, $attributesToSelect);
            }

            Profiler::stop(self::PROFILER_NAME . ':STORES');

            $markers = $this->serializer->serialize($markers);
            $this->setMarkersToCache($markers, $collection->getStoreId(), $this->getIdentities($collection));
        }

        return $this->serializer->unserialize($markers);
    }

    /**
     * Transform retailer data to marker data
     *
     * @param RetailerInterface $retailer   Retailer model
     * @param array             $attributes Attributes
     *
     * @return array
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function getMarkerData(RetailerInterface $retailer, array $attributes)
    {
        unset($attributes['name'], $attributes['url_key']);
        $address = $retailer->getExtensionAttributes()->getAddress();
        Profiler::start(self::PROFILER_NAME . ':STORES_DATA');
        $markerData = [
            'id'           => $retailer->getId(),
            'latitude'     => $address->getCoordinates()->getLatitude(),
            'longitude'    => $address->getCoordinates()->getLongitude(),
            'name'         => $retailer->getName(),
            'address'      => $this->addressFormatter->formatAddress($address, self::ADDRESS_FORMAT),
            'url'          => $this->storeLocatorHelper->getRetailerUrl($retailer),
            'directionUrl' => $this->map->getDirectionUrl($address->getCoordinates()),
            'setStoreData' => $this->getSetStorePostData($retailer),
            'image'        => $this->getRetailerImage($retailer),
            'postCode'     => $address->getPostcode(),
            'city'         => $address->getCity(),
            'street'       => $address->getStreet(),
        ];

        foreach ($attributes as $customAttribute) {
            $markerData[$customAttribute] = $retailer->getData($customAttribute) ?: '';
        }

        Profiler::stop(self::PROFILER_NAME . ':STORES_DATA');
        Profiler::start(self::PROFILER_NAME . ':STORES_SCHEDULE');

        $markerData['schedule'] = array_merge(
            $this->scheduleHelper->getConfig(),
            [
                'calendar'            => $this->scheduleManager->getCalendar($retailer),
                'openingHours'        => $this->scheduleManager->getWeekOpeningHours($retailer),
                'specialOpeningHours' => $retailer->getExtensionAttributes()->getSpecialOpeningHours(),
            ]
        );

        Profiler::stop(self::PROFILER_NAME . ':STORES_SCHEDULE');

        return $markerData;
    }

    /**
     * Get retailer collection
     *
     * @param array $attributesToSelect Attributes to select
     *
     * @return RetailerCollection
     */
    protected function getRetailerCollection($attributesToSelect)
    {
        $retailerCollection = $this->retailerCollectionFactory->create();
        $retailerCollection->addAttributeToSelect($attributesToSelect);
        $retailerCollection->addFieldToFilter('is_active', (int) true);
        $retailerCollection->addOrder('name', 'asc');

        return $retailerCollection;
    }

    /**
     * Get markers from cache
     *
     * @param int $storeId Current store id
     *
     * @return string
     */
    protected function getMarkersFromCache($storeId)
    {
        return $this->cacheInterface->load($this->getMarkersCacheKey($storeId));
    }

    /**
     * Set markers to cache
     *
     * @param string|array $markers    Markers data
     * @param int          $storeId    Store id
     * @param array        $identities Identities
     *
     * @return bool
     */
    protected function setMarkersToCache($markers, $storeId, array $identities)
    {
        if (is_array($markers)) {
            $markers = $this->serializer->serialize($markers);
        }

        return $this->cacheInterface->save($markers, $this->getMarkersCacheKey($storeId), $identities, 86400);
    }

    /**
     * Get set store post data - Url used for choose store
     *
     * @param RetailerInterface $retailer Retailer model
     *
     * @return array
     */
    protected function getSetStorePostData(RetailerInterface $retailer)
    {
        $setUrl   = $this->_urlBuilder->getUrl('storelocator/store/set', ['_secure' => true]);
        $postData = ['id' => $retailer->getId()];

        return ['action' => $setUrl, 'data' => $postData];
    }

    /**
     * Get retailer image
     *
     * @param RetailerInterface $retailer Retailer model
     *
     * @return false|string
     */
    protected function getRetailerImage(RetailerInterface $retailer)
    {
        return $retailer->getMediaPath() ? $this->getBaseRetailerImageUrl() . $retailer->getMediaPath() : false;
    }

    /**
     * Get base retailer image url
     *
     * @return string
     */
    protected function getBaseRetailerImageUrl()
    {
        return $this->_urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]) . 'seller/';
    }

    /**
     * Get markers data cache key
     *
     * @param int $storeId Current store id
     *
     * @return string
     */
    private function getMarkersCacheKey($storeId)
    {
        return sprintf('%s_%s_%s', self::MARKERS_DATA_CACHE_KEY, $storeId, $this->dateTime->gmtDate('Y-m-d'));
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @param RetailerCollection $retailerCollection Retailer collection
     *
     * @return array|string[]
     */
    private function getIdentities(RetailerCollection $retailerCollection)
    {
        return array_merge([self::CACHE_TAG], $retailerCollection->getNewEmptyItem()->getCacheTags() ?? []);
    }
}
