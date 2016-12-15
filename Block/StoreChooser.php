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

namespace Smile\StoreLocator\Block;

use Magento\Framework\View\Element\Template;

/**
 * Store chooser block.
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class StoreChooser extends Template
{
    /**
     * @var \Smile\StoreLocator\Helper\Data
     */
    private $storeLocatorHelper;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context            Template context.
     * @param \Smile\StoreLocator\Helper\Data                  $storeLocatorHelper Store locator helper.
     * @param array                                            $data               Additional data.
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Smile\StoreLocator\Helper\Data $storeLocatorHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->storeLocatorHelper = $storeLocatorHelper;
    }

    /**
     * Get store locator home URL.
     *
     * @return string
     */
    public function getStoreLocatorHomeUrl()
    {
        return $this->storeLocatorHelper->getHomeUrl();
    }
}
