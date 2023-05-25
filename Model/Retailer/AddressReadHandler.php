<?php

namespace Smile\StoreLocator\Model\Retailer;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Smile\StoreLocator\Api\Data\RetailerAddressInterface;
use Smile\StoreLocator\Model\Data\RetailerAddressConverter as Converter;
use Smile\StoreLocator\Model\ResourceModel\RetailerAddress as ResourceModel;
use Smile\StoreLocator\Model\RetailerAddressFactory as ModelFactory;

/**
 * Retailer address read handler.
 */
class AddressReadHandler implements ExtensionInterface
{
    public function __construct(
        private ModelFactory $modelFactory,
        private ResourceModel $resource,
        private Converter $converter
    ) {
    }

    /**
     * @inheritdoc
     */
    public function execute($entity, $arguments = [])
    {
        /** @var $entity \Smile\Retailer\Model\Retailer */
        $addressModel = $this->modelFactory->create();
        $addressModel->setRetailerId($entity->getId());

        $this->resource->load($addressModel, $entity->getId(), RetailerAddressInterface::RETAILER_ID);

        $addressEntity = $this->converter->toEntity($addressModel);

        $entity->getExtensionAttributes()->setAddress($addressEntity);
        $entity->setAddress($addressEntity);

        return $entity;
    }
}
