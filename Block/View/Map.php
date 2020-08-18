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

use Smile\Map\Api\MapInterface;
use Smile\Map\Model\AddressFormatter;
use Smile\Retailer\Api\Data\RetailerInterface;
use Smile\StoreLocator\Api\Data\RetailerAddressInterface;
use Smile\StoreLocator\Block\AbstractView;


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
    private $map;

    /**
     * @var \Smile\StoreLocator\Helper\Data
     */
    private $storeLocatorHelper;

    /**
     * @var \Smile\Map\Model\AddressFormatter
     */
    private $addressFormatter;

    /**
     * @var \Smile\StoreLocator\Helper\Schedule
     */
    private $scheduleHelper;

    /**
     * @var \Smile\StoreLocator\Model\Retailer\ScheduleManagement
     */
    private $scheduleManager;


    /**
     * @var RetailerCollectionFactory
     */
    private $retailerCollectionFactory;


    /**
     * Constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context      $context                Application context.
     * @param \Magento\Framework\Registry                           $coreRegistry           Application registry.
     * @param \Smile\Map\Api\MapProviderInterface                   $mapProvider            Map configuration provider.
     * @param \Smile\StoreLocator\Helper\Data                       $storeLocatorHelper     Store locacator helper.
     * @param AddressFormatter                                      $addressFormatter       Address formatter.
     * @param \Smile\StoreLocator\Helper\Schedule                   $scheduleHelper         Schedule Helper
     * @param \Smile\StoreLocator\Model\Retailer\ScheduleManagement $scheduleManagement     Schedule Management
     * @param array                                                 $data                   Additional data.
     */

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Smile\Map\Api\MapProviderInterface $mapProvider,
        \Smile\StoreLocator\Helper\Data $storeLocatorHelper,
        \Smile\Map\Model\AddressFormatter $addressFormatter,
        \Smile\StoreLocator\Helper\Schedule $scheduleHelper,
        \Smile\StoreLocator\Model\Retailer\ScheduleManagement $scheduleManagement,
        \Smile\Retailer\Model\ResourceModel\Retailer\CollectionFactory $retailerCollectionFactory,
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
     * @return RetailerAddressInterface
     */
    public function getAddress()
    {
        return $this->getRetailer()->getAddress();
    }


    public function getId() {
        return $this->getRetailer()->getId();
    }

    /**
     * Returns current store coordinates.
     *
     * @return \Smile\Map\Api\Data\GeoPointInterface
     */
    public function getCoordinates()
    {
        return $this->getAddress()->getCoordinates();
    }

    /**
     * {@inheritDoc}
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
    public function getMarkerData() {

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
            'mail' => $this->getContactMailMail(),
            'closestShops' => $this->collectionFull(),
        ];
        $storeMarkerData['schedule'] = array_merge(
            $this->scheduleHelper->getConfig(),
            [
                'calendar'            => $this->scheduleManager->getCalendar($retailer),
                'openingHours'        => $this->scheduleManager->getWeekOpeningHours($retailer),
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
    public function getStoreListUrl()
    {
        return $this->storeLocatorHelper->getHomeUrl();
    }

    /**
     * Get address formatted in HTML.
     *
     * @return string
     */
    public function getAddressHtml()
    {
        return $this->addressFormatter->formatAddress($this->getAddress(), AddressFormatter::FORMAT_HTML);
    }


    /**
     * Get URL used to redirect user to the direction API.
     *
     * @return string
     */
    public function getDirectionUrl()
    {
        return $this->map->getDirectionUrl($this->getCoordinates());
    }

    /**
     * Returns retailer description.
     *
     * @return null|string
     */
    public function getDescription()
    {
        return $this->getRetailer()->getDescription();
    }

    /**
     * * Get media part.
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getImageUrl(){

        $currentStore = $this->_storeManager->getStore();
        $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

        return $mediaUrl;
    }

    /**
     * Get image name.
     *
     * @return bool|string
     */
    protected function getMediaPath() {

        $retailer = $this->getRetailer();

        return $retailer ? $retailer->getMediaPath() : false;
    }

    /**
     * Get current retailer.
     *
     * @return \Smile\Retailer\Api\Data\RetailerInterface
     */
    protected function currentRetailer() {

        $retailer = $this->getRetailer();

        return $retailer;
    }

    /**
     * Get full image url.
     *
     * @return bool|string
     */
    public function getImage() {

        $mediaPath = $this->getMediaPath();
        $imageUrlRetailer = $this->getImageUrl() . 'seller/';
        $image = $mediaPath ? $imageUrlRetailer . $mediaPath : false;

        return $image;
    }

    /**
     * Get store name.
     *
     * @return string
     */
    public function getStoreName() {

        $name = $this->getRetailer()->getName();

        return $name;
    }

    /**
     * Get phone number.
     *
     * @return null|string
     */
    public function getPhone() {

        $retailer = $this->getRetailer();
        $phone = $retailer->getContactPhone();
        $result = $phone ? $phone : false;

        return $result;
    }

    /**
     * Get email address.
     *
     * @return null|string
     */
    public function getContactMailMail() {

        $retailer = $this->getRetailer();
        $mail = $retailer->getMail();
        $result = $mail ? $mail : false;

        return $result;
    }

    /**
     * Get all exist markers.
     *
     * @return mixed
     */

    public function getAllMarkers() {
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
    public function collectionFull() {
        $collection = $this->getAllMarkers();

        $markers  = null;

        if (!$markers) {
            /** @var RetailerInterface $retailer */
            $imageUrlRetailer = $this->getImageUrl().'seller/';
            foreach ($collection as $retailer) {
                $address = $retailer->getExtensionAttributes()->getAddress();
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
                ];

                foreach (['contact_mail', 'contact_phone', 'contact_mail'] as $contactAttribute) {
                    $markerData[$contactAttribute] = $retailer->getData($contactAttribute) ? $retailer->getData($contactAttribute) : '';
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
