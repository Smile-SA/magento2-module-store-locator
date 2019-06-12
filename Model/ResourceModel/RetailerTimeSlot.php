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
namespace Smile\StoreLocator\Model\ResourceModel;

use Magento\Framework\Locale\Resolver;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime;
use Smile\StoreLocator\Api\Data\RetailerTimeSlotInterface;
use Zend_Date;

/**
 * Resource Model for Time Slots items (Eg. : Opening Hours)
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
class RetailerTimeSlot extends AbstractDb
{
    /**
     * @var \Magento\Framework\Locale\Resolver
     */
    private $localeResolver;

    /**
     * @var null
     */
    private $locale;

    /**
     * RetailerTimeSlot constructor.
     *
     * @param Context     $context        Context
     * @param Resolver    $localeResolver Locale Resolver
     * @param null|string $connectionName Connection Name
     */
    public function __construct(Context $context, Resolver $localeResolver, $connectionName = null)
    {
        $this->localeResolver = $localeResolver;
        $this->locale = $this->localeResolver->getLocale();
        parent::__construct($context, $connectionName);
    }

    /**
     * Save time slots for a given retailer
     *
     * @param integer $retailerId    The retailer id
     * @param null    $attributeCode The time slot type to store
     * @param array   $timeSlots     The time slots to save, array based
     *
     * @return bool
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveTimeSlots($retailerId, $attributeCode, $timeSlots)
    {
        $data = [];
        $this->deleteByRetailerId($retailerId, $attributeCode);

        foreach ($timeSlots as $date => $timeSlotList) {
            $dateField = is_numeric($date) ? RetailerTimeSlotInterface::DAY_OF_WEEK_FIELD : RetailerTimeSlotInterface::DATE_FIELD;

            $row = [
                "retailer_id"    => $retailerId,
                "attribute_code" => $attributeCode,
                $dateField       => $date,
                "start_time"     => null,
                "end_time"       => null,
            ];

            if (!count($timeSlotList)) {
                $data[] = $row;
                continue;
            }

            foreach ($timeSlotList as $timeSlot) {
                $data[] = array_merge(
                    $row,
                    [
                        'start_time' => $this->dateFromHour($timeSlot->getStartTime()),
                        'end_time'   => $this->dateFromHour($timeSlot->getEndTime()),
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
     * Retrieve all time slots of a given retailer
     *
     * @param integer $retailerId    The retailer id
     * @param null    $attributeCode The time slot type to retrieve
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getTimeSlots($retailerId, $attributeCode)
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
     * Retrieve all time slots of a given retailer list
     *
     * @param integer[] $retailerIds   The retailer ids
     * @param null      $attributeCode The time slot type to retrieve
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getMultipleTimeSlots($retailerIds, $attributeCode)
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
     * Delete Time Slots for a given retailer Id
     *
     * @param integer $retailerId    The retailer id
     * @param null    $attributeCode The time slot type to delete, if any
     *
     * @return bool
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteByRetailerId($retailerId, $attributeCode = null)
    {
        $deleteCondition = ["retailer_id = ?" => $retailerId];
        if (null !== $attributeCode) {
            $deleteCondition["attribute_code = ?"] = $attributeCode;
        }

        return (bool) $this->getConnection()->delete($this->getMainTable(), $deleteCondition);
    }

    /**
     * Define main table name and attributes table
     *
     * @SuppressWarnings(PHPMD.CamelCaseMethodName) The method is inherited
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('smile_retailer_time_slots', 'retailer_id');
    }

    /**
     * Build default date (01.01.1970) from an hour
     *
     * @param string $hour The hour
     *
     * @return string
     */
    private function dateFromHour($hour)
    {
        $date = new Zend_Date(0, Zend_Date::TIMESTAMP); // Init as 1970-01-01 since field is store on a DATETIME column.
        $date->setTime($hour);

        return $date->toString(DateTime::DATETIME_INTERNAL_FORMAT);
    }

    /**
     * Extract hour from a date
     *
     * @param string $date The date
     *
     * @return string
     */
    private function dateToHour($date)
    {
        setlocale(LC_TIME, $this->locale);

        return $date = strftime('%H:%M', strtotime($date));
    }
}
