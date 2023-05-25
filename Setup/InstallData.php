<?php

namespace Smile\StoreLocator\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Smile\StoreLocator\Setup\StoreLocatorSetupFactory;

/**
 * Store locator data setup.
 */
class InstallData implements InstallDataInterface
{
    private StoreLocatorSetup $storeLocatorSetup;

    public function __construct(
        private EavSetupFactory $eavSetupFactory,
        StoreLocatorSetupFactory $storeLocatorSetupFactory
    ) {
        $this->storeLocatorSetup = $storeLocatorSetupFactory->create();
    }

    /**
     * @inheritdoc
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $this->storeLocatorSetup->addUrlKeyAttribute($eavSetup);
        $this->storeLocatorSetup->addContactInformation($eavSetup);
        $this->storeLocatorSetup->addImage($eavSetup);
    }
}
