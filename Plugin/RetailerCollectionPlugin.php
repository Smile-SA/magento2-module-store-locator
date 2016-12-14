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

use Smile\Retailer\Model\ResourceModel\Retailer\Collection as RetailerCollection;
use Smile\StoreLocator\Model\Retailer\AddressReadHandler;

/**
 * Retailer collection plugin.
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class RetailerCollectionPlugin
{
    /**
     * @var AddressReadHandler
     */
    private $addressReadHandler;

    /**
     * Constructor.
     *
     * @param AddressReadHandler $addressReadHandler Address read handler.
     */
    public function __construct(AddressReadHandler $addressReadHandler)
    {
        $this->addressReadHandler = $addressReadHandler;
    }

    /**
     * Append address loading to the retailer collection.
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     *
     * @param RetailerCollection $collection Collection loaded.
     * @param \Closure           $proceed    Original method.
     * @param string             $printQuery Print queries used to load the collection.
     * @param string             $logQuery   Log queries used to load the collection.
     *
     * @return \Smile\Retailer\Model\ResourceModel\Retailer\Collection
     */
    public function aroundLoad(RetailerCollection $collection, \Closure $proceed, $printQuery = false, $logQuery = false)
    {

        if (!$collection->isLoaded()) {
            $proceed($printQuery, $logQuery);

            foreach ($collection->getItems() as $currentItem) {
                $this->addressReadHandler->execute($currentItem);
            }
        }

        return $collection;
    }
}
