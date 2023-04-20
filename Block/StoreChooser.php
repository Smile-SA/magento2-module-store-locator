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

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Smile\Map\Api\MapInterface;
use Smile\Map\Api\MapProviderInterface;
use Smile\StoreLocator\Helper\Data;

/**
 * Store chooser block.
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class StoreChooser extends Template
{
    /**
     * @var Data
     */
    private Data $storeLocatorHelper;

    /**
     * @var MapInterface
     */
    private MapInterface $map;

    /**
     * Constructor.
     *
     * @param Context               $context            Template context.
     * @param Data                  $storeLocatorHelper Store locator helper.
     * @param MapProviderInterface  $mapProvider        Map Provider.
     * @param array                 $data               Additional data.
     */
    public function __construct(
        Context $context,
        Data $storeLocatorHelper,
        MapProviderInterface $mapProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->storeLocatorHelper = $storeLocatorHelper;
        $this->map = $mapProvider->getMap();
    }

    /**
     * {@inheritDoc}
     */
    public function getJsLayout(): string
    {
        $jsLayout = $this->jsLayout;

        $jsLayout['components']['top-storelocator-chooser']['storeLocatorHomeUrl']  = $this->getStoreLocatorHomeUrl();
        $jsLayout['components']['top-storelocator-chooser']['children']['geocoder']['provider'] = $this->map->getIdentifier();

        $jsLayout['components']['top-storelocator-chooser']['children']['geocoder'] = array_merge(
            $jsLayout['components']['top-storelocator-chooser']['children']['geocoder'],
            $this->map->getConfig()
        );

        return json_encode($jsLayout);
    }

    /**
     * Get store locator home URL.
     *
     * @return string
     */
    public function getStoreLocatorHomeUrl(): string
    {
        return $this->storeLocatorHelper->getHomeUrl();
    }
}
