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
use Smile\StoreLocator\Model\Retailer\OpeningHoursReadHandler;
use Smile\StoreLocator\Model\Retailer\SpecialOpeningHoursReadHandler;

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
     * @var \Smile\StoreLocator\Model\Retailer\OpeningHoursReadHandler
     */
    private $openingHoursReadHandler;

    /**
     * @var \Smile\StoreLocator\Model\Retailer\SpecialOpeningHoursReadHandler
     */
    private $specialOpeningHoursReadHandler;

    /**
     * Constructor.
     *
     * @param AddressReadHandler             $addressReadHandler             Address read handler.
     * @param OpeningHoursReadHandler        $openingHoursReadHandler        Opening Hours read handler.
     * @param SpecialOpeningHoursReadHandler $specialOpeningHoursReadHandler Special Opening Hours read handler.
     */
    public function __construct(
        AddressReadHandler $addressReadHandler,
        OpeningHoursReadHandler $openingHoursReadHandler,
        SpecialOpeningHoursReadHandler $specialOpeningHoursReadHandler
    ) {
        $this->addressReadHandler = $addressReadHandler;
        $this->openingHoursReadHandler = $openingHoursReadHandler;
        $this->specialOpeningHoursReadHandler = $specialOpeningHoursReadHandler;
    }

    /**
     * Append address loading to the retailer collection.
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     *
     * @param RetailerCollection $collection Collection loaded.
     * @param \Closure           $proceed    Original method.
     * @param bool               $printQuery Print queries used to load the collection.
     * @param bool               $logQuery   Log queries used to load the collection.
     *
     * @return \Smile\Retailer\Model\ResourceModel\Retailer\Collection
     */
    public function aroundLoad(RetailerCollection $collection, \Closure $proceed, $printQuery = false, $logQuery = false)
    {

        if (!$collection->isLoaded()) {
            $proceed($printQuery, $logQuery);

            foreach ($collection->getItems() as $currentItem) {
                $this->addressReadHandler->execute($currentItem);
                $this->openingHoursReadHandler->execute($currentItem);
                $this->specialOpeningHoursReadHandler->execute($currentItem);
            }
        }

        return $collection;
    }
}
