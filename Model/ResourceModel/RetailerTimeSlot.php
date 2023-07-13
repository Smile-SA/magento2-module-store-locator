<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Model\ResourceModel;

use DateTime;
use Exception;
use IntlDateFormatter;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Locale\Resolver;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime as MagentoDateTime;
use Smile\StoreLocator\Api\Data\RetailerTimeSlotInterface;

/**
 * Resource Model for Time Slots items (Eg. : Opening Hours).
 */
class RetailerTimeSlot extends AbstractDb
{
    private string $locale;

    public function __construct(
        Context $context,
        private Resolver $localeResolver,
        ?string $connectionName = null
    ) {
        $this->locale = $this->localeResolver->getLocale();
        parent::__construct($context, $connectionName);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('smile_retailer_time_slots', 'retailer_id');
    }

    /**
     * Save time slots for a given retailer.
     *
     * @throws Exception
     */
    public function saveTimeSlots(int $retailerId, ?string $attributeCode, array $timeSlots): bool
    {
        $data = [];
        $this->deleteByRetailerId($retailerId, $attributeCode);

        foreach ($timeSlots as $date => $timeSlotList) {
            $dateField = is_numeric($date)
                ? RetailerTimeSlotInterface::DAY_OF_WEEK_FIELD
                : RetailerTimeSlotInterface::DATE_FIELD;

            $row = [
                'retailer_id' => $retailerId,
                'attribute_code' => $attributeCode,
                $dateField => $date,
                'start_time' => null,
                'end_time' => null,
            ];

            if (!count($timeSlotList)) {
                $data[] = $row;
                continue;
            }

            foreach ($timeSlotList as $timeSlot) {
                // phpcs:ignore Magento2.Performance.ForeachArrayMerge.ForeachArrayMerge
                $data[] = array_merge(
                    $row,
                    [
                        'start_time' => $this->dateFromHour($timeSlot->getStartTime()),
                        'end_time' => $this->dateFromHour($timeSlot->getEndTime()),
                    ]
                );
            }
        }

        $result = true;
        if (!empty($data)) {
            $result = (bool) $this->getConnection()->insertMultiple($this->getMainTable(), $data);
        }

        return $result;
    }

    /**
     * Retrieve all time slots of a given retailer.
     *
     * @throws LocalizedException
     */
    public function getTimeSlots(int $retailerId, ?string $attributeCode): array
    {
        $binds = [':retailer_id' => (int) $retailerId, ':attribute_code' => (string) $attributeCode];

        $select = $this->getConnection()->select();
        $select->from($this->getMainTable())
            ->where("retailer_id = :retailer_id")
            ->where("attribute_code = :attribute_code");

        $timeSlotData = $this->getConnection()->fetchAll($select, $binds);

        foreach ($timeSlotData as &$row) {
            foreach (['start_time', 'end_time'] as $timeField) {
                if (isset($row[$timeField]) && ($row[$timeField] !== null)) {
                    $row[$timeField] = $this->dateToHour($row[$timeField]);
                }
            }
        }

        return $timeSlotData;
    }

    /**
     * Retrieve all time slots of a given retailer list.
     *
     * @throws LocalizedException
     */
    public function getMultipleTimeSlots(array $retailerIds, ?string $attributeCode): array
    {
        $retailerIds = array_map('intval', $retailerIds);

        $select = $this->getConnection()->select();
        $select->from($this->getMainTable())
               ->where("retailer_id IN (?)", $retailerIds)
               ->where("attribute_code = ?", $attributeCode);

        $rows = $this->getConnection()->fetchAll($select);

        $timeSlotData = array_fill_keys($retailerIds, []);
        foreach ($rows as &$row) {
            foreach (['start_time', 'end_time'] as $timeField) {
                if (isset($row[$timeField]) && ($row[$timeField] !== null)) {
                    $row[$timeField] = $this->dateToHour($row[$timeField]);
                }
            }
            $timeSlotData[$row['retailer_id']][] = $row;
        }

        return $timeSlotData;
    }

    /**
     * Delete Time Slots for a given retailer Id.
     *
     * @throws LocalizedException
     */
    public function deleteByRetailerId(int $retailerId, ?string $attributeCode = null): bool
    {
        $deleteCondition = ["retailer_id = ?" => $retailerId];
        if (null !== $attributeCode) {
            $deleteCondition["attribute_code = ?"] = $attributeCode;
        }

        return (bool) $this->getConnection()->delete($this->getMainTable(), $deleteCondition);
    }

    /**
     * Build default date (01.01.1970) from an hour.
     *
     * @throws Exception
     */
    private function dateFromHour(string $hour): string
    {
        $date = new DateTime('1970-01-01 00:00:00'); // Init as 1970-01-01 since field is store on a DATETIME column.
        [$hour, $min] = explode(':', $hour);

        return $date->setTime((int) $hour, (int) $min)->format(MagentoDateTime::DATETIME_PHP_FORMAT);
    }

    /**
     * Extract hour from a date.
     */
    private function dateToHour(string $date): string
    {
        $formatter = new IntlDateFormatter(
            $this->locale,
            IntlDateFormatter::NONE,
            IntlDateFormatter::SHORT
        );

        return $formatter->format(DateTime::createFromFormat(MagentoDateTime::DATETIME_PHP_FORMAT, $date));
    }
}
