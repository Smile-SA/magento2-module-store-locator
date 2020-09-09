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

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Smile\Map\Api\MapInterface;
use Smile\Map\Api\MapProviderInterface;
use Smile\Retailer\Model\ResourceModel\Retailer\CollectionFactory;
use Smile\StoreLocator\Helper\Markers as MarkersHelper;

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
    const CACHE_TAG = MarkersHelper::CACHE_TAG;

    /**
     * @var MapInterface
     */
    private $map;

    /**
     * @var RetailerCollectionFactory
     */
    private $retailerCollectionFactory;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var array
     */
    private $attributesToSelect;

    /**
     * @var MarkersHelper
     */
    private $markersHelper;

    /**
     * Constructor.
     *
     * @param Context              $context                      Block context.
     * @param MapProviderInterface $mapProvider                  Map provider.
     * @param CollectionFactory    $retailerCollectionFactory    Retailer collection factory.
     * @param SerializerInterface  $serializer                   JSON Serializer
     * @param MarkersHelper        $markersHelper                Markers helper
     * @param array                $additionalAttributesToSelect Additional attributes to select
     * @param array                $data                         Additional data.
     */
    public function __construct(
        Context $context,
        MapProviderInterface $mapProvider,
        CollectionFactory $retailerCollectionFactory,
        SerializerInterface $serializer,
        MarkersHelper $markersHelper,
        array $additionalAttributesToSelect = [],
        $data = []
    ) {
        parent::__construct($context, $data);
        $this->map                       = $mapProvider->getMap();
        $this->retailerCollectionFactory = $retailerCollectionFactory;
        $this->serializer                = $serializer;
        $this->markersHelper             = $markersHelper;
        $this->attributesToSelect        = $additionalAttributesToSelect;

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
    public function getJsLayout()
    {
        $jsLayout = $this->jsLayout;

        $jsLayout['components']['store-locator-search']['provider'] = $this->map->getIdentifier();
        $jsLayout['components']['store-locator-search']['markers']  = $this->markersHelper->getMarkersData(
            $this->attributesToSelect
        );

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
     * Return unique ID(s) for each object in system
     *
     * @return array|string[]
     */
    public function getIdentities()
    {
        return array_merge(
            [self::CACHE_TAG],
            $this->retailerCollectionFactory->create()->getNewEmptyItem()->getCacheTags() ?? []
        );
    }

    /**
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     * {@inheritDoc}
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');

        if ($breadcrumbsBlock) {
            $siteHomeUrl = $this->getBaseUrl();
            $breadcrumbsBlock->addCrumb('home', ['label' => __('Home'), 'title' => __('Go to Home Page'), 'link' => $siteHomeUrl]);
            $breadcrumbsBlock->addCrumb('search', ['label' => __('Our stores'), 'title' => __('Our stores')]);
        }

        $this->pageConfig->getTitle()->set(__('Shop Search'));
    }
}
