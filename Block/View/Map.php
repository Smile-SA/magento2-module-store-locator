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
namespace Smile\StoreLocator\Block\View;

use Magento\Framework\View\Element\Template\Context;
use Smile\StoreLocator\Block\Retailer\AbstractView;
use Smile\StoreLocator\Api\Data\RetailerAddressInterface;
use Smile\Map\Api\MapInterface;
use Smile\Map\Model\AddressFormatter;

/**
 * Map rendering block.
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class Map extends \Smile\StoreLocator\Block\AbstractView
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
     * Constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context            Application context.
     * @param \Magento\Framework\Registry                      $coreRegistry       Application registry.
     * @param \Smile\Map\Api\MapProviderInterface              $mapProvider        Map configuration provider.
     * @param \Smile\StoreLocator\Helper\Data                  $storeLocatorHelper Store locacator helper.
     * @param \Smile\Map\Model\AddressFormatter                $addressFormatter   Address formatter.
     * @param array                                            $data               Additional data.
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Smile\Map\Api\MapProviderInterface $mapProvider,
        \Smile\StoreLocator\Helper\Data $storeLocatorHelper,
        \Smile\Map\Model\AddressFormatter $addressFormatter,
        array $data = []
    ) {
        parent::__construct($context, $coreRegistry, $data);

        $this->map                = $mapProvider->getMap();
        $this->addressFormatter   = $addressFormatter;
        $this->storeLocatorHelper = $storeLocatorHelper;
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
        $jsLayout['components']['store-locator-store-view']['markers'][] = [
            'latitude'  => $this->getCoordinates()->getLatitude(),
            'longitude' => $this->getCoordinates()->getLongitude(),
        ];

        $jsLayout['components']['store-locator-store-view'] = array_merge(
            $jsLayout['components']['store-locator-store-view'],
            $this->map->getConfig()
        );

        return json_encode($jsLayout);
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
}
