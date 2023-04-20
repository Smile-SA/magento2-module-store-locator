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

use Smile\Map\Api\Data\GeoPointInterfaceFactory;
use Smile\Retailer\Api\Data\RetailerInterface;
use Smile\Retailer\Model\Retailer\PostDataHandlerInterface;
use Smile\StoreLocator\Api\Data\RetailerAddressInterfaceFactory;

/**
 * Read addresses from post data.
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class AddressPostDataHandler implements PostDataHandlerInterface
{
    /**
     * @var RetailerAddressInterfaceFactory
     */
    private RetailerAddressInterfaceFactory $retailerAddressFactory;

    /**
     * @var GeoPointInterfaceFactory
     */
    private GeoPointInterfaceFactory $geoPointFactory;

    /**
     * Constructor.
     *
     * @param RetailerAddressInterfaceFactory $retailerAddressFactory Retailer address factory.
     * @param GeoPointInterfaceFactory        $geoPointFactory        Geo point factory.
     */
    public function __construct(
        RetailerAddressInterfaceFactory $retailerAddressFactory,
        GeoPointInterfaceFactory $geoPointFactory
    ) {
        $this->retailerAddressFactory = $retailerAddressFactory;
        $this->geoPointFactory        = $geoPointFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function getData(RetailerInterface $retailer, mixed $data): mixed
    {
        if (isset($data['address'])) {
            $addressData = $data['address'];

            if (isset($addressData['coordinates'])) {
                $addressData['coordinates'] = $this->geoPointFactory->create($addressData['coordinates']);
            }

            if (isset($addressData['street']) && !is_array($addressData['street'])) {
                $addressData['street'] = explode('\n', $addressData['street']);
            }

            $data['address'] = $this->retailerAddressFactory->create(['data' => $addressData]);
        }

        return $data;
    }
}
