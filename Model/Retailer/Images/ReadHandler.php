<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\StoreLocator
 * @author    Romain Ruaud <romain.ruaud@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\StoreLocator\Model\Retailer\Images;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Smile\StoreLocator\Api\Data\Retailer\ImageInterfaceFactory;
use Smile\Retailer\Api\RetailerRepositoryInterface;

/**
 * Read Handler for Retailer Images.
 * Build a pseudo gallery of images when loading the Retailer.
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
class ReadHandler implements ExtensionInterface
{
    /**
     * @var array
     */
    private $imagesAttributes;

    /**
     * @var RetailerRepositoryInterface
     */
    private $retailerRepository;

    /**
     * @var ImageInterfaceFactory
     */
    private $imageFactory;

    /**
     * Images constructor.
     *
     * @param RetailerRepositoryInterface $retailerRepository Retailer Repository
     * @param ImageInterfaceFactory       $imageFactory       Retailer Image Factory
     * @param array                       $imagesAttributes   The attributes code to put into the gallery
     */
    public function __construct(RetailerRepositoryInterface $retailerRepository, ImageInterfaceFactory $imageFactory, $imagesAttributes = [])
    {
        $this->retailerRepository = $retailerRepository;
        $this->imagesAttributes   = $imagesAttributes;
        $this->imageFactory       = $imageFactory;
    }

    /**
     * Perform action on relation/extension attribute
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param \Smile\Seller\Api\Data\RetailerInterface $entity    The entity
     * @param array                                  $arguments Arguments
     *
     * @return object|bool
     */
    public function execute($entity, $arguments = [])
    {
        /** @var $entity \Smile\Seller\Api\Data\SellerInterface */
        if ((int) $entity->getAttributeSetId() !== (int) $this->retailerRepository->getEntityAttributeSetId()) {
            return $entity;
        }

        $entityExtension = $entity->getExtensionAttributes();
        $images          = [];

        foreach ($this->imagesAttributes as $attributeCode) {
            if ($entity->getData($attributeCode)) {
                $imageData = [
                    'url' => (string) $entity->getImageAttributeUrl($attributeCode),
                    'alt' => (string) $entity->getData($attributeCode . '_alt'),
                ];
                $images[] = $this->imageFactory->create($imageData);
            }
        }

        $entityExtension->setImages($images);
        $entity->setExtensionAttributes($entityExtension);

        return $entity;
    }
}
