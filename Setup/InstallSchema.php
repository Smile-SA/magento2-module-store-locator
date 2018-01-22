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

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Smile\StoreLocator\Setup\StoreLocatorSetupFactory;

/**
 * Store locator schema setup.
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var \Smile\StoreLocator\Setup\StoreLocatorSetup
     */
    private $storeLocatorSetup;

    /**
     * InstallSchema constructor.
     *
     * @param \Smile\StoreLocator\Setup\StoreLocatorSetupFactory $storeLocatorSetupFactory The Store Locator Setup Factory
     */
    public function __construct(StoreLocatorSetupFactory $storeLocatorSetupFactory)
    {
        $this->storeLocatorSetup = $storeLocatorSetupFactory->create();
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $this->storeLocatorSetup->createRetailerAddressTable($setup);
        $this->storeLocatorSetup->createOpeningHoursTable($setup);
        $setup->endSetup();
    }
}
