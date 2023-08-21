<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Model\Retailer;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Smile\StoreLocator\Api\Data\RetailerAddressInterface;
use Smile\StoreLocator\Model\Data\RetailerAddressConverter as Converter;
use Smile\StoreLocator\Model\ResourceModel\RetailerAddress as ResourceModel;
use Smile\StoreLocator\Model\RetailerAddressFactory as ModelFactory;

/**
 * Retailer address save handler.
 */
class AddressSaveHandler implements ExtensionInterface
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
        $addressEntity = $entity->getAddress();
        $addressEntity->setRetailerId((int) $entity->getId());

        $addressModel  = $this->modelFactory->create();
        $this->resource->load($addressModel, $entity->getId(), RetailerAddressInterface::RETAILER_ID);

        if ($addressModel->getAddressId()) {
            $addressEntity->setAddressId($addressModel->getAddressId());
        }

        $addressModel = $this->converter->toModel($addressEntity);

        if (!$addressModel->getAddressId()) {
            $addressModel->setId(null);
            $addressModel->isObjectNew(true);
        }

        $this->resource->save($addressModel);

        return $entity;
    }
}
