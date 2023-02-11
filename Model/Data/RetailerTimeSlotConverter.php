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

use Magento\Framework\Locale\ListsInterface;
use Smile\StoreLocator\Api\Data\RetailerTimeSlotDaysInterface;
use Smile\StoreLocator\Api\Data\RetailerTimeSlotDaysInterfaceFactory;
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
     * @var RetailerTimeSlotInterfaceFactory $timeSlotFactory
     */
    private $timeSlotFactory;
    /**
     * @var ListsInterface $localeLists
     */
    private $localeLists;
    /**
     * @var RetailerTimeSlotDaysInterfaceFactory $timeSlotDaysFactory
     */
    private $timeSlotDaysFactory;
    /**
     * RetailerTimeSlotConverter constructor.
     *
     * @param RetailerTimeSlotInterfaceFactory $timeSlotFactory Time Slot Factory
     * @param ListsInterface $localeLists Locale Lists
     * @param RetailerTimeSlotDaysInterfaceFactory $timeSlotDaysFactory Time Slot Days Factory
     */
    public function __construct(
        RetailerTimeSlotInterfaceFactory $timeSlotFactory,
        ListsInterface $localeLists,
        RetailerTimeSlotDaysInterfaceFactory $timeSlotDaysFactory
    ) {
        $this->timeSlotFactory = $timeSlotFactory;
        $this->localeLists = $localeLists;
        $this->timeSlotDaysFactory = $timeSlotDaysFactory;
    }

    /**
     * Convert a set of timeslot entries to a multidimensional array of RetailerTimeSlotInterface
     *
     * @param array  $timeSlots The time slot data
     * @param string $dateField The date field to use
     *
     * @return RetailerTimeSlotDaysInterface
     */
    public function toEntity($timeSlots, $dateField = RetailerTimeSlotInterface::DAY_OF_WEEK_FIELD)
    {
        /** @var RetailerTimeSlotDaysInterface $openingHours */
        $openingHours = $this->timeSlotDaysFactory->create();

        if (!empty($timeSlots)) {
            $days = $this->localeLists->getOptionWeekdays(true, true);
            foreach ($timeSlots as $row) {
                $day = 'Date';

                if ($dateField === RetailerTimeSlotInterface::DAY_OF_WEEK_FIELD) {
                    $day = $row[$dateField];
                    $day = ucfirst($days[$day]['label']);
                }

                if (null !== $row['start_time'] && null !== $row['end_time']) {
                    $timeSlotModels = [$this->timeSlotFactory->create(
                        ['data' => ['start_time' => $row['start_time'], 'end_time' => $row['end_time']]]
                    )];
                    if ($dateField === RetailerTimeSlotInterface::DAY_OF_WEEK_FIELD) {
                        if ($data = $openingHours->{'get'.$day}()) {
                            $timeSlotModels = array_merge(... [$data, $timeSlotModels]);
                        }
                        $openingHours->{'set' . $day}($timeSlotModels);
                    }
                }
            }
        }

        return $openingHours;
    }
}
