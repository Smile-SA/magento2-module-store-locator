<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\StoreLocator
 * @author    Aurelien FOUCRET <aurelien.foucret@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\StoreLocator\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Smile\StoreLocator\Setup\StoreLocatorSetupFactory;

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
     * @var \Smile\StoreLocator\Setup\StoreLocatorSetup
     */
    private $storeLocatorSetup;

    /**
     * Constructor.
     *
     * @param EavSetupFactory          $eavSetupFactory          EAV Setup Factory.
     * @param StoreLocatorSetupFactory $storeLocatorSetupFactory The Store Locator Setup Factory
     */
    public function __construct(EavSetupFactory $eavSetupFactory, StoreLocatorSetupFactory $storeLocatorSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->storeLocatorSetup = $storeLocatorSetupFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $this->storeLocatorSetup->addUrlKeyAttribute($eavSetup);
        $this->storeLocatorSetup->addContactInformation($eavSetup);
    }
}
