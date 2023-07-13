<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Model\Data;

use JsonSerializable;
use Magento\Framework\DataObject;
use Smile\StoreLocator\Api\Data\RetailerTimeSlotInterface;

/**
 * Data Object for Time Slot entries.
 */
class RetailerTimeSlot extends DataObject implements RetailerTimeSlotInterface, JsonSerializable
{
    /**
     * @inheritdoc
     */
    public function getStartTime(): string
    {
        return $this->getData('start_time');
    }

    /**
     * @inheritdoc
     */
    public function getEndTime(): string
    {
        return $this->getData('end_time');
    }

    /**
     * @inheritdoc
     */
    public function setStartTime(string $time): self
    {
        $this->setData('start_time', $time);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setEndTime(string $time): self
    {
        $this->setData('end_time', $time);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): mixed
    {
        return [
            'start_time' => $this->getStartTime(),
            'end_time'   => $this->getEndTime(),
        ];
    }
}
