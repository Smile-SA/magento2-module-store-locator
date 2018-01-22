<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\StoreLocator
 * @author    Romain Ruaud <romain.ruaud@smile.fr>
 * @author    Guillaume Vrac <guillaume.vrac@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\StoreLocator\Block;

use Magento\Framework\View\Element\Template;

/**
 * Retailer View Block
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 * @author   Guillaume Vrac <guillaume.vrac@smile.fr>
 */
class AbstractView extends Template
{
    /**
     * Constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context      Application context
     * @param \Magento\Framework\Registry                      $coreRegistry Application Registry
     * @param array                                            $data         Block Data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->coreRegistry       = $coreRegistry;
    }

    /**
     * Get the current shop.
     *
     * @return \Smile\Retailer\Api\Data\RetailerInterface
     */
    public function getRetailer()
    {
        return $this->coreRegistry->registry('current_retailer');
    }
}
