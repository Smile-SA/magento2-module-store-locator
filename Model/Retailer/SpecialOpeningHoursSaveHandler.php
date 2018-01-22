<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\StoreLocator
 * @author    Romain Ruaud <romain.ruaud@smile.fr>
 * @copyright 2017 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\StoreLocator\Model\Retailer;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Smile\StoreLocator\Model\ResourceModel\RetailerTimeSlot as TimeSlotResource;

/**
 * Save Handler for Retailer Special Opening Hours
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
class SpecialOpeningHoursSaveHandler implements ExtensionInterface
{
    /**
     * @var \Smile\StoreLocator\Model\ResourceModel\RetailerTimeSlot
     */
    private $resource;

    /**
     * OpeningHoursSaveHandler constructor.
     *
     * @param \Smile\StoreLocator\Model\ResourceModel\RetailerTimeSlot $resource Resource Model
     */
    public function __construct(TimeSlotResource $resource)
    {
        $this->resource = $resource;
    }

    /**
     * {@inheritDoc}
     */
    public function execute($entity, $arguments = [])
    {
        if (empty($entity->getSpecialOpeningHours())) {
            $this->resource->deleteByRetailerId($entity->getId(), 'special_opening_hours');
        }

        if ($entity->getSpecialOpeningHours()) {
            $this->resource->saveTimeSlots($entity->getId(), 'special_opening_hours', $entity->getSpecialOpeningHours());
        }

        return $entity;
    }
}
