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
namespace Smile\StoreLocator\Block\View;

use Magento\Framework\Locale\ListsInterface;
use Magento\Framework\Locale\Resolver;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Smile\StoreLocator\Api\Data\RetailerTimeSlotInterface;
use Smile\StoreLocator\Block\AbstractView;
use Smile\StoreLocator\Helper\Schedule;
use Smile\StoreLocator\Model\Retailer\ScheduleManagement;

/**
 * Opening Hours display block
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
class OpeningHours extends AbstractView
{
    /**
     * @var Schedule
     */
    private Schedule $scheduleHelper;

    /**
     * @var ScheduleManagement
     */
    private ScheduleManagement $scheduleManager;

    /**
     * @var ListsInterface
     */
    private ListsInterface $localeList;

    /**
     * OpeningHours constructor.
     *
     * @param Context            $context         Application Context
     * @param Registry           $coreRegistry    Application Registry
     * @param ScheduleManagement $scheduleManager Schedule Manager
     * @param Schedule           $scheduleHelper  Schedule Helper
     * @param ListsInterface     $localeList      Locale List
     * @param array              $data            Data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        ScheduleManagement $scheduleManager,
        Schedule $scheduleHelper,
        ListsInterface $localeList,
        array $data = []
    ) {
        $this->scheduleManager = $scheduleManager;
        $this->scheduleHelper  = $scheduleHelper;
        $this->localeList      = $localeList;

        parent::__construct($context, $coreRegistry, $data);
    }

    /**
     * {@inheritDoc}
     */
    public function getJsLayout(): string
    {
        $jsLayout = $this->jsLayout;

        if (!isset($jsLayout['components']['smile-storelocator-store']['schedule'])) {
            $jsLayout['components']['smile-storelocator-store']['schedule'] = [];
        }
        $jsLayout['components']['smile-storelocator-store']['retailerId'] = $this->getRetailer()->getId();
        $jsLayout['components']['smile-storelocator-store']['schedule']   = array_merge(
            $jsLayout['components']['smile-storelocator-store']['schedule'],
            $this->scheduleHelper->getConfig(),
            [
                'calendar'            => $this->scheduleManager->getCalendar($this->getRetailer()),
                'openingHours'        => $this->getWeekOpeningHours(),
                'specialOpeningHours' => $this->getRetailer()->getSpecialOpeningHours(),
            ]
        );

        return json_encode($jsLayout);
    }

    /**
     * Retrieve Week Opening Hours
     *
     * @return array
     */
    public function getWeekOpeningHours(): array
    {
        return $this->scheduleManager->getWeekOpeningHours($this->getRetailer());
    }


    /**
     * Retrieve Opening Hours with schema.org compliant format.
     *
     * @see http://schema.org/openingHours
     *
     * @return string
     */
    public function getOpeningHoursMicroFormat(): string
    {
        $days   = $this->localeList->getOptionWeekdays(true, true);

        $microData = [];
        $microDataDays = [];

        foreach ($this->getWeekOpeningHours() as $day => $openingHours) {
            if (!empty($openingHours) && isset($days[$day]) && isset($days[$day]['value'])) {
                $dayCode = ucfirst(substr($days[$day]['value'], 0, 2));
                $microData[$dayCode] = [];
                /** @var RetailerTimeSlotInterface $openingHour */
                foreach ($openingHours as $openingHour) {
                    $microData[$dayCode][] = $openingHour->getStartTime() . '-' . $openingHour->getEndTime();
                }
                $microDataDays[] = $dayCode . ' ' . implode(' ', $microData[$dayCode]);
            }
        }

        $string = implode(',', $microDataDays);

        return $string;
    }
}
