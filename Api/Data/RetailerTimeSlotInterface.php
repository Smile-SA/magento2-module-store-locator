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
namespace Smile\StoreLocator\Api\Data;

/**
 * Generic Interface for retailer time slots items
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
interface RetailerTimeSlotInterface
{
    /**
     * The date field
     */
    const DATE_FIELD = 'date';

    /**
     * The day of week field
     */
    const DAY_OF_WEEK_FIELD = 'day_of_week';

    /**
     * @return string
     */
    public function getStartTime(): string;

    /**
     * @return string
     */
    public function getEndTime(): string;

    /**
     * Set the start time
     *
     * @param string $time The time
     *
     * @return mixed
     */
    public function setStartTime(string $time): mixed;

    /**
     * Set the end time
     *
     * @param string $time The time
     *
     * @return mixed
     */
    public function setEndTime(string $time): mixed;
}
