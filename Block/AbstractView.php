<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Block;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Smile\Retailer\Api\Data\RetailerInterface;

/**
 * Retailer View Block.
 */
class AbstractView extends Template
{
    public function __construct(
        Context $context,
        protected Registry $coreRegistry,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Get the current shop.
     */
    public function getRetailer(): ?RetailerInterface
    {
        return $this->coreRegistry->registry('current_retailer');
    }
}
