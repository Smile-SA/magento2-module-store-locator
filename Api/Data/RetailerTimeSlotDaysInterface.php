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
interface RetailerTimeSlotDaysInterface
{
    /**
     * @return RetailerTimeSlotInterface[]
     */
    public function getMonday();

    /**
     * @return RetailerTimeSlotInterface[]
     */
    public function getTuesday();

    /**
     * @return RetailerTimeSlotInterface[]
     */
    public function getWednesday();

    /**
     * @return RetailerTimeSlotInterface[]
     */
    public function getThursday();

    /**
     * @return RetailerTimeSlotInterface[]
     */
    public function getFriday();

    /**
     * @return RetailerTimeSlotInterface[]
     */
    public function getSaturday();

    /**
     * @return RetailerTimeSlotInterface[]
     */
    public function getSunday();

    /**
     * @return RetailerTimeSlotInterface[]
     */
    public function getDate();

    /**
     * Set the start time
     *
     * @param RetailerTimeSlotInterface[] $days The time
     *
     * @return mixed
     */
    public function setMonday($days);

    /**
     * Set the start time
     *
     * @param RetailerTimeSlotInterface[] $days The time
     *
     * @return mixed
     */
    public function setTuesday($days);

    /**
     * Set the start time
     *
     * @param RetailerTimeSlotInterface[] $days The time
     *
     * @return mixed
     */
    public function setWednesday($days);

    /**
     * Set the start time
     *
     * @param RetailerTimeSlotInterface[] $days The time
     *
     * @return mixed
     */
    public function setThursday($days);

    /**
     * Set the start time
     *
     * @param RetailerTimeSlotInterface[] $days The time
     *
     * @return mixed
     */
    public function setFriday($days);

    /**
     * Set the start time
     *
     * @param RetailerTimeSlotInterface[] $days The time
     *
     * @return mixed
     */
    public function setSaturday($days);

    /**
     * Set the start time
     *
     * @param RetailerTimeSlotInterface[] $days The time
     *
     * @return mixed
     */
    public function setSunday($days);

    /**
     * Set the start time
     *
     * @param RetailerTimeSlotInterface[] $days The time
     *
     * @return mixed
     */
    public function setDate($days);
}
