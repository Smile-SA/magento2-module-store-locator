<?php

namespace Smile\StoreLocator\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Upgrade Schema for StoreLocator Module.
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    private StoreLocatorSetup $storeLocatorSetup;

    public function __construct(StoreLocatorSetupFactory $storeLocatorSetupFactory)
    {
        $this->storeLocatorSetup = $storeLocatorSetupFactory->create();
    }

    /**
     * @inheritdoc
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->storeLocatorSetup->createOpeningHoursTable($setup);
        }
        if (version_compare($context->getVersion(), '1.2.2', '<')) {
            $this->storeLocatorSetup->updateDecimalDegreesColumns($setup);
        }

        $setup->endSetup();
    }
}
