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
use Smile\StoreLocator\Api\Data\RetailerTimeSlotInterface;
use Smile\StoreLocator\Model\Data\RetailerTimeSlotConverter;
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
     * @var \Smile\StoreLocator\Model\Data\RetailerTimeSlotConverter
     */
    private $converter;

    /**
     * OpeningHoursSaveHandler constructor.
     *
     * @param RetailerTimeSlotConverter $converter Time Slot Factory
     * @param TimeSlotResource          $resource  Resource Model
     */
    public function __construct(RetailerTimeSlotConverter $converter, TimeSlotResource $resource)
    {
        $this->converter = $converter;
        $this->resource  = $resource;
    }

    /**
     * {@inheritDoc}
     */
    public function execute($entity, $arguments = [])
    {
        $timeSlots = $this->resource->getTimeSlots($entity->getId(), 'opening_hours');

        $openingHours = $this->converter->toEntity($timeSlots, RetailerTimeSlotInterface::DAY_OF_WEEK_FIELD);

        $entity->getExtensionAttributes()->setOpeningHours($openingHours);

        return $entity;
    }
}
