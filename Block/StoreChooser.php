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
     * @var \Smile\StoreLocator\Helper\Data
     */
    private $storeLocatorHelper;

    /**
     * @var \Smile\Map\Api\MapInterface
     */
    private $map;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context            Template context.
     * @param \Smile\StoreLocator\Helper\Data                  $storeLocatorHelper Store locator helper.
     * @param \Smile\Map\Api\MapProviderInterface              $mapProvider        Map Provider.
     * @param array                                            $data               Additional data.
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Smile\StoreLocator\Helper\Data $storeLocatorHelper,
        \Smile\Map\Api\MapProviderInterface $mapProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->storeLocatorHelper = $storeLocatorHelper;
        $this->map = $mapProvider->getMap();
    }

    /**
     * {@inheritDoc}
     */
    public function getJsLayout()
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
    public function getStoreLocatorHomeUrl()
    {
        return $this->storeLocatorHelper->getHomeUrl();
    }
}
