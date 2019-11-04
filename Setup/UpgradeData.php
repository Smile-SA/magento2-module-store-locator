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

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Eav\Setup\EavSetupFactory;

/**
 * Data Upgrade for Store Locator
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
class UpgradeData implements UpgradeDataInterface
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
     * @throws LocalizedException
     * @throws \Zend_Validate_Exception
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
