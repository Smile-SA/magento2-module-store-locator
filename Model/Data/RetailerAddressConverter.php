<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\StoreLocator
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\StoreLocator\Model\Data;

use Smile\StoreLocator\Api\Data\RetailerAddressInterface;
use Smile\StoreLocator\Api\Data\RetailerAddressInterfaceFactory as EntityFactory;
use Smile\StoreLocator\Model\RetailerAddressFactory as ModelFactory;
use Magento\Framework\Api\SimpleDataObjectConverter;

/**
 * Retailer address converter utils.
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class RetailerAddressConverter
{
    /**
     *@var EntityFactory
     */
    private $entityFactory;

    /**
     * @var ModelFactory
     */
    private $modelFactory;

    /**
     * @var string[]
     */
    private $copyFields = [
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

    /**
     * Constructor.
     *
     * @param EntityFactory $entityFactory Retailer address entity factory.
     * @param ModelFactory  $modelFactory  Retailer address model factory.
     */
    public function __construct(EntityFactory $entityFactory, ModelFactory $modelFactory)
    {
        $this->entityFactory = $entityFactory;
        $this->modelFactory  = $modelFactory;
    }

    /**
     * Convert the entity to a new model object.
     *
     * @param \Smile\StoreLocator\Api\Data\RetailerAddressInterface $entity Entity.
     *
     * @return \Smile\StoreLocator\Model\RetailerAddress
     */
    public function toModel(\Smile\StoreLocator\Api\Data\RetailerAddressInterface $entity)
    {
        return $this->convert($this->modelFactory, $entity);
    }

    /**
     * Convert the model to a new entity object.
     *
     * @param \Smile\StoreLocator\Model\RetailerAddress $model Model.
     *
     * @return \Smile\StoreLocator\Api\Data\RetailerAddressInterface
     */
    public function toEntity(\Smile\StoreLocator\Model\RetailerAddress $model)
    {
        return $this->convert($this->entityFactory, $model);
    }

    /**
     * Process conversion.
     *
     * @param mixed $factory New item factory.
     * @param mixed $source  Source object.
     *
     * @return mixed
     */
    private function convert($factory, $source)
    {
        $target = $factory->create();
        $target = $this->copyFields($source, $target);

        return $target;
    }

    /**
     * Copy field from a source object to a target one securely.
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * @param mixed $source Source object.
     * @param mixed $target Target object.
     *
     * @return mixed
     */
    private function copyFields($source, $target)
    {
        $sourceValues        = $this->extractValues($source);
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
     *
     * @param mixed $source Source object.
     *
     * @return mixed[]
     */
    private function extractValues($source)
    {
        $values = [];

        $sourceObjectMethods = get_class_methods(get_class($source));

        foreach ($this->copyFields as $currentField) {
            $getMethodName = 'get' . SimpleDataObjectConverter::snakeCaseToUpperCamelCase($currentField);
            $hasMethodName = 'has' . SimpleDataObjectConverter::snakeCaseToUpperCamelCase($currentField);

            if ((in_array($hasMethodName, $sourceObjectMethods) && $source->$hasMethodName()) ||
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
