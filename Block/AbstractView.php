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

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Smile\Retailer\Api\Data\RetailerInterface;
use Smile\Seller\Api\Data\SellerInterface;

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
     * @param Context   $context      Application context
     * @param Registry  $coreRegistry Application Registry
     * @param array     $data         Block Data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->coreRegistry       = $coreRegistry;
    }

    /**
     * Get the current shop.
     *
     * @return ?RetailerInterface
     */
    public function getRetailer(): ?RetailerInterface
    {
        return $this->coreRegistry->registry('current_retailer');
    }
}
