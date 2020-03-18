<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\StoreLocator
 * @author    Romain Ruaud <romain.ruaud@smile.fr>
 * @author    Fanny DECLERCK <fadec@smile.fr>
 * @copyright 2019 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\StoreLocator\Model\Retailer;

use Smile\StoreLocator\Api\Data\RetailerTimeSlotInterfaceFactory;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Smile\Retailer\Api\RetailerRepositoryInterface;
use Smile\StoreLocator\Model\ResourceModel\RetailerTimeSlot;

/**
 * Post Data Handler for Retailer Opening Hours
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 * @author   Fanny DECLERCK <fadec@smile.fr>
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
     * @var RetailerRepositoryInterface
     */
    private $retailerRepository;

    /**
     * @var RetailerTimeSlot
     */
    private $retailerTimeSlot;

    /**
     * OpeningHoursPostDataHandler constructor.
     *
     * @param RetailerTimeSlotInterfaceFactory $timeSlotFactory    Time Slot Factory
     * @param JsonHelper                       $jsonHelper         JSON Helper
     * @param RetailerRepositoryInterface      $retailerRepository Retailer Repository Interface
     * @param RetailerTimeSlot                 $retailerTimeSlot   Retailer Time Slot Resource Model
     */
    public function __construct(
        RetailerTimeSlotInterfaceFactory $timeSlotFactory,
        JsonHelper $jsonHelper,
        RetailerRepositoryInterface $retailerRepository,
        RetailerTimeSlot $retailerTimeSlot
    ) {
        $this->timeSlotFactory    = $timeSlotFactory;
        $this->jsonHelper         = $jsonHelper;
        $this->retailerRepository = $retailerRepository;
        $this->retailerTimeSlot   = $retailerTimeSlot;
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

            // If not a single opening hour is saved, we delete existing entry for current retailer
            if (empty($openingHours) && isset($data['entity_id'])) {
                $this->retailerTimeSlot->deleteByRetailerId($data['entity_id']);
            }

            $data['opening_hours'] = $openingHours;

            $this->updateOpeningHoursBySellerIds($data);
        }

        return $data;
    }

    /**
     * Update opening hours by seller ids.
     *
     * @param array $data Data seller ids / Opening hours by days.
     *
     * @return void
     */
    private function updateOpeningHoursBySellerIds($data)
    {
        if (isset($data['opening_hours_seller_ids'])) {
            foreach ($data['opening_hours_seller_ids'] as $id) {
                $model = $this->retailerRepository->get($id);
                $model->setData('opening_hours', $data['opening_hours']);
                $this->retailerRepository->save($model);
            }
        }
    }
}
