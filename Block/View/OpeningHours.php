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

use Magento\Framework\Locale\Resolver;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Smile\StoreLocator\Helper\Schedule;
use Smile\StoreLocator\Model\Retailer\ScheduleManagement;

/**
 * Opening Hours display block
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
class OpeningHours extends \Smile\StoreLocator\Block\AbstractView
{
    /**
     * @var \Smile\StoreLocator\Helper\Schedule
     */
    private $scheduleHelper;

    /**
     * @var \Smile\StoreLocator\Model\Retailer\ScheduleManagement
     */
    private $scheduleManager;

    /**
     * OpeningHours constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context      $context         Application Context
     * @param \Magento\Framework\Registry                           $coreRegistry    Application Registry
     * @param \Smile\StoreLocator\Model\Retailer\ScheduleManagement $scheduleManager Schedule Manager
     * @param \Smile\StoreLocator\Helper\Schedule                   $scheduleHelper  Schedule Helper
     * @param array                                                 $data            Data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        ScheduleManagement $scheduleManager,
        Schedule $scheduleHelper,
        array $data = []
    ) {
        $this->scheduleManager = $scheduleManager;
        $this->scheduleHelper = $scheduleHelper;

        parent::__construct($context, $coreRegistry, $data);
    }

    /**
     * {@inheritDoc}
     */
    public function getJsLayout()
    {
        $jsLayout = $this->jsLayout;

        $jsLayout['components']['smile-storelocator-store']['retailerId'] = $this->getRetailer()->getId();
        $jsLayout['components']['smile-storelocator-store']['schedule'] = array_merge(
            $jsLayout['components']['smile-storelocator-store']['schedule'],
            $this->scheduleHelper->getConfig(),
            [
                'calendar'               => $this->scheduleManager->getCalendar($this->getRetailer()),
                'openingHours'           => $this->scheduleManager->getWeekOpeningHours($this->getRetailer()),
                'specialOpeningHours'    => $this->getRetailer()->getSpecialOpeningHours(),
            ]
        );

        return json_encode($jsLayout);
    }
}
