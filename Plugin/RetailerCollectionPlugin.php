<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\StoreLocator
 * @author    Aurelien FOUCRET <aurelien.foucret@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\StoreLocator\Plugin;

use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Smile\Map\Api\Data\GeoPointInterfaceFactory;
use Smile\Retailer\Api\Data\RetailerInterface;
use Smile\Retailer\Model\ResourceModel\Retailer\Collection as RetailerCollection;
use Smile\StoreLocator\Api\Data\RetailerTimeSlotInterface;
use Smile\StoreLocator\Model\Data\RetailerTimeSlotConverter;
use Smile\StoreLocator\Model\ResourceModel\RetailerTimeSlot as TimeSlotResource;

/**
 * Retailer collection plugin.
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class RetailerCollectionPlugin
{
    /**
     * @var \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface
     */
    private $joinProcessor;

    /**
     * @var \Smile\Map\Api\Data\GeoPointInterfaceFactory
     */
    private $geoPointFactory;

    /**
     * @var \Smile\StoreLocator\Model\Data\RetailerTimeSlotConverter
     */
    private $timeSlotConverter;

    /**
     * @var \Smile\StoreLocator\Model\ResourceModel\RetailerTimeSlot
     */
    private $timeSlotResource;

    /**
     * Constructor.
     *
     * @param JoinProcessorInterface    $joinProcessor     Extension Attribute Join Processor
     * @param GeoPointInterfaceFactory  $geoPointFactory   GeoPoint Factory
     * @param RetailerTimeSlotConverter $timeSlotConverter Time Slots Converter
     * @param TimeSlotResource          $timeSlotsResource Time Slots Resource
     */
    public function __construct(
        JoinProcessorInterface $joinProcessor,
        GeoPointInterfaceFactory $geoPointFactory,
        RetailerTimeSlotConverter $timeSlotConverter,
        TimeSlotResource $timeSlotsResource
    ) {
        $this->joinProcessor     = $joinProcessor;
        $this->geoPointFactory   = $geoPointFactory;
        $this->timeSlotConverter = $timeSlotConverter;
        $this->timeSlotResource  = $timeSlotsResource;
    }

    /**
     * Append address loading to the retailer collection.
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * @param RetailerCollection $collection Collection loaded.
     * @param \Closure           $proceed    Original method.
     * @param bool               $printQuery Print queries used to load the collection.
     * @param bool               $logQuery   Log queries used to load the collection.
     *
     * @return \Smile\Retailer\Model\ResourceModel\Retailer\Collection
     */
    public function aroundLoad(RetailerCollection $collection, \Closure $proceed, $printQuery = false, $logQuery = false)
    {
        if (!$collection->isLoaded()) {
            \Magento\Framework\Profiler::start('SmileStoreLocator:EXTENSIONS_ATTRIBUTES');

            // Process joining for Address : defined via extension_attributes.xml file.
            $this->joinProcessor->process($collection);

            $proceed($printQuery, $logQuery);

            $ids                     = $collection->getAllIds();
            $openingHoursData        = $this->timeSlotResource->getMultipleTimeSlots($ids, 'opening_hours');
            $specialOpeningHoursData = $this->timeSlotResource->getMultipleTimeSlots($ids, 'special_opening_hours');
            $entityType              = get_class($collection->getNewEmptyItem());

            /** @var RetailerInterface $currentItem */
            foreach ($collection->getItems() as $currentItem) {
                // Process hydrating Item data with the extension attributes Data.
                $data = $this->joinProcessor->extractExtensionAttributes($entityType, $currentItem->getData());
                if ($data[ExtensibleDataInterface::EXTENSION_ATTRIBUTES_KEY]) {
                    $currentItem->setExtensionAttributes($data[ExtensibleDataInterface::EXTENSION_ATTRIBUTES_KEY]);
                }

                if ($currentItem->getExtensionAttributes()->getAddress()) {
                    $currentItem->getExtensionAttributes()
                                ->getAddress()
                                ->setCoordinates(
                                    $this->geoPointFactory->create(
                                        $currentItem->getExtensionAttributes()->getAddress()->getData()
                                    )
                                );
                }

                $currentItem->getExtensionAttributes()->setOpeningHours(
                    $this->timeSlotConverter->toEntity(
                        $openingHoursData[$currentItem->getId()],
                        RetailerTimeSlotInterface::DAY_OF_WEEK_FIELD
                    )
                );

                $specialOpeningHours = $this->timeSlotConverter->toEntity(
                    $specialOpeningHoursData[$currentItem->getId()],
                    RetailerTimeSlotInterface::DATE_FIELD
                );
                ksort($specialOpeningHours);
                $currentItem->getExtensionAttributes()->setSpecialOpeningHours($specialOpeningHours);
            }
            \Magento\Framework\Profiler::stop('SmileStoreLocator:EXTENSIONS_ATTRIBUTES');
        }

        return $collection;
    }
}
