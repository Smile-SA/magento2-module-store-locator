<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Model\ResourceModel;

use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Smile\StoreLocator\Api\Data\RetailerAddressInterface;

/**
 * Retailer address resource model.
 */
class RetailerAddress extends AbstractDb
{
    public function __construct(
        Context $context,
        private MetadataPool $metadataPool,
        ?string $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $metadata = $this->metadataPool->getMetadata(RetailerAddressInterface::class);
        $this->_init($metadata->getEntityTable(), $metadata->getIdentifierField());
    }

    /**
     * @inheritdoc
     */
    public function getConnection()
    {
        return $this->metadataPool->getMetadata(RetailerAddressInterface::class)->getEntityConnection();
    }
}
