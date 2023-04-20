<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\StoreLocator
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\StoreLocator\Model\ResourceModel;

use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Smile\StoreLocator\Api\Data\RetailerAddressInterface;

/**
 * Retailer address resource model.
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class RetailerAddress extends AbstractDb
{
    /**
     * @var EntityManager
     */
    private EntityManager $entityManager;

    /**
     * @var MetadataPool
     */
    private MetadataPool $metadataPool;

    /**
     *
     * @param Context           $context        DB context.
     * @param EntityManager     $entityManager  Entity manager.
     * @param MetadataPool      $metadataPool   Entity metadata pool.
     * @param ?string           $connectionName Connection name.
     */
    public function __construct(
        Context $context,
        EntityManager $entityManager,
        MetadataPool $metadataPool,
        ?string $connectionName = null
    ) {
        $this->entityManager = $entityManager;
        $this->metadataPool  = $metadataPool;
        parent::__construct($context, $connectionName);
    }

    /**
     * {@inheritDoc}
     */
    public function getConnection()
    {
        return $this->metadataPool->getMetadata(RetailerAddressInterface::class)->getEntityConnection();
    }

    /**
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     *
     * {@inheritDoc}
     */
    protected function _construct()
    {
        $metadata = $this->metadataPool->getMetadata(RetailerAddressInterface::class);
        $this->_init($metadata->getEntityTable(), $metadata->getIdentifierField());
    }
}
