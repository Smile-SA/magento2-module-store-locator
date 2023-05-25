<?php

namespace Smile\StoreLocator\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Store locator schema setup.
 */
class InstallSchema implements InstallSchemaInterface
{
    private StoreLocatorSetup $storeLocatorSetup;

    public function __construct(StoreLocatorSetupFactory $storeLocatorSetupFactory)
    {
        $this->storeLocatorSetup = $storeLocatorSetupFactory->create();
    }

    /**
     * @inheritdoc
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $this->storeLocatorSetup->createRetailerAddressTable($setup);
        $this->storeLocatorSetup->createOpeningHoursTable($setup);
        $setup->endSetup();
    }
}
