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
namespace Smile\StoreLocator\Model\Retailer\Attribute\Backend;

use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Psr\Log\LoggerInterface;

/**
 * Image attribute backend model.
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 * @author   Guillaume Vrac <guillaume.vrac@smile.fr>
 */
class Image extends AbstractBackend
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * Image uploader
     *
     * @var \Magento\Catalog\Model\ImageUploader
     */
    private $imageUploader;

    /**
     * Image Backend constructor.
     *
     * @param \Psr\Log\LoggerInterface             $logger        Resource Logger
     * @param \Magento\Catalog\Model\ImageUploader $imageUploader Image Uploader
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Catalog\Model\ImageUploader $imageUploader
    ) {
        $this->logger = $logger;
        $this->imageUploader = $imageUploader;
    }

    /**
     * Preprocess the uploaded file.
     *
     * @SuppressWarnings(PHPMD.ElseExpression)
     *
     * @param \Magento\Framework\DataObject $object The Entity being saved
     *
     * @return \Smile\StoreLocator\Model\Retailer\Attribute\Backend\Image
     */
    public function beforeSave($object)
    {
        $image = $object->getData($this->getAttribute()->getName(), null);

        $imageFile = null;

        $object->unsetData($this->getAttribute()->getName());
        if (is_array($image)) {
            if (!empty($image['delete'])) {
                $imageFile = null;
                $object->setData($this->getAttribute()->getName(), $imageFile);
            } elseif (isset($image[0]['name'])) {
                if (isset($image[0]['tmp_name'])) {
                    $imageFile = $image[0]['name'];
                    $object->setData($this->getAttribute()->getName(), $imageFile);
                }
            }
        }
    }

    /**
     * Save uploaded file and set its name to category
     *
     * @param \Magento\Framework\DataObject $object The Entity being saved
     *
     * @return \Smile\StoreLocator\Model\Retailer\Attribute\Backend\Image
     */
    public function afterSave($object)
    {
        $image = $object->getData($this->getAttribute()->getName(), null);

        if ($image !== null) {
            try {
                $this->imageUploader->moveFileFromTmp($image);
            } catch (\Exception $e) {
                $this->logger->critical($e);
            }
        }

        return $this;
    }
}
