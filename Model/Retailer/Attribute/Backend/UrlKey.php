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
     * @var \Smile\StoreLocator\Model\Url
     */
    private $urlModel;

    /**
     * Constructor.
     *
     * @param \Smile\StoreLocator\Model\Url $urlModel Retailer URL Model.
     */
    public function __construct(\Smile\StoreLocator\Model\Url $urlModel)
    {
        $this->urlModel = $urlModel;
    }

    /**
     * {@inheritDoc}
     */
    public function beforeSave($object)
    {
        $urlKey = $this->urlModel->getUrlKey($object);

        if ($urlKey !== $object->getCustomAttribute('url_key')?->getValue()) {
            $object->setCustomAttribute('url_key', $urlKey);
        }

        $retailerIdCheck = $this->urlModel->checkIdentifier($urlKey);

        if ($retailerIdCheck !== false && ((int) $object->getId() !== (int) $retailerIdCheck)) {
            throw new CouldNotSaveException(__('Retailer url_key "%1" already exists.', $urlKey));
        }

        return $this;
    }
}
