<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Model\Retailer;

use DateInterval;
use DateTime;
use Magento\Framework\Locale\ListsInterface;
use Smile\Retailer\Api\Data\RetailerExtensionInterface;
use Smile\Retailer\Api\Data\RetailerInterface;
use Smile\StoreLocator\Api\Data\RetailerTimeSlotInterface;

/**
 * Schedule Management class for Retailers.
 */
class ScheduleManagement
{
    /**
     * Display calendar up to X days.
     */
    private const CALENDAR_MAX_DATE = 6;

    public function __construct(private ListsInterface $localeList)
    {
    }

    /**
     * Retrieve opening hours for a given date.
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @return RetailerTimeSlotInterface[]
     */
    public function getOpeningHours(RetailerInterface $retailer, ?DateTime $dateTime = null): array
    {
        $dayOpening = [];

        if ($dateTime == null) {
            $dateTime = new DateTime();
        }

        $dayOfWeek = $dateTime->format('w');
        $date = $dateTime->format('Y-m-d');

        /** @var RetailerExtensionInterface $retailerExtensionAttr */
        $retailerExtensionAttr = $retailer->getExtensionAttributes();
        $openingHours = $retailerExtensionAttr->getOpeningHours();
        $specialOpeningHours = $retailerExtensionAttr->getSpecialOpeningHours();

        if (isset($openingHours[$dayOfWeek])) {
            $dayOpening = $openingHours[$dayOfWeek];
        }

        if (isset($specialOpeningHours[$date])) {
            $dayOpening = $specialOpeningHours[$date];
        }

        return $dayOpening;
    }

    /**
     * Get shop calendar: opening hours for the next X days.
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function getCalendar(RetailerInterface $retailer): array
    {
        $calendar = [];
        $date = $this->getMinDate();
        $calendar[$date->format('Y-m-d')] = $this->getOpeningHours($retailer, $date);

        while ($date < $this->getMaxDate()) {
            $date->add(DateInterval::createFromDateString('+1 day'));
            $calendar[$date->format('Y-m-d')] = $this->getOpeningHours($retailer, $date);
        }

        return $calendar;
    }

    /**
     * Retrieve opening hours.
     */
    public function getWeekOpeningHours(RetailerInterface $retailer): array
    {
        $openingHours = [];

        $days = $this->localeList->getOptionWeekdays(true, true);

        foreach (array_keys($days) as $day) {
            $openingHours[$day] = [];
        }

        /** @var RetailerExtensionInterface $retailerExtensionAttr */
        $retailerExtensionAttr = $retailer->getExtensionAttributes();
        foreach ($retailerExtensionAttr->getOpeningHours() as $day => $hours) {
            $openingHours[$day] = $hours;
        }

        return $openingHours;
    }

    /**
     * Get min date to calculate calendar.
     */
    private function getMinDate(): DateTime
    {
        return new DateTime();
    }

    /**
     * Get max date to calculate calendar.
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private function getMaxDate(): DateTime
    {
        $date = $this->getMinDate();
        $date->add(DateInterval::createFromDateString(sprintf('+%s day', self::CALENDAR_MAX_DATE)));

        return $date;
    }
}
