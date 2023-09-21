<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Model\ResourceModel;

use Magento\Framework\DB\Select;
use Smile\Seller\Model\ResourceModel\Seller;

/**
 * Retailer URL resource model.
 */
class Url extends Seller
{
    /**
     * Check an URL key exists and returns the retailer id. False if no retailer found.
     */
    public function checkIdentifier(string $urlKey, int $storeId): int
    {
        $urlKeyAttribute = $this->getAttribute('url_key');
        $select = $this->getConnection()->select();

        $select->from($urlKeyAttribute->getBackendTable(), ['entity_id'])
            ->where('attribute_id = ?', $urlKeyAttribute->getAttributeId())
            ->where('value = ?', $urlKey)
            ->where('store_id IN(?, 0)', $storeId)
            ->order('store_id ' . Select::SQL_DESC)
            ->limit(1);

        return (int) $this->getConnection()->fetchOne($select);
    }
}
