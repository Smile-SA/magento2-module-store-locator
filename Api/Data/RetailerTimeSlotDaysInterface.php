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
     * @return \Smile\StoreLocator\Api\Data\RetailerTimeSlotInterface[]
     */
    public function getMonday();

    /**
     * @return \Smile\StoreLocator\Api\Data\RetailerTimeSlotInterface[]
     */
    public function getTuesday();

    /**
     * @return \Smile\StoreLocator\Api\Data\RetailerTimeSlotInterface[]
     */
    public function getWednesday();

    /**
     * @return \Smile\StoreLocator\Api\Data\RetailerTimeSlotInterface[]
     */
    public function getThursday();

    /**
     * @return \Smile\StoreLocator\Api\Data\RetailerTimeSlotInterface[]
     */
    public function getFriday();

    /**
     * @return \Smile\StoreLocator\Api\Data\RetailerTimeSlotInterface[]
     */
    public function getSaturday();

    /**
     * @return \Smile\StoreLocator\Api\Data\RetailerTimeSlotInterface[]
     */
    public function getSunday();

    /**
     * @return \Smile\StoreLocator\Api\Data\RetailerTimeSlotInterface[]
     */
    public function getDate();

    /**
     * Set the start time
     *
     * @param \Smile\StoreLocator\Api\Data\RetailerTimeSlotInterface[] $days The time
     *
     * @return mixed
     */
    public function setMonday($days);

    /**
     * Set the start time
     *
     * @param \Smile\StoreLocator\Api\Data\RetailerTimeSlotInterface[] $days The time
     *
     * @return mixed
     */
    public function setTuesday($days);

    /**
     * Set the start time
     *
     * @param \Smile\StoreLocator\Api\Data\RetailerTimeSlotInterface[] $days The time
     *
     * @return mixed
     */
    public function setWednesday($days);

    /**
     * Set the start time
     *
     * @param \Smile\StoreLocator\Api\Data\RetailerTimeSlotInterface[] $days The time
     *
     * @return mixed
     */
    public function setThursday($days);

    /**
     * Set the start time
     *
     * @param \Smile\StoreLocator\Api\Data\RetailerTimeSlotInterface[] $days The time
     *
     * @return mixed
     */
    public function setFriday($days);

    /**
     * Set the start time
     *
     * @param \Smile\StoreLocator\Api\Data\RetailerTimeSlotInterface[] $days The time
     *
     * @return mixed
     */
    public function setSaturday($days);

    /**
     * Set the start time
     *
     * @param \Smile\StoreLocator\Api\Data\RetailerTimeSlotInterface[] $days The time
     *
     * @return mixed
     */
    public function setSunday($days);

    /**
     * Set the start time
     *
     * @param \Smile\StoreLocator\Api\Data\RetailerTimeSlotInterface[] $days The time
     *
     * @return mixed
     */
    public function setDate($days);
}
