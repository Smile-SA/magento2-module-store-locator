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

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Smile\Seller\Api\Data\SellerInterface;
use Smile\Retailer\Api\Data\RetailerInterface;

/**
 * Store locator data setup.
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Constructor.
     *
     * @param EavSetupFactory $eavSetupFactory EAV Setup Factory.
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

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

        $eavSetup->addAttributeToGroup($entityId, $attrSetId, $groupId, 'url_key', 3);
    }
}
