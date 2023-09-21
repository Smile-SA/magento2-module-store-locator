<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Api\Data;

/**
 * Generic Interface for retailer time slots items.
 */
interface RetailerTimeSlotInterface
{
    public const DATE_FIELD = 'date';
    public const DAY_OF_WEEK_FIELD = 'day_of_week';

    /**
     * Get start time.
     *
     * @return string
     */
    public function getStartTime(): string;

    /**
     * Get end time.
     *
     * @return string
     */
    public function getEndTime(): string;

    /**
     * Set the start time
     *
     * @param string $time The time
     * @return $this
     */
    public function setStartTime(string $time): self;

    /**
     * Set the end time
     *
     * @param string $time The time
     * @return $this
     */
    public function setEndTime(string $time): self;
}
