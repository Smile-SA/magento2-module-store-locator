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
namespace Smile\StoreLocator\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Store locator schema setup.
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $this->createRetailerAddressTable($setup);
        $setup->endSetup();
    }

    /**
     * Create the reyailer address table.
     *
     * @param SchemaSetupInterface $setup Schema setup.
     *
     * @return $this
     */
    private function createRetailerAddressTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable('smile_retailer_address'))
            ->addColumn('address_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'Address ID')
            ->addColumn('retailer_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable' => false], 'Retailer Id')
            ->addColumn('created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT], 'Created At')
            ->addColumn('updated_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE], 'Updated At')
            ->addColumn('street', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, ['nullable' => false], 'Street Address')
            ->addColumn('postcode', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => true, 'default' => null], 'Zip/Postal Code')
            ->addColumn('city', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'City')
            ->addColumn('region', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => true, 'default' => null], 'State/Province')
            ->addColumn('region_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable' => true, 'default' => null], 'State/Province')
            ->addColumn('country_id', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'Country')
            ->addColumn('latitude', \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT, null, ['nullable' => false], 'Latitude')
            ->addColumn('longitude', \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT, null, ['nullable' => false], 'Longitude')
            ->addIndex($setup->getIdxName('smile_retailer_address', ['retailer_id']), ['retailer_id'], \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
            ->addForeignKey(
                $setup->getFkName('smile_retailer_address', 'retailer_id', 'smile_seller_entity', 'entity_id'),
                'retailer_id',
                $setup->getTable('smile_seller_entity'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment('Retailer Address');

        $setup->getConnection()->createTable($table);

        return $this;
    }
}
