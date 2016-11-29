<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade Smile Elastic Suite to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\LocalizedRetailer
 * @author    Romain Ruaud <romain.ruaud@smile.fr>
 * @author    Guillaume Vrac <guillaume.vrac@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\StoreLocator\Block\Retailer\View;

use Magento\Framework\View\Element\Template\Context;
use Smile\StoreLocator\Block\Retailer\AbstractView;


class Map extends \Smile\StoreLocator\Block\Retailer\AbstractView
{
    /**
     * @var MapInterface
     */
    private $map;

    /**
     * @var \Smile\StoreLocator\Helper\Data
     */
    private $storeLocatorHelper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Smile\Map\Api\MapProviderInterface $mapProvider,
        \Smile\StoreLocator\Helper\Data $storeLocatorHelper,
        array $data = []
    ) {
        parent::__construct($context, $coreRegistry, $data);
        $this->map                = $mapProvider->getMap();
        $this->storeLocatorHelper = $storeLocatorHelper;
    }

    public function getJsLayout()
    {
        $jsLayout = $this->jsLayout;

        $jsLayout['components']['store-locator-store-view']['provider']  = $this->map->getIdentifier();
        $jsLayout['components']['store-locator-store-view']['markers'][] = [
            'latitude' => $this->getRetailer()->getLatitude(),
            'longitude' => $this->getRetailer()->getLongitude()
        ];

        $jsLayout['components']['store-locator-store-view']= array_merge(
            $jsLayout['components']['store-locator-store-view'],
            $this->map->getConfig()
        );

        return json_encode($jsLayout);
    }
}