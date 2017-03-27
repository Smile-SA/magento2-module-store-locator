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
use Magento\Framework\Setup\SchemaSetupInterface;
use Smile\Retailer\Api\Data\RetailerInterface;
use Smile\Seller\Api\Data\SellerInterface;

/**
 * StoreLocator setup class
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
class StoreLocatorSetup
{
    /**
     * Create the retailer address table.
     *
     * @param SchemaSetupInterface $setup Schema setup.
     *
     * @return $this
     */
    public function createRetailerAddressTable(SchemaSetupInterface $setup)
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
    /**
     * Create Opening Hours main table
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup Setup instance
     */
    public function createOpeningHoursTable(SchemaSetupInterface $setup)
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

    /**
     * Add 'url_key' attribute to Retailers
     *
     * @param \Magento\Eav\Setup\EavSetup $eavSetup EAV module Setup
     */
    public function addUrlKeyAttribute($eavSetup)
    {
        $entityId  = SellerInterface::ENTITY;
        $attrSetId = RetailerInterface::ATTRIBUTE_SET_RETAILER;
        $groupId   = 'general';

        $eavSetup->addAttribute(
            SellerInterface::ENTITY,
            'url_key',
            [
                'type'         => 'varchar',
                'label'        => 'URL Key',
                'input'        => 'text',
                'required'     => false,
                'user_defined' => true,
                'sort_order'   => 3,
                'global'       => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'backend'      => 'Smile\StoreLocator\Model\Retailer\Attribute\Backend\UrlKey',
            ]
        );

        $eavSetup->addAttributeToGroup($entityId, $attrSetId, $groupId, 'url_key', 35);
    }

    /**
     * Add contact information (phone, mail, etc..) attribute to Retailers
     *
     * @param \Magento\Eav\Setup\EavSetup $eavSetup EAV module Setup
     */
    public function addContactInformation($eavSetup)
    {
        $entityId  = SellerInterface::ENTITY;
        $attrSetId = RetailerInterface::ATTRIBUTE_SET_RETAILER;
        $groupId   = 'Contact';

        $eavSetup->addAttributeGroup($entityId, $attrSetId, $groupId, 150);

        $eavSetup->addAttribute(
            SellerInterface::ENTITY,
            'contact_phone',
            [
                'type'         => 'varchar',
                'label'        => 'Contact Phone number',
                'input'        => 'text',
                'required'     => false,
                'user_defined' => true,
                'sort_order'   => 10,
                'global'       => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            ]
        );

        $eavSetup->addAttribute(
            SellerInterface::ENTITY,
            'contact_fax',
            [
                'type'         => 'varchar',
                'label'        => 'Contact Fax number',
                'input'        => 'text',
                'required'     => false,
                'user_defined' => true,
                'sort_order'   => 20,
                'global'       => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            ]
        );

        $eavSetup->addAttribute(
            SellerInterface::ENTITY,
            'contact_mail',
            [
                'type'           => 'varchar',
                'label'          => 'Contact Mail',
                'input'          => 'email',
                'required'       => false,
                'user_defined'   => true,
                'sort_order'     => 30,
                'global'         => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'frontend_class' => 'validate-email',
            ]
        );

        $eavSetup->addAttribute(
            SellerInterface::ENTITY,
            'show_contact_form',
            [
                'type'         => 'int',
                'label'        => 'Show contact form',
                'input'        => 'boolean',
                'required'     => false,
                'user_defined' => true,
                'sort_order'   => 40,
                'global'       => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'source'       => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
            ]
        );

        $eavSetup->addAttributeToGroup($entityId, $attrSetId, $groupId, 'contact_phone', 10);
        $eavSetup->addAttributeToGroup($entityId, $attrSetId, $groupId, 'contact_fax', 20);
        $eavSetup->addAttributeToGroup($entityId, $attrSetId, $groupId, 'contact_mail', 30);
        $eavSetup->addAttributeToGroup($entityId, $attrSetId, $groupId, 'show_contact_form', 40);
    }
}
