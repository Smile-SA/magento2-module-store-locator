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
namespace Smile\StoreLocator\Model\Retailer\Attribute\Backend;

use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Magento\Framework\Exception\CouldNotSaveException;
use Smile\StoreLocator\Model\Url;

/**
 * Retailer URL key backend model.
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class UrlKey extends AbstractBackend
{
    /**
     * @var Url
     */
    private Url $urlModel;

    /**
     * Constructor.
     *
     * @param Url $urlModel Retailer URL Model.
     */
    public function __construct(Url $urlModel)
    {
        $this->urlModel = $urlModel;
    }

    /**
     * {@inheritDoc}
     */
    public function beforeSave($object): self
    {
        $urlKey = $this->urlModel->getUrlKey($object);

        if ($urlKey !== $object->getUrlKey()) {
            $object->setUrlKey($urlKey);
        }

        $objectId        = (int) $object->getId();
        $retailerIdCheck = (int) $this->urlModel->checkIdentifier($urlKey);

        if ($retailerIdCheck !== false && ($objectId !== $retailerIdCheck)) {
            throw new CouldNotSaveException(__('Retailer url_key "%1" already exists.', $urlKey));
        }

        return $this;
    }
}
