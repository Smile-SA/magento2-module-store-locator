<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Smile\StoreLocator\Setup\Patch\StoreLocatorSetup;
use Smile\StoreLocator\Setup\Patch\StoreLocatorSetupFactory;

/**
 * Class default groups and attributes for customer
 */
class DefaultStoreLocatorAttributes implements DataPatchInterface, PatchVersionInterface
{
    public function __construct(
        private readonly StoreLocatorSetupFactory $storeLocatorSetupFactory,
        private readonly EavSetupFactory   $eavSetupFactory,
        private readonly ModuleDataSetupInterface $moduleDataSetup
    ) {
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function apply(): self
    {
        $this->moduleDataSetup->startSetup();

        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        /** @var StoreLocatorSetup $storeLocatorSetup */
        $storeLocatorSetup = $this->storeLocatorSetupFactory->create();

        $storeLocatorSetup->addUrlKeyAttribute($eavSetup);
        $storeLocatorSetup->addContactInformation($eavSetup);
        $storeLocatorSetup->setContactFormRequired($eavSetup);
        $storeLocatorSetup->addImage($eavSetup);

        $this->moduleDataSetup->endSetup();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getVersion(): string
    {
        return '2.1.1';
    }

    /**
     * @inheritdoc
     */
    public function getAliases(): array
    {
        return [];
    }
}
