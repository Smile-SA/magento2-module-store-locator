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

use Smile\StoreLocator\Api\Data\RetailerTimeSlotInterfaceFactory;
use Magento\Framework\Json\Helper\Data as JsonHelper;

/**
 * Post Data Handler for Retailer Opening Hours
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
class OpeningHoursPostDataHandler implements \Smile\Retailer\Model\Retailer\PostDataHandlerInterface
{
    /**
     * @var RetailerTimeSlotInterfaceFactory
     */
    private $timeSlotFactory;

    /**
     * @var JsonHelper
     */
    private $jsonHelper;

    /**
     * OpeningHoursPostDataHandler constructor.
     *
     * @param RetailerTimeSlotInterfaceFactory $timeSlotFactory Time Slot Factory
     * @param JsonHelper                       $jsonHelper      JSON Helper
     */
    public function __construct(RetailerTimeSlotInterfaceFactory $timeSlotFactory, JsonHelper $jsonHelper)
    {
        $this->timeSlotFactory = $timeSlotFactory;
        $this->jsonHelper      = $jsonHelper;
    }

    /**
     * {@inheritDoc}
     */
    public function getData(\Smile\Retailer\Api\Data\RetailerInterface $retailer, $data)
    {
        if (isset($data['opening_hours'])) {
            $openingHours = [];
            foreach ($data['opening_hours'] as $date => &$timeSlotList) {
                if (is_string($timeSlotList)) {
                    try {
                        $timeSlotList = $this->jsonHelper->jsonDecode($timeSlotList);
                    } catch (\Zend_Json_Exception $exception) {
                        $timeSlotList = [];
                    }
                }

                if (!count($timeSlotList)) {
                    continue;
                }

                foreach ($timeSlotList as $timeSlot) {
                    $timeSlotModel = $this->timeSlotFactory->create(
                        ['data' => ['start_time' => $timeSlot[0], 'end_time' => $timeSlot[1]]]
                    );
                    $openingHours[$date][] = $timeSlotModel;
                }
            }

            $data['opening_hours'] = $openingHours;
        }

        return $data;
    }
}
