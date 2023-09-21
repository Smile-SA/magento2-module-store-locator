<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Model\Retailer;

use Smile\Map\Api\Data\GeoPointInterfaceFactory;
use Smile\Retailer\Api\Data\RetailerInterface;
use Smile\Retailer\Model\Retailer\PostDataHandlerInterface;
use Smile\StoreLocator\Api\Data\RetailerAddressInterfaceFactory;

/**
 * Read addresses from post data.
 */
class AddressPostDataHandler implements PostDataHandlerInterface
{
    public function __construct(
        private RetailerAddressInterfaceFactory $retailerAddressFactory,
        private GeoPointInterfaceFactory $geoPointFactory
    ) {
    }

    /**
     * @inheritdoc
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
