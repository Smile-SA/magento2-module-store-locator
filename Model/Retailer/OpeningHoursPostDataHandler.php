<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Model\Retailer;

use Exception;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Smile\Retailer\Api\Data\RetailerInterface;
use Smile\Retailer\Api\RetailerRepositoryInterface;
use Smile\Retailer\Model\Retailer\PostDataHandlerInterface;
use Smile\StoreLocator\Api\Data\RetailerTimeSlotInterfaceFactory;
use Smile\StoreLocator\Model\ResourceModel\RetailerTimeSlot;

/**
 * Post Data Handler for Retailer Opening Hours.
 */
class OpeningHoursPostDataHandler implements PostDataHandlerInterface
{
    public function __construct(
        private RetailerTimeSlotInterfaceFactory $timeSlotFactory,
        private JsonSerializer $jsonSerializer,
        private RetailerRepositoryInterface $retailerRepository,
        private RetailerTimeSlot $retailerTimeSlot
    ) {
    }

    /**
     * @inheritdoc
     */
    public function getData(RetailerInterface $retailer, mixed $data): mixed
    {
        if (isset($data['opening_hours'])) {
            $openingHours = [];

            foreach ($data['opening_hours'] as $date => &$timeSlotList) {
                if (is_string($timeSlotList)) {
                    try {
                        $timeSlotList = $this->jsonSerializer->unserialize($timeSlotList);
                    } catch (Exception $exception) {
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
                $this->retailerTimeSlot->deleteByRetailerId((int) $data['entity_id']);
            }

            $data['opening_hours'] = $openingHours;

            $this->updateOpeningHoursBySellerIds($data);
        }

        return $data;
    }

    /**
     * Update opening hours by seller ids.
     *
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    private function updateOpeningHoursBySellerIds(array $data): void
    {
        if (isset($data['opening_hours_seller_ids'])) {
            foreach ($data['opening_hours_seller_ids'] as $id) {
                $model = $this->retailerRepository->get((int) $id);
                $model->setData('opening_hours', $data['opening_hours']);
                $this->retailerRepository->save($model);
            }
        }
    }
}
