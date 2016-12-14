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
namespace Smile\StoreLocator\Model\ResourceModel;

use Magento\Framework\DB\Select;

/**
 * Retailer URL resource model.
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class Url extends \Smile\Seller\Model\ResourceModel\Seller
{
    /**
     * Check an URL key exists and returns the retailer id. False if no retailer found.
     *
     * @param urlKey $urlKey  URL key.
     * @param int    $storeId Store Id.
     *
     * @return int|false
     */
    public function checkIdentifier($urlKey, $storeId)
    {
        $urlKeyAttribute = $this->getAttribute('url_key');
        $select = $this->getConnection()->select();

        $select->from($urlKeyAttribute->getBackendTable(), ['entity_id'])
            ->where('attribute_id = ?', $urlKeyAttribute->getAttributeId())
            ->where('value = ?', $urlKey)
            ->where('store_id IN(?, 0)', $storeId)
            ->order('store_id ' . Select::SQL_DESC)
            ->limit(1);

        return $this->getConnection()->fetchOne($select);
    }
}