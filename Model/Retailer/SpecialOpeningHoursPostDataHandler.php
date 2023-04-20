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

use DateTime;
use DateTimeZone;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Framework\Stdlib\DateTime as MagentoDateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Smile\Retailer\Api\Data\RetailerInterface;
use Smile\Retailer\Api\RetailerRepositoryInterface;
use Smile\StoreLocator\Api\Data\RetailerTimeSlotInterface;
use Smile\StoreLocator\Api\Data\RetailerTimeSlotInterfaceFactory;
use Smile\Retailer\Model\Retailer\PostDataHandlerInterface;

/**
 * Post Data Handler for Retailer Opening Hours
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 * @author   Fanny DECLERCK <fadec@smile.fr>
 */
class SpecialOpeningHoursPostDataHandler implements PostDataHandlerInterface
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
     * @var TimezoneInterface
     */
    private TimezoneInterface $localeDate;

    /**
     * @var RetailerRepositoryInterface
     */
    private RetailerRepositoryInterface $retailerRepository;

    /**
     * OpeningHoursPostDataHandler constructor.
     *
     * @param RetailerTimeSlotInterfaceFactory $timeSlotFactory    Time Slot Factory
     * @param JsonSerializer                   $jsonSerializer     JSON Serializer
     * @param TimezoneInterface                $localeDate         The Locale Date Interface
     * @param RetailerRepositoryInterface      $retailerRepository Retailer Repository Interface
     */
    public function __construct(
        RetailerTimeSlotInterfaceFactory $timeSlotFactory,
        JsonSerializer $jsonSerializer,
        TimezoneInterface $localeDate,
        RetailerRepositoryInterface $retailerRepository
    ) {
        $this->timeSlotFactory    = $timeSlotFactory;
        $this->jsonSerializer     = $jsonSerializer;
        $this->localeDate         = $localeDate;
        $this->retailerRepository = $retailerRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function getData(RetailerInterface $retailer, mixed $data): mixed
    {
        if (isset($data['special_opening_hours'])) {
            $specialOpeningHours = [];

            foreach ($data['special_opening_hours'] as $item) {
                if (!isset($item[RetailerTimeSlotInterface::DATE_FIELD]) || '' === $item[RetailerTimeSlotInterface::DATE_FIELD]) {
                    continue;
                }

                $date = $this->formatDate($item[RetailerTimeSlotInterface::DATE_FIELD], null);
                $specialOpeningHours[$date] = [];

                if (is_string($item['opening_hours'])) {
                    try {
                        $item['opening_hours'] = $this->jsonSerializer->unserialize($item['opening_hours']);
                    } catch (\Exception $exception) {
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
     * Prepare date for save in DB
     *
     * @param string   $date   The Date
     * @param ?string  $format This Date format
     *
     * @throws \Exception
     * @return string
     */
    private function formatDate(string $date, ?string $format): string
    {
        return (new DateTime($date, new DateTimeZone($this->localeDate->getConfigTimezone())))->format(
            $format ?: MagentoDateTime::DATE_PHP_FORMAT
        );
    }

    /**
     * Update special opening hours by seller ids.
     *
     * @param array $data Data seller ids /Special Opening hours by days.
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return void
     */
    private function updateSpecialOpeningHoursBySellerIds(array $data): void
    {
        if (isset($data['special_opening_hours_seller_ids'])) {
            foreach ($data['special_opening_hours_seller_ids'] as $id) {
                $model = $this->retailerRepository->get($id);
                $model->setData('special_opening_hours', $data['special_opening_hours']);
                $this->retailerRepository->save($model);
            }
        }
    }
}
