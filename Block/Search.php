<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Block;

use DateTime;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\DataObject;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Profiler;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\Store;
use Magento\Theme\Block\Html\Breadcrumbs;
use Smile\Map\Api\MapInterface;
use Smile\Map\Api\MapProviderInterface;
use Smile\Map\Model\AddressFormatter;
use Smile\Retailer\Api\Data\RetailerExtensionInterface;
use Smile\Retailer\Api\Data\RetailerInterface;
use Smile\Retailer\Model\ResourceModel\Retailer\Collection;
use Smile\Retailer\Model\ResourceModel\Retailer\CollectionFactory as RetailerCollectionFactory;
use Smile\StoreLocator\Helper\Data;
use Smile\StoreLocator\Helper\Schedule;
use Smile\StoreLocator\Model\Retailer\ScheduleManagement;

/**
 * Shop search block.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Search extends Template implements IdentityInterface
{
    private const DEFAULT_ATTRIBUTES_TO_SELECT = ['name', 'url_key', 'contact_mail', 'contact_phone', 'contact_fax'];
    public const CACHE_TAG = 'smile_store_locator_markers';

    private MapInterface $map;
    private CacheInterface $cacheInterface;
    private array $attributesToSelect;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        MapProviderInterface $mapProvider,
        private RetailerCollectionFactory $retailerCollectionFactory,
        private Data $storeLocatorHelper,
        private AddressFormatter $addressFormatter,
        private Schedule $scheduleHelper,
        private ScheduleManagement $scheduleManagement,
        private SerializerInterface $serializer,
        array $additionalAttributesToSelect = [],
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->map = $mapProvider->getMap();
        $this->cacheInterface = $context->getCache();
        $this->attributesToSelect = array_values(
            array_merge(self::DEFAULT_ATTRIBUTES_TO_SELECT, $additionalAttributesToSelect)
        );

        $this->addData(
            [
                'cache_lifetime' => false,
                'cache_tags'     => $this->getIdentities(),
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function getJsLayout()
    {
        $jsLayout = $this->jsLayout;

        $jsLayout['components']['store-locator-search']['provider'] = $this->map->getIdentifier();
        $jsLayout['components']['store-locator-search']['markers']  = $this->getMarkers();
        $jsLayout['components']['store-locator-search'] = array_merge(
            $jsLayout['components']['store-locator-search'],
            $this->map->getConfig()
        );

        $jsLayout['components']['store-locator-search']['children']['geocoder']['provider'] = $this->map
            ->getIdentifier();
        $jsLayout['components']['store-locator-search']['children']['geocoder'] = array_merge(
            $jsLayout['components']['store-locator-search']['children']['geocoder'],
            $this->map->getConfig()
        );

        if ($this->getRequest()->getParam('query', false)) {
            $query = $this->getRequest()->getParam('query', false);
            $jsLayout['components']['store-locator-search']['children']['geocoder']['fulltextSearch'] =
                $this->escapeJsQuote($query);
        }

        return $this->serializer->serialize($jsLayout);
    }

    /**
     * List of markers displayed on the map.
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function getMarkers(): array
    {
        $collection = $this->getRetailerCollection();
        $todayDate = (new DateTime())->format('Y-m-d');
        $cacheKey = sprintf('%s_%s_%s', 'smile_storelocator_search', $collection->getStoreId(), $todayDate);
        $markers = $this->cacheInterface->load($cacheKey);
        $attributes = $this->attributesToSelect;
        unset($attributes['name'], $attributes['url_key']);

        if (!$markers) {
            Profiler::start('SmileStoreLocator:STORES');
            $imageUrlRetailer = $this->getImageUrl() . 'seller/';
            $markers = [];
            /** @var RetailerInterface $retailer */
            foreach ($collection as $retailer) {
                /** @var RetailerExtensionInterface $retailerExtensionAttr */
                $retailerExtensionAttr = $retailer->getExtensionAttributes();
                $address = $retailerExtensionAttr->getAddress();
                Profiler::start('SmileStoreLocator:STORES_DATA');
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
                    'postCode' => $address->getPostcode(),
                    'city' => $address->getCity(),
                    'street' => $address->getStreet(),
                ];
                Profiler::stop('SmileStoreLocator:STORES_DATA');
                foreach ($attributes as $contactAttribute) {
                    $markerData[$contactAttribute] = $retailer->getData($contactAttribute)
                        ? $retailer->getData($contactAttribute)
                        : '';
                }
                Profiler::start('SmileStoreLocator:STORES_SCHEDULE');

                // phpcs:ignore Magento2.Performance.ForeachArrayMerge.ForeachArrayMerge
                $markerData['schedule'] = array_merge(
                    $this->scheduleHelper->getConfig(),
                    [
                        'calendar' => $this->scheduleManagement->getCalendar($retailer),
                        'openingHours' => $this->scheduleManagement->getWeekOpeningHours($retailer),
                        'specialOpeningHours' => $retailerExtensionAttr->getSpecialOpeningHours(),
                    ]
                );

                Profiler::stop('SmileStoreLocator:STORES_SCHEDULE');
                $marketDataObject = new DataObject($markerData);
                $this->_eventManager->dispatch('smile_store_locator_marker_data', ['data_object' => $marketDataObject]);
                $markers[] = $marketDataObject->getData();
            }
            Profiler::stop('SmileStoreLocator:STORES');

            $markers = $this->serializer->serialize($markers);
            $this->cacheInterface->save(
                $markers,
                $cacheKey,
                $this->getIdentities(),
                86400
            );
        }

        return $this->serializer->unserialize($markers);
    }

    /**
     * @inheritdoc
     */
    public function getIdentities(): array
    {
        return array_merge([self::CACHE_TAG], $this->getRetailerCollection()->getNewEmptyItem()->getCacheTags() ?? []);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareLayout(): self
    {
        parent::_prepareLayout();

        /** @var Breadcrumbs|bool $breadcrumbsBlock */
        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbsBlock) {
            $siteHomeUrl = $this->getBaseUrl();
            $breadcrumbsBlock->addCrumb(
                'home',
                ['label' => __('Home'), 'title' => __('Go to Home Page'), 'link' => $siteHomeUrl]
            );
            $breadcrumbsBlock->addCrumb(
                'search',
                ['label' => __('Our stores'), 'title' => __('Our stores')]
            );
        }

        $this->pageConfig->getTitle()->set(__('Shop Search'));

        return $this;
    }

    /**
     * Collection of displayed retailers.
     *
     * @throws LocalizedException
     */
    private function getRetailerCollection(): Collection
    {
        $retailerCollection = $this->retailerCollectionFactory->create();
        $retailerCollection->addAttributeToSelect($this->attributesToSelect);
        $retailerCollection->addFieldToFilter('is_active', 1);
        $retailerCollection->addOrder('name', 'asc');

        return $retailerCollection;
    }

    /**
     * Get the JSON post data used to build the set store link.
     */
    private function getSetStorePostData(RetailerInterface $retailer): array
    {
        $setUrl   = $this->_urlBuilder->getUrl('storelocator/store/set', ['_secure' => true]);
        $postData = ['id' => $retailer->getId()];

        return ['action' => $setUrl, 'data' => $postData];
    }

    /**
     * Get base media url.
     *
     * @throws NoSuchEntityException
     */
    public function getImageUrl(): string
    {
        /** @var Store $currentStore */
        $currentStore = $this->_storeManager->getStore();

        return $currentStore->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
    }
}
