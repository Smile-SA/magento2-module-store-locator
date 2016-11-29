<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\StoreLocator
 * @author    Romain Ruaud <romain.ruaud@smile.fr>
 * @author    Guillaume Vrac <guillaume.vrac@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Smile\StoreLocator\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Smile\Retailer\Api\Data\RetailerInterface;

/**
 * Installer for Store Locator Data.
 * Basically adds attributes to Retailer entity.
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 * @author   Guillaume Vrac <guillaume.vrac@smile.fr>
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var StoreLocatorSetupFactory
     */
    private $storeLocatorSetupFactory;

    /**
     * InstallData constructor
     *
     * @param StoreLocatorSetupFactory $storeLocatorSetupFactory The Store Locator Setup factory
     */
    public function __construct(StoreLocatorSetupFactory $storeLocatorSetupFactory)
    {
        $this->storeLocatorSetupFactory = $storeLocatorSetupFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /** @var storeLocatorSetup $storeLocatorSetup */
        $storeLocatorSetup = $this->storeLocatorSetupFactory->create(['setup' => $setup]);
        $storeLocatorSetup->installEntities();

        $this->installlStoreLocatorAttributesAttributes($storeLocatorSetup);

        $setup->endSetup();
    }


    /**
     * Initialize the Store locator attributes.
     *
     * @param StoreLocatorSetup $setup The Retailer Setup
     */
    protected function installlStoreLocatorAttributesAttributes(StoreLocatorSetup $setup)
    {
        $attributeSetsDefinition = $setup->getAttributeSetDefinition();
        $groupsDefinition        = $setup->getGroupsDefinition();

        foreach ($attributeSetsDefinition as $attributeSetName => $groups) {
            $setup->addAttributeSet(RetailerInterface::ENTITY, $attributeSetName);

            foreach ($groups as $groupName => $attributes) {
                $sortOrder = $groupsDefinition[$groupName];
                $setup->addAttributeGroup(RetailerInterface::ENTITY, $attributeSetName, $groupName, $sortOrder);

                foreach ($attributes as $key => $attributeCode) {
                    $sortOrder = ($key + 1 ) * 10;
                    $setup->addAttributeToGroup(
                        RetailerInterface::ENTITY,
                        $attributeSetName,
                        $groupName,
                        $attributeCode,
                        $sortOrder
                    );
                }
            }
        }
    }
}
