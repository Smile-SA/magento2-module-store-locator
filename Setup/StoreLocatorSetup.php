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
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Eav\Setup\EavSetup;

use Smile\Retailer\Api\Data\RetailerInterface;
use Smile\Seller\Api\Data\SellerInterface;
use Smile\StoreLocator\Model\Retailer\Attribute\Backend\UrlKey;

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
     * @throws \Zend_Db_Exception
     *
     * @return StoreLocatorSetup
     */
    public function createRetailerAddressTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable('smile_retailer_address'))
            ->addColumn(
                'address_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Address ID'
            )
            ->addColumn(
                'retailer_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Retailer Id'
            )
            ->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => Table::TIMESTAMP_INIT
                ],
                'Created At'
            )
            ->addColumn(
                'updated_at',
                Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => Table::TIMESTAMP_INIT_UPDATE
                ],
                'Updated At'
            )
            ->addColumn(
                'street',
                Table::TYPE_TEXT,
                null,
                [
                    'nullable' => false
                ],
                'Street Address'
            )
            ->addColumn(
                'postcode',
                Table::TYPE_TEXT,
                255,
                [
                    'nullable' => true,
                    'default' => null
                ],
                'Zip/Postal Code'
            )
            ->addColumn(
                'city',
                Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false
                ],
                'City'
            )
            ->addColumn(
                'region',
                Table::TYPE_TEXT,
                255,
                [
                    'nullable' => true,
                    'default' => null
                ],
                'State/Province'
            )
            ->addColumn(
                'region_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => true,
                    'default' => null
                ],
                'State/Province'
            )
            ->addColumn(
                'country_id',
                Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false
                ],
                'Country'
            )
            ->addColumn(
                'latitude',
                Table::TYPE_FLOAT,
                null,
                [
                    'nullable' => false
                ],
                'Latitude'
            )
            ->addColumn(
                'longitude',
                Table::TYPE_FLOAT,
                null,
                [
                    'nullable' => false
                ],
                'Longitude'
            )
            ->addIndex(
                $setup->getIdxName('smile_retailer_address', ['retailer_id']),
                ['retailer_id'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            )
            ->addForeignKey(
                $setup->getFkName(
                    'smile_retailer_address',
                    'retailer_id',
                    'smile_seller_entity',
                    'entity_id'
                ),
                'retailer_id',
                $setup->getTable('smile_seller_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )->setComment('Retailer Address');

        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Update latitude and longitude column type.
     *
     * @param SchemaSetupInterface $setup Schema setup.
     *
     * @return $this
     */
    public function updateDecimalDegreesColumns(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->modifyColumn(
            $setup->getTable('smile_retailer_address'),
            'latitude',
            [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,6',
                'nullable' => false,
            ]
        );
        $setup->getConnection()->modifyColumn(
            $setup->getTable('smile_retailer_address'),
            'longitude',
            [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,6',
                'nullable' => false,
            ]
        );

        return $this;
    }

    /**
     * Create Opening Hours main table
     *
     * @throws \Zend_Db_Exception
     * @param  SchemaSetupInterface $setup Setup instance
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
                /**
                 * Hack : Magento does not support TIME column on its DDL.
                 * This column will contain full datetime but work only with hours.
                 */
                Table::TYPE_DATETIME,
                null,
                [
                    'nullable' => true,
                    'default' => null
                ],
                'Start Time'
            )->addColumn(
                "end_time",
                /**
                 * Hack : Magento does not support TIME column on its DDL.
                 * This column will contain full datetime but work only with hours.
                 */
                Table::TYPE_DATETIME,
                null,
                [
                    'nullable' => true,
                    'default' => null
                ],
                'End Time'
            )->addForeignKey(
                $setup->getFkName(
                    'smile_retailer_time_slots',
                    'retailer_id',
                    'smile_seller_entity',
                    'entity_id'
                ),
                'retailer_id',
                $setup->getTable('smile_seller_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Smile Retailer Opening Hours Table');
        $setup->getConnection()->createTable($table);
    }

    /**
     * Add 'url_key' attribute to Retailers
     *
     * @param EavSetup $eavSetup EAV module Setup
     *
     * @throws LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function addUrlKeyAttribute(EavSetup $eavSetup)
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
                'global'       => ScopedAttributeInterface::SCOPE_STORE,
                'backend'      => UrlKey::class,
            ]
        );

        $eavSetup->addAttributeToGroup($entityId, $attrSetId, $groupId, 'url_key', 35);
    }

    /**
     * Add contact information (phone, mail, etc..) attribute to Retailers
     *
     * @param EavSetup $eavSetup EAV module Setup
     *
     * @throws LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function addContactInformation(EavSetup $eavSetup)
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
                'global'       => ScopedAttributeInterface::SCOPE_GLOBAL,
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
                'global'       => ScopedAttributeInterface::SCOPE_GLOBAL,
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
                'global'         => ScopedAttributeInterface::SCOPE_GLOBAL,
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
                'required'     => true,
                'user_defined' => true,
                'sort_order'   => 40,
                'global'       => ScopedAttributeInterface::SCOPE_GLOBAL,
                'source'       => Boolean::class,
            ]
        );

        $eavSetup->addAttributeToGroup($entityId, $attrSetId, $groupId, 'contact_phone', 10);
        $eavSetup->addAttributeToGroup($entityId, $attrSetId, $groupId, 'contact_fax', 20);
        $eavSetup->addAttributeToGroup($entityId, $attrSetId, $groupId, 'contact_mail', 30);
        $eavSetup->addAttributeToGroup($entityId, $attrSetId, $groupId, 'show_contact_form', 40);
    }

    /**
     * Update show_contact_form to Required.
     *
     * @param EavSetup $eavSetup EAV module Setup
     */
    public function setContactFormRequired(EavSetup $eavSetup)
    {
        $eavSetup->updateAttribute(
            SellerInterface::ENTITY,
            'show_contact_form',
            'is_required',
            1
        );
    }


    /**
     * Add image attribute to Retailers
     *
     * @param \Magento\Eav\Setup\EavSetup $eavSetup EAV module Setup
     */
    public function addImage($eavSetup)
    {
        $entityId  = SellerInterface::ENTITY;
        $attrSetId = RetailerInterface::ATTRIBUTE_SET_RETAILER;
        $groupId   = 'General';

        $eavSetup->addAttribute(
            SellerInterface::ENTITY,
            'image',
            [
                'type'         => 'varchar',
                'label'        => 'Media',
                'input'        => 'image',
                'required'     => false,
                'user_defined' => true,
                'sort_order'   => 17,
                'backend_model' => 'Magento\Catalog\Model\Category\Attribute\Backend\Image',
                'global'       => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            ]
        );

        $eavSetup->addAttributeToGroup($entityId, $attrSetId, $groupId, 'image', 50);
    }
}
