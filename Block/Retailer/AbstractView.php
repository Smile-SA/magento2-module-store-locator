<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade Smile Elastic Suite to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\LocalizedRetailer
 * @author    Romain Ruaud <romain.ruaud@smile.fr>
 * @author    Guillaume Vrac <guillaume.vrac@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\StoreLocator\Block\Retailer;

use Magento\Framework\Locale\ListsInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;


/**
 * Retailer View Block
 *
 * @category Smile
 * @package  Smile\LocalizedRetailer
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 * @author   Guillaume Vrac <guillaume.vrac@smile.fr>
 */
class AbstractView extends Template
{
    /**
     * @var StoreLocatorHelper
     */
    private $storeLocatorHelper;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context        Application context
     * @param \Magento\Framework\Registry                      $coreRegistry   Application Registry
     * @param array                                            $data           Block Data
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
     * @return \Smile\Retailer\Api\Data\RetailerInterface
     */
    public function getRetailer()
    {
        return $this->coreRegistry->registry('current_retailer');
    }
}
