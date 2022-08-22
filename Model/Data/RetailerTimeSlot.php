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
use Smile\StoreLocator\Api\Data\RetailerTimeSlotInterface;
use Zend\Stdlib\JsonSerializable;

/**
 * Data Object for Time Slot entries.
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
class RetailerTimeSlot extends DataObject implements RetailerTimeSlotInterface, JsonSerializable
{
    /**
     * {@inheritDoc}
     */
    public function getStartTime()
    {
        return $this->getData('start_time');
    }

    /**
     * {@inheritDoc}
     */
    public function getEndTime()
    {
        return $this->getData('end_time');
    }

    /**
     * {@inheritDoc}
     */
    public function setStartTime($time)
    {
        $this->setData('start_time', $time);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setEndTime($time)
    {
        $this->setData('end_time', $time);

        return $this;
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *        which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize(): mixed
    {
        return [
            'start_time' => $this->getStartTime(),
            'end_time'   => $this->getEndTime(),
        ];
    }
}
