<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Model\Retailer;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Smile\StoreLocator\Api\Data\RetailerTimeSlotInterface;
use Smile\StoreLocator\Model\Data\RetailerTimeSlotConverter;
use Smile\StoreLocator\Model\ResourceModel\RetailerTimeSlot as TimeSlotResource;

/**
 * Read Handler for Retailer Opening Hours.
 */
class OpeningHoursReadHandler implements ExtensionInterface
{
    public function __construct(
        private RetailerTimeSlotConverter $converter,
        private TimeSlotResource $resource
    ) {
    }

    /**
     * @inheritdoc
     */
    public function execute($entity, $arguments = [])
    {
        $timeSlots = $this->resource->getTimeSlots((int) $entity->getId(), 'opening_hours');

        $openingHours = $this->converter->toEntity($timeSlots, RetailerTimeSlotInterface::DAY_OF_WEEK_FIELD);

        $entity->getExtensionAttributes()->setOpeningHours($openingHours);
        $entity->setOpeningHours($openingHours);

        return $entity;
    }
}
