<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Model\Retailer;

use DateTime;
use DateTimeZone;
use Exception;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Framework\Stdlib\DateTime as MagentoDateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Smile\Retailer\Api\Data\RetailerInterface;
use Smile\Retailer\Api\RetailerRepositoryInterface;
use Smile\Retailer\Model\Retailer\PostDataHandlerInterface;
use Smile\StoreLocator\Api\Data\RetailerTimeSlotInterface;
use Smile\StoreLocator\Api\Data\RetailerTimeSlotInterfaceFactory;

/**
 * Post Data Handler for Retailer Opening Hours.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SpecialOpeningHoursPostDataHandler implements PostDataHandlerInterface
{
    public function __construct(
        private RetailerTimeSlotInterfaceFactory $timeSlotFactory,
        private JsonSerializer $jsonSerializer,
        private TimezoneInterface $localeDate,
        private RetailerRepositoryInterface $retailerRepository
    ) {
    }

    /**
     * @inheritdoc
     */
    public function getData(RetailerInterface $retailer, mixed $data): mixed
    {
        if (isset($data['special_opening_hours'])) {
            $specialOpeningHours = [];

            foreach ($data['special_opening_hours'] as $item) {
                if (
                    !isset($item[RetailerTimeSlotInterface::DATE_FIELD])
                    || '' === $item[RetailerTimeSlotInterface::DATE_FIELD]
                ) {
                    continue;
                }

                $date = $this->formatDate($item[RetailerTimeSlotInterface::DATE_FIELD]);
                $specialOpeningHours[$date] = [];

                if (is_string($item['opening_hours'])) {
                    try {
                        $item['opening_hours'] = $this->jsonSerializer->unserialize($item['opening_hours']);
                    } catch (Exception) {
                        $item['opening_hours'] = [];
                    }
                }

                if (!count($item['opening_hours'])) {
                    continue;
                }

                foreach ($item['opening_hours'] as $timeSlot) {
                    $timeSlotModel = $this->timeSlotFactory->create(
                        ['data' => ['start_time' => $timeSlot[0], 'end_time' => $timeSlot[1]]]
                    );
                    $specialOpeningHours[$date][] = $timeSlotModel;
                }
            }

            $data['special_opening_hours'] = $specialOpeningHours;

            $this->updateSpecialOpeningHoursBySellerIds($data);
        }

        return $data;
    }

    /**
     * Prepare date for save in DB.
     *
     * @throws Exception
     */
    private function formatDate(string $date): string
    {
        $date = new DateTime($date, new DateTimeZone($this->localeDate->getConfigTimezone()));

        return $date->format(MagentoDateTime::DATE_PHP_FORMAT);
    }

    /**
     * Update special opening hours by seller ids.
     *
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    private function updateSpecialOpeningHoursBySellerIds(array $data): void
    {
        if (isset($data['special_opening_hours_seller_ids'])) {
            foreach ($data['special_opening_hours_seller_ids'] as $id) {
                $model = $this->retailerRepository->get((int) $id);
                $model->setData('special_opening_hours', $data['special_opening_hours']);
                $this->retailerRepository->save($model);
            }
        }
    }
}
