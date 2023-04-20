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
 * Save Handler for Retailer Opening Hours
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
class OpeningHoursSaveHandler implements ExtensionInterface
{
    /**
     * @var TimeSlotResource
     */
    private TimeSlotResource $resource;

    /**
     * OpeningHoursSaveHandler constructor.
     *
     * @param TimeSlotResource $resource Resource Model
     */
    public function __construct(
        TimeSlotResource $resource
    ) {
        $this->resource = $resource;
    }

    /**
     * @param object $entity
     * @param array $arguments
     * @return object|bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute($entity, $arguments = []): bool|object
    {
        if ($entity->getOpeningHours()) {
            $this->resource->saveTimeSlots($entity->getId(), 'opening_hours', $entity->getOpeningHours());
        }

        return $entity;
    }
}
