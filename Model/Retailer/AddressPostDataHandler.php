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
namespace Smile\StoreLocator\Model\Retailer;

/**
 * Read addresses from post data.
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class AddressPostDataHandler implements \Smile\Retailer\Model\Retailer\PostDataHandlerInterface
{
    /**
     * @var \Smile\StoreLocator\Api\Data\RetailerAddressInterfaceFactory
     */
    private $retailerAddressFactory;

    /**
     * @var \Smile\Map\Api\Data\GeoPointInterfaceFactory
     */
    private $geoPointFactory;

    /**
     * Constructor.
     *
     * @param \Smile\StoreLocator\Api\Data\RetailerAddressInterfaceFactory $retailerAddressFactory Retailer address factory.
     * @param \Smile\Map\Api\Data\GeoPointInterfaceFactory                 $geoPointFactory        Geo point factory.
     */
    public function __construct(
        \Smile\StoreLocator\Api\Data\RetailerAddressInterfaceFactory $retailerAddressFactory,
        \Smile\Map\Api\Data\GeoPointInterfaceFactory $geoPointFactory
    ) {
        $this->retailerAddressFactory = $retailerAddressFactory;
        $this->geoPointFactory        = $geoPointFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function getData(\Smile\Retailer\Api\Data\RetailerInterface $retailer, $data)
    {
        if (isset($data['address'])) {
            $addressData = $data['address'];

            if (isset($addressData['coordinates'])) {
                $addressData['coordinates'] = $this->geoPointFactory->create($addressData['coordinates']);
            }

            if (isset($addressData['street']) && !is_array($addressData['street'])) {
                $addressData['street'] = explode('\n', $addressData['street']);
            }

            unset($data['address']);
            $data['extension_attributes_list']['address'] = $this->retailerAddressFactory->create(['data' => $addressData]);
        }

        return $data;
    }
}
