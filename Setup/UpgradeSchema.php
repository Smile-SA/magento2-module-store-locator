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

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

use Smile\StoreLocator\Setup\StoreLocatorSetup;
use Smile\StoreLocator\Setup\StoreLocatorSetupFactory;

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
     * @var StoreLocatorSetup
     */
    private StoreLocatorSetup $storeLocatorSetup;

    /**
     * InstallSchema constructor.
     *
     * @param StoreLocatorSetupFactory $storeLocatorSetupFactory The Store Locator Setup Factory
     */
    public function __construct(StoreLocatorSetupFactory $storeLocatorSetupFactory)
    {
        $this->storeLocatorSetup = $storeLocatorSetupFactory->create();
    }

    /**
     * Installs DB schema for a module
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param SchemaSetupInterface   $setup   Setup
     * @param ModuleContextInterface $context Context
     *
     * @throws \Zend_Db_Exception
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context): void
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
