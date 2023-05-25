<?php

namespace Smile\StoreLocator\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Validator\ValidateException;

/**
 * Data Upgrade for Store Locator.
 */
class UpgradeData implements UpgradeDataInterface
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
     * @throws LocalizedException
     * @throws ValidateException
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        if (version_compare($context->getVersion(), '1.2.0', '<')) {
            $this->storeLocatorSetup->addContactInformation($eavSetup);
        }

        if (version_compare($context->getVersion(), '1.2.1', '<')) {
            $this->storeLocatorSetup->setContactFormRequired($eavSetup);
        }

        if (version_compare($context->getVersion(), '2.0.0', '<')) {
            $this->storeLocatorSetup->addImage($eavSetup);
        }
    }
}
