<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Plugin;

use Closure;
use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Profiler;
use Smile\Map\Api\Data\GeoPointInterfaceFactory;
use Smile\Retailer\Api\Data\RetailerExtensionInterface;
use Smile\Retailer\Api\Data\RetailerInterface;
use Smile\Retailer\Model\ResourceModel\Retailer\Collection as RetailerCollection;
use Smile\StoreLocator\Api\Data\RetailerAddressInterface;
use Smile\StoreLocator\Api\Data\RetailerTimeSlotInterface;
use Smile\StoreLocator\Model\Data\RetailerTimeSlotConverter;
use Smile\StoreLocator\Model\ResourceModel\RetailerTimeSlot as TimeSlotResource;

class RetailerCollectionPlugin
{
    public function __construct(
        private JoinProcessorInterface $joinProcessor,
        private GeoPointInterfaceFactory $geoPointFactory,
        private RetailerTimeSlotConverter $timeSlotConverter,
        private TimeSlotResource $timeSlotResource
    ) {
    }

    /**
     * Append address loading to the retailer collection.
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function aroundLoad(
        RetailerCollection $collection,
        Closure $proceed,
        bool $printQuery = false,
        bool $logQuery = false
    ): RetailerCollection {
        if (!$collection->isLoaded()) {
            Profiler::start('SmileStoreLocator:EXTENSIONS_ATTRIBUTES');

            // Process joining for Address : defined via extension_attributes.xml file.
            $this->joinProcessor->process($collection);

            $proceed($printQuery, $logQuery);

            $ids = $collection->getAllIds();
            $openingHoursData = $this->timeSlotResource->getMultipleTimeSlots($ids, 'opening_hours');
            $specialOpeningHoursData = $this->timeSlotResource->getMultipleTimeSlots($ids, 'special_opening_hours');
            $entityType = get_class($collection->getNewEmptyItem());

            /** @var RetailerInterface $currentItem */
            foreach ($collection->getItems() as $currentItem) {
                // Process hydrating Item data with the extension attributes Data.
                $data = $this->joinProcessor->extractExtensionAttributes($entityType, $currentItem->getData());
                if (isset($data[ExtensibleDataInterface::EXTENSION_ATTRIBUTES_KEY])) {
                    $currentItem->setExtensionAttributes($data[ExtensibleDataInterface::EXTENSION_ATTRIBUTES_KEY]);
                }

                /** @var RetailerExtensionInterface $currentItemExtensionAttr */
                $currentItemExtensionAttr = $currentItem->getExtensionAttributes();
                /** @var DataObject|RetailerAddressInterface|null $address */
                $address = $currentItemExtensionAttr->getAddress();
                if ($address) {
                    $address->setCoordinates(
                        $this->geoPointFactory->create(
                            $address->getData()
                        )
                    );
                }

                $currentItemExtensionAttr->setOpeningHours(
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
                $currentItemExtensionAttr->setSpecialOpeningHours($specialOpeningHours);
            }
            Profiler::stop('SmileStoreLocator:EXTENSIONS_ATTRIBUTES');
        }

        return $collection;
    }
}
