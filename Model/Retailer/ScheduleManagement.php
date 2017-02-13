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
     * Retrieve opening hours for a given date
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

        $openingHours = $retailer->getOpeningHours();
        $specialOpeningHours = $retailer->getSpecialOpeningHours();

        if (isset($openingHours[$dayOfWeek])) {
            $dayOpening = $openingHours[$dayOfWeek];
        }

        if (isset($specialOpeningHours[$date])) {
            $dayOpening = $specialOpeningHours[$date];
        }

        return $dayOpening;
    }
}
