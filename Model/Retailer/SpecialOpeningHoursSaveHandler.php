<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Model\Retailer;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Smile\StoreLocator\Model\ResourceModel\RetailerTimeSlot as TimeSlotResource;

/**
 * Save Handler for Retailer Special Opening Hours.
 */
class SpecialOpeningHoursSaveHandler implements ExtensionInterface
{
    public function __construct(private TimeSlotResource $resource)
    {
    }

    /**
     * @inheritdoc
     */
    public function execute($entity, $arguments = [])
    {
        if (empty($entity->getSpecialOpeningHours())) {
            $this->resource->deleteByRetailerId((int) $entity->getId(), 'special_opening_hours');
        }

        if ($entity->getSpecialOpeningHours()) {
            $this->resource->saveTimeSlots(
                (int) $entity->getId(),
                'special_opening_hours',
                $entity->getSpecialOpeningHours()
            );
        }

        return $entity;
    }
}
