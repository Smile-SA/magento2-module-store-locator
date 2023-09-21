<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Smile\Map\Api\MapInterface;
use Smile\Map\Api\MapProviderInterface;
use Smile\StoreLocator\Helper\Data;

/**
 * Store chooser block.
 */
class StoreChooser extends Template
{
    private MapInterface $map;

    public function __construct(
        Context $context,
        private Data $storeLocatorHelper,
        MapProviderInterface $mapProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->map = $mapProvider->getMap();
    }

    /**
     * @inheritdoc
     */
    public function getJsLayout()
    {
        $jsLayout = $this->jsLayout;

        $jsLayout['components']['top-storelocator-chooser']['storeLocatorHomeUrl']  = $this->getStoreLocatorHomeUrl();
        $jsLayout['components']['top-storelocator-chooser']['children']['geocoder']['provider'] = $this->map
            ->getIdentifier();

        $jsLayout['components']['top-storelocator-chooser']['children']['geocoder'] = array_merge(
            $jsLayout['components']['top-storelocator-chooser']['children']['geocoder'],
            $this->map->getConfig()
        );

        return json_encode($jsLayout);
    }

    /**
     * Get store locator home URL.
     */
    public function getStoreLocatorHomeUrl(): string
    {
        return $this->storeLocatorHelper->getHomeUrl();
    }
}
