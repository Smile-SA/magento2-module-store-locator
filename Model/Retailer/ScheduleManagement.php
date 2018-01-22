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

use Smile\Retailer\Api\Data\RetailerInterface;
use Smile\StoreLocator\Api\Data\RetailerTimeSlotInterface;
use Magento\Framework\Locale\ListsInterface;
use Magento\Framework\Locale\Resolver;

/**
 * Schedule Management class for Retailers
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
class ScheduleManagement
{
    /**
     * Display calendar up to X days.
     */
    const CALENDAR_MAX_DATE = 6;

    /**
     * @var \Magento\Framework\Locale\ListsInterface
     */
    private $localeList;

    /**
     * ScheduleManagement constructor.
     *
     * @param \Magento\Framework\Locale\ListsInterface $localeList Locale Lists
     */
    public function __construct(ListsInterface $localeList)
    {
        $this->localeList = $localeList;
    }

    /**
     * Retrieve opening hours for a given date
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * @param RetailerInterface $retailer The retailer
     * @param null              $dateTime The date to retrieve opening hours for
     *
     * @return RetailerTimeSlotInterface[]
     */
    public function getOpeningHours($retailer, $dateTime = null)
    {
        $dayOpening = [];

        if ($dateTime == null) {
            $dateTime = new \DateTime();
        }
        if (is_string($dateTime)) {
            $dateTime = \DateTime::createFromFormat('Y-m-d', $dateTime);
        }

        $dayOfWeek = $dateTime->format('w');
        $date      = $dateTime->format('Y-m-d');

        $openingHours = $retailer->getExtensionAttributes()->getOpeningHours();
        $specialOpeningHours = $retailer->getExtensionAttributes()->getSpecialOpeningHours();

        if (isset($openingHours[$dayOfWeek])) {
            $dayOpening = $openingHours[$dayOfWeek];
        }

        if (isset($specialOpeningHours[$date])) {
            $dayOpening = $specialOpeningHours[$date];
        }

        return $dayOpening;
    }


    /**
     * Get shop calendar : opening hours for the next X days.
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * @param RetailerInterface $retailer The retailer
     *
     * @return array
     */
    public function getCalendar($retailer)
    {
        $calendar = [];
        $date = $this->getMinDate();
        $calendar[$date->format('Y-m-d')] = $this->getOpeningHours($retailer, $date);

        while ($date < $this->getMaxDate()) {
            $date->add(\DateInterval::createFromDateString('+1 day'));
            $calendar[$date->format('Y-m-d')] = $this->getOpeningHours($retailer, $date);
        }

        return $calendar;
    }

    /**
     * Retrieve opening hours
     *
     * @param RetailerInterface $retailer The retailer
     *
     * @return array
     */
    public function getWeekOpeningHours($retailer)
    {
        $openingHours = [];

        $days = $this->localeList->getOptionWeekdays(true, true);

        foreach (array_keys($days) as $day) {
            $openingHours[$day] = [];
        }

        foreach ($retailer->getExtensionAttributes()->getOpeningHours() as $day => $hours) {
            $openingHours[$day] = $hours;
        }

        return $openingHours;
    }

    /**
     * Get min date to calculate calendar
     *
     * @return \DateTime
     */
    private function getMinDate()
    {
        $date = new \DateTime();

        return $date;
    }

    /**
     * Get max date to calculate calendar
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * @return \DateTime
     */
    private function getMaxDate()
    {
        $date = $this->getMinDate();
        $date->add(\DateInterval::createFromDateString(sprintf('+%s day', self::CALENDAR_MAX_DATE)));

        return $date;
    }
}
