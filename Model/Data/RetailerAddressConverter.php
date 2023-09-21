<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Model\Data;

use Magento\Framework\Api\SimpleDataObjectConverter;
use Smile\StoreLocator\Api\Data\RetailerAddressInterface;
use Smile\StoreLocator\Api\Data\RetailerAddressInterfaceFactory as EntityFactory;
use Smile\StoreLocator\Model\RetailerAddress;
use Smile\StoreLocator\Model\RetailerAddressFactory as ModelFactory;

/**
 * Retailer address converter utils.
 */
class RetailerAddressConverter
{
    /**
     * @var string[]
     */
    private array $copyFields = [
        RetailerAddressInterface::ADDRESS_ID,
        RetailerAddressInterface::RETAILER_ID,
        RetailerAddressInterface::STREET,
        RetailerAddressInterface::POSTCODE,
        RetailerAddressInterface::CITY,
        RetailerAddressInterface::REGION,
        RetailerAddressInterface::REGION_ID,
        RetailerAddressInterface::COUNTRY_ID,
        RetailerAddressInterface::COORDINATES,
    ];

    public function __construct(private EntityFactory $entityFactory, private ModelFactory $modelFactory)
    {
    }

    /**
     * Convert the entity to a new model object.
     */
    public function toModel(RetailerAddressInterface $entity): RetailerAddress
    {
        return $this->convert($this->modelFactory, $entity);
    }

    /**
     * Convert the model to a new entity object.
     */
    public function toEntity(RetailerAddress $model): RetailerAddressInterface
    {
        return $this->convert($this->entityFactory, $model);
    }

    /**
     * Process conversion.
     */
    private function convert(mixed $factory, mixed $source): mixed
    {
        $target = $factory->create();

        return $this->copyFields($source, $target);
    }

    /**
     * Copy field from a source object to a target one securely.
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private function copyFields(mixed $source, mixed $target): mixed
    {
        $sourceValues = $this->extractValues($source);
        $targetObjectMethods = get_class_methods(get_class($target));
        foreach ($sourceValues as $currentField => $value) {
            $setMethodName = 'set' . SimpleDataObjectConverter::snakeCaseToUpperCamelCase($currentField);
            if (in_array($setMethodName, $targetObjectMethods)) {
                $target->{$setMethodName}($value);
            } elseif (in_array('setData', $targetObjectMethods)) {
                $target->setData($currentField, $value);
            }
        }

        return $target;
    }

    /**
     * Extract values from a source object securely.
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function extractValues(mixed $source): array
    {
        $values = [];

        $sourceObjectMethods = get_class_methods(get_class($source));

        foreach ($this->copyFields as $currentField) {
            $getMethodName = 'get' . SimpleDataObjectConverter::snakeCaseToUpperCamelCase($currentField);
            $hasMethodName = 'has' . SimpleDataObjectConverter::snakeCaseToUpperCamelCase($currentField);

            if (
                (in_array($hasMethodName, $sourceObjectMethods) && $source->$hasMethodName()) ||
                (in_array('hasData', $sourceObjectMethods) && $source->hasData($currentField)) ||
                (in_array($getMethodName, $sourceObjectMethods) && $source->$getMethodName() !== null) ||
                (in_array('getData', $sourceObjectMethods) && $source->getData($currentField) !== null)
            ) {
                if (in_array($getMethodName, $sourceObjectMethods)) {
                    $values[$currentField] = $source->{$getMethodName}();
                } elseif (in_array('getData', $sourceObjectMethods)) {
                    $values[$currentField] = $source->getData($currentField);
                }
            }
        }

        return $values;
    }
}
