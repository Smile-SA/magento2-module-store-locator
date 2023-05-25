<?php

namespace Smile\StoreLocator\Model\Retailer\Attribute\Backend;

use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Magento\Framework\Exception\CouldNotSaveException;
use Smile\StoreLocator\Model\Url;

/**
 * Retailer URL key backend model.
 */
class UrlKey extends AbstractBackend
{
    public function __construct(private Url $urlModel)
    {
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($object)
    {
        $urlKey = $this->urlModel->getUrlKey($object);

        if ($urlKey !== $object->getUrlKey()) {
            $object->setUrlKey($urlKey);
        }

        $objectId = (int) $object->getId();
        $retailerIdCheck = $this->urlModel->checkIdentifier($urlKey);

        if ($retailerIdCheck !== false && $objectId !== $retailerIdCheck) {
            throw new CouldNotSaveException(__('Retailer url_key "%1" already exists.', $urlKey));
        }

        return $this;
    }
}
