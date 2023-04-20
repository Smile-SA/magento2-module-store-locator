<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\StoreLocator
 * @author    Aurelien FOUCRET <aurelien.foucret@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\StoreLocator\Block;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\DataObject;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Profiler;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Smile\Map\Api\MapInterface;
use Smile\Map\Api\MapProviderInterface;
use Smile\Map\Model\AddressFormatter;
use Smile\Retailer\Api\Data\RetailerInterface;
use Smile\Retailer\Model\ResourceModel\Retailer\Collection;
use Smile\Retailer\Model\ResourceModel\Retailer\CollectionFactory as RetailerCollectionFactory;
use Smile\StoreLocator\Helper\Data;
use Smile\StoreLocator\Helper\Schedule;
use Smile\StoreLocator\Model\Retailer\ScheduleManagement;

/**
 * Shop search block.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class Search extends Template implements IdentityInterface
{
    const DEFAULT_ATTRIBUTES_TO_SELECT = ['name', 'url_key', 'contact_mail', 'contact_phone', 'contact_fax'];
    const CACHE_TAG = 'smile_store_locator_markers';

    /**
     * @var MapInterface
     */
    private MapInterface $map;

    /**
     * @var RetailerCollectionFactory
     */
    private RetailerCollectionFactory $retailerCollectionFactory;

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
     * @var CacheInterface
     */
    private CacheInterface $cacheInterface;

    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * @var array
     */
    private array $attributesToSelect;

    /**
     * Constructor.
     *
     * @param Context                   $context                      Block context.
     * @param MapProviderInterface      $mapProvider                  Map provider.
     * @param RetailerCollectionFactory $retailerCollectionFactory    Retailer collection factory.
     * @param Data                      $storeLocatorHelper           Store locator helper.
     * @param AddressFormatter          $addressFormatter             Address formatter tool.
     * @param Schedule                  $scheduleHelper               Schedule Helper
     * @param ScheduleManagement        $scheduleManagement           Schedule Management
     * @param SerializerInterface       $serializer                   JSON Serializer
     * @param array                     $additionalAttributesToSelect Additional attributes to select
     * @param array                     $data                         Additional data.
     */
    public function __construct(
        Context $context,
        MapProviderInterface $mapProvider,
        RetailerCollectionFactory $retailerCollectionFactory,
        Data $storeLocatorHelper,
        AddressFormatter $addressFormatter,
        Schedule $scheduleHelper,
        ScheduleManagement $scheduleManagement,
        SerializerInterface $serializer,
        array $additionalAttributesToSelect = [],
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->map                       = $mapProvider->getMap();
        $this->retailerCollectionFactory = $retailerCollectionFactory;
        $this->storeLocatorHelper        = $storeLocatorHelper;
        $this->addressFormatter          = $addressFormatter;
        $this->scheduleHelper            = $scheduleHelper;
        $this->scheduleManager           = $scheduleManagement;
        $this->cacheInterface            = $context->getCache();
        $this->serializer                = $serializer;
        $this->attributesToSelect        = array_values(
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
     * {@inheritDoc}
     */
    public function getJsLayout(): string
    {
        $jsLayout = $this->jsLayout;

        $jsLayout['components']['store-locator-search']['provider'] = $this->map->getIdentifier();
        $jsLayout['components']['store-locator-search']['markers']  = $this->getMarkers();
        $jsLayout['components']['store-locator-search'] = array_merge(
            $jsLayout['components']['store-locator-search'],
            $this->map->getConfig()
        );

        $jsLayout['components']['store-locator-search']['children']['geocoder']['provider'] = $this->map->getIdentifier();
        $jsLayout['components']['store-locator-search']['children']['geocoder'] = array_merge(
            $jsLayout['components']['store-locator-search']['children']['geocoder'],
            $this->map->getConfig()
        );

        if ($this->getRequest()->getParam('query', false)) {
            $query = $this->getRequest()->getParam('query', false);
            $jsLayout['components']['store-locator-search']['children']['geocoder']['fulltextSearch'] = $this->escapeJsQuote($query);
        }

        return $this->serializer->serialize($jsLayout);
    }

    /**
     * List of markers displayed on the map.
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * @return array
     */
    public function getMarkers(): array
    {
        $collection = $this->getRetailerCollection();
        $todayDate = (new \DateTime())->format('Y-m-d');
        $cacheKey = sprintf('%s_%s_%s', 'smile_storelocator_search', $collection->getStoreId(), $todayDate);
        $markers = $this->cacheInterface->load($cacheKey);
        $attributes = $this->attributesToSelect;
        unset($attributes['name'], $attributes['url_key']);

        if (!$markers) {
            Profiler::start('SmileStoreLocator:STORES');
            /** @var RetailerInterface $retailer */
            $imageUrlRetailer = $this->getImageUrl().'seller/';
            $markers = [];
            foreach ($collection as $retailer) {
                $address = $retailer->getExtensionAttributes()->getAddress();
                Profiler::start('SmileStoreLocator:STORES_DATA');
                $image = $retailer->getMediaPath() ? $imageUrlRetailer.$retailer->getMediaPath() : false;
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
                    'postCode'     => $address->getPostcode(),
                    'city'         => $address->getCity(),
                    'street'       => $address->getStreet(),
                ];
                Profiler::stop('SmileStoreLocator:STORES_DATA');
                foreach ($attributes as $contactAttribute) {
                    $markerData[$contactAttribute] = $retailer->getData($contactAttribute) ? $retailer->getData($contactAttribute) : '';
                }
                Profiler::start('SmileStoreLocator:STORES_SCHEDULE');
                $markerData['schedule'] = array_merge(
                    $this->scheduleHelper->getConfig(),
                    [
                        'calendar'            => $this->scheduleManager->getCalendar($retailer),
                        'openingHours'        => $this->scheduleManager->getWeekOpeningHours($retailer),
                        'specialOpeningHours' => $retailer->getExtensionAttributes()->getSpecialOpeningHours(),
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
     * Return unique ID(s) for each object in system
     *
     * @return array|string[]
     */
    public function getIdentities(): array
    {
        return array_merge([self::CACHE_TAG], $this->getRetailerCollection()->getNewEmptyItem()->getCacheTags() ?? []);
    }

    /**
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     * {@inheritDoc}
     */
    protected function _prepareLayout(): void
    {
        parent::_prepareLayout();

        if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
            $siteHomeUrl = $this->getBaseUrl();
            $breadcrumbsBlock->addCrumb('home', ['label' => __('Home'), 'title' => __('Go to Home Page'), 'link' => $siteHomeUrl]);
            $breadcrumbsBlock->addCrumb('search', ['label' => __('Our stores'), 'title' => __('Our stores')]);
        }

        $this->pageConfig->getTitle()->set(__('Shop Search'));
    }

    /**
     * Collection of displayed retailers.
     *
     * @return Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getRetailerCollection(): Collection
    {
        $retailerCollection = $this->retailerCollectionFactory->create();
        $retailerCollection->addAttributeToSelect($this->attributesToSelect);
        $retailerCollection->addFieldToFilter('is_active', (int) true);
        $retailerCollection->addOrder('name', 'asc');

        return $retailerCollection;
    }

    /**
     * Get the JSON post data used to build the set store link.
     *
     * @param RetailerInterface $retailer The store
     *
     * @return array
     */
    private function getSetStorePostData(RetailerInterface $retailer): array
    {
        $setUrl   = $this->_urlBuilder->getUrl('storelocator/store/set', ['_secure' => true]);
        $postData = ['id' => $retailer->getId()];

        return ['action' => $setUrl, 'data' => $postData];
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getImageUrl(): string
    {
        $currentStore = $this->_storeManager->getStore();
        $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $mediaUrl;
    }
}
