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
namespace Smile\StoreLocator\Model\Data;

use Smile\StoreLocator\Api\Data\RetailerTimeSlotInterface;
use Smile\StoreLocator\Api\Data\RetailerTimeSlotInterfaceFactory;

/**
 * Converter for Time Slot operations
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
class RetailerTimeSlotConverter
{
    /**
     * RetailerTimeSlotConverter constructor.
     *
     * @param RetailerTimeSlotInterfaceFactory $timeSlotFactory Time Slot Factory
     */
    public function __construct(RetailerTimeSlotInterfaceFactory $timeSlotFactory)
    {
        $this->timeSlotFactory = $timeSlotFactory;
    }

    /**
     * Convert a set of timeslot entries to a multidimensional array of RetailerTimeSlotInterface
     *
     * @param array  $timeSlots The time slot data
     * @param string $dateField The date field to use
     *
     * @return array
     */
    public function toEntity($timeSlots, $dateField = RetailerTimeSlotInterface::DAY_OF_WEEK_FIELD)
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
