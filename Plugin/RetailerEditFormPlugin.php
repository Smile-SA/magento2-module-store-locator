<?php

namespace Smile\StoreLocator\Plugin;

use Smile\Retailer\Api\Data\RetailerInterface;
use Smile\Seller\Ui\Component\Seller\Form\DataProvider;

/**
 * Retailer form data provider plugin.
 */
class RetailerEditFormPlugin
{
    /**
     * Add latitude and longitude to address data.
     */
    public function afterGetData(DataProvider $subject, array $result): array
    {
        $retailer = $this->getRetailer($subject);

        if ($retailer !== null && $retailer->getExtensionAttributes()->getAddress()) {
            $address = $retailer->getExtensionAttributes()->getAddress();
            $result[$retailer->getId()]['address'] = $address->getData();

            if ($address->getCoordinates()) {
                $result[$retailer->getId()]['address']['coordinates'] = [
                    'latitude'  => $address->getCoordinates()->getLatitude(),
                    'longitude' => $address->getCoordinates()->getLongitude(),
                ];
            }
        }

        return $result;
    }

    /**
     * Return the currently edited retailer.
     */
    private function getRetailer(DataProvider $dataProvider): ?RetailerInterface
    {
        $retailer = $dataProvider->getCollection()->getFirstItem();

        return $retailer instanceof RetailerInterface ? $retailer : null;
    }
}
