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
use Smile\StoreLocator\Model\RetailerAddressFactory as ModelFactory;
use Smile\StoreLocator\Model\ResourceModel\RetailerAddress as ResourceModel;
use Smile\StoreLocator\Model\Data\RetailerAddressConverter as Converter;
use Smile\StoreLocator\Api\Data\RetailerAddressInterface;

/**
 * Retailer address save handler.
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class AddressSaveHandler implements ExtensionInterface
{
    /**
     * @var ModelFactory
     */
    private $modelFactory;

    /**
     * @var ResourceModel
     */
    private $resource;

    /**
     * @var Converter
     */
    private $converter;

    /**
     * Constructor.
     *
     * @param ModelFactory  $modelFactory Model factory.
     * @param ResourceModel $resource     Resource model.
     * @param Converter     $converter    Entity / Model converter.
     */
    public function __construct(ModelFactory $modelFactory, ResourceModel $resource, Converter $converter)
    {
        $this->modelFactory = $modelFactory;
        $this->resource     = $resource;
        $this->converter    = $converter;
    }

    /**
     * {@inheritDoc}
     */
    public function execute($entity, $arguments = [])
    {
        $addressEntity = $entity->getExtensionAttributes()->getAddress();
        $addressEntity->setRetailerId($entity->getId());

        $addressModel  = $this->modelFactory->create();
        $this->resource->load($addressModel, $entity->getId(), RetailerAddressInterface::RETAILER_ID);

        if ($addressModel->getId()) {
            $addressEntity->setId($addressModel->getId());
        }

        $addressModel = $this->converter->toModel($addressEntity);

        $this->resource->save($addressModel);

        return $entity;
    }
}
