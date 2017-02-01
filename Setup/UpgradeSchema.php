<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\StoreLocator
 * @author    Romain Ruaud <romain.ruaud@smile.fr>
 * @copyright 2017 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\StoreLocator\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Upgrade Schema for StoreLocator Module
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * Installs DB schema for a module
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param SchemaSetupInterface   $setup   Setup
     * @param ModuleContextInterface $context Context
     *
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->createOpeningHoursTable($setup);
        }
        $setup->endSetup();
    }
    /**
     * Create Opening Hours main table
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup Setup instance
     */
    private function createOpeningHoursTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable("smile_retailer_time_slots"))
            ->addColumn(
                "retailer_id",
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Retailer Id'
            )->addColumn(
                "attribute_code",
                Table::TYPE_TEXT,
                25,
                ['nullable' => false],
                'Retailer Id'
            )->addColumn(
                "day_of_week",
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => true, 'default' => null],
                'Day Of Week'
            )->addColumn(
                "date",
                Table::TYPE_DATE,
                null,
                ['nullable' => true, 'default' => null],
                'Opening Date, if any'
            )->addColumn(
                "start_time",
                Table::TYPE_DATETIME, // Hack : Magento does not support TIME column on its DDL. This column will contain full datetime but work only with hours.
                null,
                ['nullable' => true, 'default' => null],
                'Start Time'
            )->addColumn(
                "end_time",
                Table::TYPE_DATETIME, // Hack : Magento does not support TIME column on its DDL. This column will contain full datetime but work only with hours.
                null,
                ['nullable' => true, 'default' => null],
                'End Time'
            )->addForeignKey(
                $setup->getFkName('smile_retailer_time_slots', 'retailer_id', 'smile_seller_entity', 'entity_id'),
                'retailer_id',
                $setup->getTable('smile_seller_entity'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('Smile Retailer Opening Hours Table');
        $setup->getConnection()->createTable($table);
    }
}
