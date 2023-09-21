<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Model\Retailer;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Smile\Retailer\Api\Data\RetailerExtensionInterface;
use Smile\Retailer\Model\Retailer;
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
        /** @var Retailer $entity */
        $addressModel = $this->modelFactory->create();
        $addressModel->setRetailerId((int) $entity->getId());

        $this->resource->load($addressModel, $entity->getId(), RetailerAddressInterface::RETAILER_ID);

        $addressEntity = $this->converter->toEntity($addressModel);

        /** @var RetailerExtensionInterface $entityExtensionAttr */
        $entityExtensionAttr = $entity->getExtensionAttributes();
        $entityExtensionAttr->setAddress($addressEntity);
        $entity->setAddress($addressEntity);

        return $entity;
    }
}
