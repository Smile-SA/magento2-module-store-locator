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
namespace Smile\StoreLocator\Model\Retailer;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Smile\StoreLocator\Api\Data\RetailerAddressInterface;
use Smile\StoreLocator\Model\RetailerAddressFactory as ModelFactory;
use Smile\StoreLocator\Model\ResourceModel\RetailerAddress as ResourceModel;
use Smile\StoreLocator\Model\Data\RetailerAddressConverter as Converter;

/**
 * Retailer address read handler.
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class AddressReadHandler implements ExtensionInterface
{
    /**
     * @var ModelFactory
     */
    private ModelFactory $modelFactory;

    /**
     * @var ResourceModel
     */
    private ResourceModel $resource;

    /**
     * @var Converter
     */
    private Converter $converter;

    /**
     * Constructor.
     *
     * @param ModelFactory  $modelFactory Address model factory.
     * @param ResourceModel $resource     Address resource model.
     * @param Converter     $converter    Adress converter.
     */
    public function __construct(
        ModelFactory $modelFactory,
        ResourceModel $resource,
        Converter $converter
    ) {
        $this->modelFactory = $modelFactory;
        $this->resource     = $resource;
        $this->converter    = $converter;
    }

    /**
     * {@inheritDoc}
     */
    public function execute($entity, $arguments = []): bool|object
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
