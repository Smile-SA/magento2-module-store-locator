<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\StoreLocator
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\StoreLocator\Plugin;

use Smile\Retailer\Api\Data\RetailerInterface;
use Smile\Seller\Ui\Component\Seller\Form\DataProvider;

/**
 * Retailer form data provider plugin.
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class RetailerEditFormPlugin
{
    /**
     * @param DataProvider  $subject
     * @param array         $result
     *
     * @return array
     */
    public function afterGetData(DataProvider $subject, $result)
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
     *
     * @param DataProvider $dataProvider DataProvider.
     *
     * @return NULL|RetailerInterface
     */
    private function getRetailer(DataProvider $dataProvider)
    {
        $retailer = $dataProvider->getCollection()->getFirstItem();

        return $retailer instanceof RetailerInterface ? $retailer : null;
    }
}
