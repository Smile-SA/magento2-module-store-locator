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
     * Append address to the dataprovider data.
     *
     * @param \Smile\Seller\Ui\Component\Seller\Form\DataProvider $dataProvider DataProvider.
     * @param \Closure                                            $proceed      Original method.
     *
     * @return array
     */
    public function aroundGetData(\Smile\Seller\Ui\Component\Seller\Form\DataProvider $dataProvider, \Closure $proceed)
    {
        $data     = $proceed();
        $retailer = $this->getRetailer($dataProvider);

        if ($retailer !== null && $retailer->getExtensionAttributes()->getAddress()) {
            $address = $retailer->getExtensionAttributes()->getAddress();
            $data[$retailer->getId()]['address'] = $address->getData();

            if ($address->getCoordinates()) {
                $data[$retailer->getId()]['address']['coordinates'] = [
                    'latitude'  => $address->getCoordinates()->getLatitude(),
                    'longitude' => $address->getCoordinates()->getLongitude(),
                ];
            }
        }

        return $data;
    }

    /**
     * Return the currently edited retailer.
     *
     * @param \Smile\Seller\Ui\Component\Seller\Form\DataProvider $dataProvider DataProvider.
     *
     * @return NULL|\Smile\Retailer\Api\Data\RetailerInterface
     */
    private function getRetailer(\Smile\Seller\Ui\Component\Seller\Form\DataProvider $dataProvider)
    {
        $retailer = $dataProvider->getCollection()->getFirstItem();

        return $retailer instanceof RetailerInterface ? $retailer : null;
    }
}
