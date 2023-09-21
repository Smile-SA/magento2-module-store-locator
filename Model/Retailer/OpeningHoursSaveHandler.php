<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Model\Retailer;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Smile\StoreLocator\Model\ResourceModel\RetailerTimeSlot as TimeSlotResource;

/**
 * Save Handler for Retailer Opening Hours.
 */
class OpeningHoursSaveHandler implements ExtensionInterface
{
    public function __construct(private TimeSlotResource $resource)
    {
    }

    /**
     * @inheritdoc
     */
    public function execute($entity, $arguments = [])
    {
        if ($entity->getOpeningHours()) {
            $this->resource->saveTimeSlots((int) $entity->getId(), 'opening_hours', $entity->getOpeningHours());
        }

        return $entity;
    }
}
