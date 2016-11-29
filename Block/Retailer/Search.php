<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\StoreLocator
 * @author    Romain Ruaud <romain.ruaud@smile.fr>
 * @author    Guillaume Vrac <guillaume.vrac@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\StoreLocator\Block\Retailer;

use Smile\StoreLocator\Helper\Address as AddressHelper;
use Smile\Map\Api\MapInterface;

/**
 * Shop search block.
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 * @author   Guillaume Vrac <guillaume.vrac@smile.fr>
 */
class Search extends \Magento\Framework\View\Element\Template
{
    /**
     * @var MapInterface
     */
    private $map;

    /**
     * @var RetailerCollectionFactory
     */
    private $retailerCollectionFactory;

    /**
     * @var \Smile\StoreLocator\Helper\Data
     */
    private $storeLocatorHelper;

    /**
     * @var \Smile\StoreLocator\Helper\Address
     */
    private $addressHelper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Smile\Map\Api\MapProviderInterface $mapProvider,
        \Smile\Seller\Model\ResourceModel\Seller\CollectionFactory $retailerCollectionFactory,
        \Smile\StoreLocator\Helper\Data $storeLocatorHelper,
        \Smile\StoreLocator\Helper\Address $addressHelper,
        $data = []
    ) {
        parent::__construct($context, $data);
        $this->map                       = $mapProvider->getMap();
        $this->retailerCollectionFactory = $retailerCollectionFactory;
        $this->storeLocatorHelper        = $storeLocatorHelper;
        $this->addressHelper             = $addressHelper;
    }

    public function getBaseUrl()
    {
        return $this->storeLocatorHelper->getBaseUrl();
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
            $retailer = $this->getRetailer();

            $breadcrumbsBlock->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl()
                ]
                );

            $breadcrumbsBlock->addCrumb('search', ['label' => __('Our stores'), 'title' => __('Our stores')]);
        }
    }

    public function getJsLayout()
    {
        $jsLayout = $this->jsLayout;

        $jsLayout['components']['store-locator-search']['provider'] = $this->map->getIdentifier();
        $jsLayout['components']['store-locator-search']['markers']  = $this->getMarkers();
        $jsLayout['components']['store-locator-search']= array_merge($jsLayout['components']['store-locator-search'], $this->map->getConfig());

        return json_encode($jsLayout);
    }

    public function getMarkers()
    {
        /**
         *
         * @var \Smile\Seller\Model\ResourceModel\Seller\Collection $retailerCollection
         */
        $retailerCollection = $this->retailerCollectionFactory->create(['$attributeSetName' => 'Retailer']);
        $retailerCollection->addAttributeToSelect('*');
        $retailerCollection->addAttributeToFilter('latitude', ['notnull' => true]);
        $retailerCollection->addAttributeToFilter('longitude', ['notnull' => true]);
        $retailerCollection->addOrder('name', 'asc');
        $markers = [];
        foreach ($retailerCollection as $retailer) {
            $markerData = [
                'latitude'  => $retailer->getLatitude(),
                'longitude' => $retailer->getLongitude(),
                'name'      => $retailer->getName(),
                'address'   => $this->addressHelper->getFormattedAddress($retailer, AddressHelper::FORMAT_SHORT),
                'url'       => $this->_urlBuilder->getUrl('storelocator/store/view', ['id' => $retailer->getId()]),
            ];

            $markers[] = $markerData;

        }
        return $markers;
    }
}
