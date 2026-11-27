<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Plugin\Rest;

use Smile\Retailer\Api\Data\RetailerInterface;
use Smile\Retailer\Model\RetailerRepository;
use Smile\StoreLocator\Api\Data\RetailerTimeSlotInterface;
use Smile\StoreLocator\Api\Data\RetailerTimeSlotInterfaceFactory;

/**
 * Plugin to save retailer extension attribut from rest
 */
class PrepareOffload
{
    public function __construct(
        private RetailerTimeSlotInterfaceFactory $timeSlotFactory
    ) {
    }

    /**
     * The save handler offload attribute.
     *
     * It expects to have them loaded properly.
     */
    public function beforeSave(RetailerRepository $subject, RetailerInterface $retailer): void
    {
        $retailer->setAddress($retailer->getExtensionAttributes()->getAddress());

        if (is_array($retailer->getExtensionAttributes()->getOpeningHours())) {
            $retailer->setOpeningHours($this->convertSchedule($retailer->getExtensionAttributes()->getOpeningHours()));
        }

        if (is_array($retailer->getExtensionAttributes()->getSpecialOpeningHours())) {
            $retailer->setSpecialOpeningHours(
                $this->convertSchedule(
                    $retailer->getExtensionAttributes()->getSpecialOpeningHours()
                )
            );
        }
    }

    /**
     * Convert JSON AM/PM hours to schedule structure
     */
    public function convertSchedule(array $mixedValue): array
    {
        $openingHours = json_decode($mixedValue[0], true);
        foreach ($openingHours as $workingDay => $schedule) {
            $openingHours[$workingDay] = array_map([$this, "createTimeSlotObject"], $schedule);
        }
        return $openingHours;
    }

    /**
     * Convert AM/PM hours to date
     */
    public function createTimeSlotObject(array $slot): RetailerTimeSlotInterface
    {
        return $this->timeSlotFactory->create(
            [
                'data' => [
                    'start_time' => date("G:i", strtotime($slot['start_time'])),
                    'end_time' => date("G:i", strtotime($slot['end_time'])),
                ],
            ]
        );
    }
}
