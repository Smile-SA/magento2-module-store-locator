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

use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Smile\StoreLocator\Api\Data\RetailerTimeSlotInterface;
use Smile\StoreLocator\Api\Data\RetailerTimeSlotInterfaceFactory;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Smile\Retailer\Api\RetailerRepositoryInterface;

/**
 * Post Data Handler for Retailer Opening Hours
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 * @author   Fanny DECLERCK <fadec@smile.fr>
 */
class SpecialOpeningHoursPostDataHandler implements \Smile\Retailer\Model\Retailer\PostDataHandlerInterface
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
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $localeDate;

    /**
     * OpeningHoursPostDataHandler constructor.
     *
     * @param RetailerTimeSlotInterfaceFactory $timeSlotFactory    Time Slot Factory
     * @param JsonHelper                       $jsonHelper         JSON Helper
     * @param TimezoneInterface                $localeDate         The Locale Date Interface
     * @param RetailerRepositoryInterface      $retailerRepository Retailer Repository Interface
     */
    public function __construct(
        RetailerTimeSlotInterfaceFactory $timeSlotFactory,
        JsonHelper $jsonHelper,
        TimezoneInterface $localeDate,
        RetailerRepositoryInterface $retailerRepository
    ) {
        $this->timeSlotFactory    = $timeSlotFactory;
        $this->jsonHelper         = $jsonHelper;
        $this->localeDate         = $localeDate;
        $this->retailerRepository = $retailerRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function getData(\Smile\Retailer\Api\Data\RetailerInterface $retailer, $data)
    {
        if (isset($data['special_opening_hours'])) {
            $specialOpeningHours = [];

            foreach ($data['special_opening_hours'] as $item) {
                if (!isset($item[RetailerTimeSlotInterface::DATE_FIELD]) || '' === $item[RetailerTimeSlotInterface::DATE_FIELD]) {
                    continue;
                }

                $date = $this->formatDate($item[RetailerTimeSlotInterface::DATE_FIELD]);
                $specialOpeningHours[$date] = [];

                if (is_string($item['opening_hours'])) {
                    try {
                        $item['opening_hours'] = $this->jsonHelper->jsonDecode($item['opening_hours']);
                    } catch (\Zend_Json_Exception $exception) {
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
     * @param string $date   The Date
     * @param string $format This Date format
     *
     * @return string
     */
    private function formatDate($date, $format = null)
    {
        if (null === $format) {
            $format = $this->localeDate->getDateFormatWithLongYear();
        }
        $date = new \Zend_Date($date, $format);

        return $date->toString(DateTime::DATE_INTERNAL_FORMAT);
    }

    /**
     * Update special opening hours by seller ids.
     *
     * @param array $data Data seller ids /Special Opening hours by days.
     *
     * @return void
     */
    private function updateSpecialOpeningHoursBySellerIds($data)
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
