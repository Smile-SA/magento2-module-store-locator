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
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Smile\Retailer\Api\Data\RetailerInterface;
use Smile\Retailer\Api\RetailerRepositoryInterface;
use Smile\Retailer\Model\Retailer\PostDataHandlerInterface;
use Smile\StoreLocator\Model\ResourceModel\RetailerTimeSlot;

/**
 * Post Data Handler for Retailer Opening Hours
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 * @author   Fanny DECLERCK <fadec@smile.fr>
 */
class OpeningHoursPostDataHandler implements PostDataHandlerInterface
{
    /**
     * @var RetailerTimeSlotInterfaceFactory
     */
    private RetailerTimeSlotInterfaceFactory $timeSlotFactory;

    /**
     * @var JsonSerializer
     */
    private JsonSerializer $jsonSerializer;

    /**
     * @var RetailerRepositoryInterface
     */
    private RetailerRepositoryInterface $retailerRepository;

    /**
     * @var RetailerTimeSlot
     */
    private RetailerTimeSlot $retailerTimeSlot;

    /**
     * OpeningHoursPostDataHandler constructor.
     *
     * @param RetailerTimeSlotInterfaceFactory $timeSlotFactory    Time Slot Factory
     * @param JsonSerializer                   $jsonSerializer     JSON Serializer
     * @param RetailerRepositoryInterface      $retailerRepository Retailer Repository Interface
     * @param RetailerTimeSlot                 $retailerTimeSlot   Retailer Time Slot Resource Model
     */
    public function __construct(
        RetailerTimeSlotInterfaceFactory $timeSlotFactory,
        JsonSerializer $jsonSerializer,
        RetailerRepositoryInterface $retailerRepository,
        RetailerTimeSlot $retailerTimeSlot
    ) {
        $this->timeSlotFactory    = $timeSlotFactory;
        $this->jsonSerializer     = $jsonSerializer;
        $this->retailerRepository = $retailerRepository;
        $this->retailerTimeSlot   = $retailerTimeSlot;
    }

    /**
     * {@inheritDoc}
     */
    public function getData(RetailerInterface $retailer, mixed $data): mixed
    {
        if (isset($data['opening_hours'])) {
            $openingHours = [];

            foreach ($data['opening_hours'] as $date => &$timeSlotList) {
                if (is_string($timeSlotList)) {
                    try {
                        $timeSlotList = $this->jsonSerializer->unserialize($timeSlotList);
                    } catch (\Exception $exception) {
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
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return void
     */
    private function updateOpeningHoursBySellerIds(array $data): void
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
