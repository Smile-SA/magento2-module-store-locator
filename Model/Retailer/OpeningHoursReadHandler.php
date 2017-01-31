<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade Smile Elastic Suite to newer
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
use Smile\StoreLocator\Api\Data\RetailerTimeSlotInterface;
use Smile\StoreLocator\Model\ResourceModel\RetailerTimeSlot as TimeSlotResource;
use Smile\StoreLocator\Api\Data\RetailerTimeSlotInterfaceFactory;

/**
 * Read Handler for Retailer Opening Hours
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
class OpeningHoursReadHandler implements ExtensionInterface
{
    /**
     * @var \Smile\StoreLocator\Model\ResourceModel\RetailerTimeSlot
     */
    private $resource;

    /**
     * @var \Smile\StoreLocator\Api\Data\RetailerTimeSlotInterfaceFactory
     */
    private $timeSlotFactory;

    /**
     * OpeningHoursSaveHandler constructor.
     *
     * @param RetailerTimeSlotInterfaceFactory $timeSlotFactory Time Slot Factory
     * @param TimeSlotResource                 $resource        Resource Model
     */
    public function __construct(RetailerTimeSlotInterfaceFactory $timeSlotFactory, TimeSlotResource $resource)
    {
        $this->timeSlotFactory = $timeSlotFactory;
        $this->resource = $resource;
    }

    /**
     * {@inheritDoc}
     */
    public function execute($entity, $arguments = [])
    {
        $timeSlots = $this->resource->getTimeSlots($entity->getId(), 'opening_hours');
        $openingHours = [];

        if (!empty($timeSlots)) {
            foreach ($timeSlots as $row) {
                $day = $row[RetailerTimeSlotInterface::DAY_OF_WEEK_FIELD];
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

        $entity->getExtensionAttributes()->setOpeningHours($openingHours);
        $entity->setOpeningHours($openingHours);

        return $entity;
    }
}
