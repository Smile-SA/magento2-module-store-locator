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

use Magento\Framework\DataObject;
use Smile\StoreLocator\Api\Data\RetailerTimeSlotDaysInterface;

/**
 * Data Object for Time Slot entries.
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
class RetailerTimeSlotDays extends DataObject implements RetailerTimeSlotDaysInterface
{
    /**
     * {@inheritDoc}
     */
    public function getMonday()
    {
        return $this->getData('monday');
    }

    /**
     * {@inheritDoc}
     */
    public function getTuesday()
    {
        return $this->getData('tuesday');
    }

    /**
     * {@inheritDoc}
     */
    public function getWednesday()
    {
        return $this->getData('wednesday');
    }

    /**
     * {@inheritDoc}
     */
    public function getThursday()
    {
        return $this->getData('thursday');
    }

    /**
     * {@inheritDoc}
     */
    public function getFriday()
    {
        return $this->getData('friday');
    }

    /**
     * {@inheritDoc}
     */
    public function getSaturday()
    {
        return $this->getData('saturday');
    }

    /**
     * {@inheritDoc}
     */
    public function getSunday()
    {
        return $this->getData('sunday');
    }

    /**
     * {@inheritDoc}
     */
    public function getDate()
    {
        return $this->getData('date');
    }

    /**
     * {@inheritDoc}
     */
    public function setMonday($days)
    {
        $this->setData('monday', $days);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setTuesday($days)
    {
        $this->setData('tuesday', $days);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setWednesday($days)
    {
        $this->setData('wednesday', $days);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setThursday($days)
    {
        $this->setData('thursday', $days);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setFriday($days)
    {
        $this->setData('friday', $days);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setSaturday($days)
    {
        $this->setData('saturday', $days);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setSunday($days)
    {
        $this->setData('sunday', $days);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setDate($days)
    {
        $this->setData('date', $days);

        return $this;
    }
}
