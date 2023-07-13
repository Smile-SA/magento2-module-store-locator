<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Model\Data;

use Smile\StoreLocator\Api\Data\RetailerTimeSlotInterface;
use Smile\StoreLocator\Api\Data\RetailerTimeSlotInterfaceFactory;

/**
 * Converter for Time Slot operations.
 */
class RetailerTimeSlotConverter
{
    public function __construct(private RetailerTimeSlotInterfaceFactory $timeSlotFactory)
    {
    }

    /**
     * Convert a set of timeslot entries to a multidimensional array of RetailerTimeSlotInterface.
     */
    public function toEntity(array $timeSlots, string $dateField = RetailerTimeSlotInterface::DAY_OF_WEEK_FIELD): array
    {
        $openingHours = [];

        if (!empty($timeSlots)) {
            foreach ($timeSlots as $row) {
                $day = $row[$dateField];
                if (!isset($openingHours[$day])) {
                    $openingHours[$day] = [];
                }

                if (null !== $row['start_time'] && null !== $row['end_time']) {
                    $timeSlotModel = $this->timeSlotFactory->create(
                        ['data' => ['start_time' => $row['start_time'], 'end_time' => $row['end_time']]]
                    );
                    $openingHours[$day][] = $timeSlotModel;
                }
            }
        }

        return $openingHours;
    }
}
