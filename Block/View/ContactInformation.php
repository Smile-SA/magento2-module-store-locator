<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Block\View;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Smile\StoreLocator\Block\AbstractView;
use Smile\StoreLocator\Helper\Contact;

/**
 * Contact Information block for StoreLocator page.
 */
class ContactInformation extends AbstractView
{
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        private Contact $contactHelper,
        array $data
    ) {
        parent::__construct($context, $coreRegistry, $data);
    }

    /**
     * Check if current retailer has contact information.
     */
    public function hasContactInformation(): bool
    {
        return $this->contactHelper->hasContactInformation($this->getRetailer());
    }

    /**
     * Check if we can display contact form for current retailer.
     */
    public function showContactForm(): bool
    {
        return $this->contactHelper->canDisplayContactForm($this->getRetailer());
    }

    /**
     * Retrieve Contact form Url for current retailer.
     */
    public function getContactFormUrl(): string
    {
        return $this->contactHelper->getContactFormUrl($this->getRetailer());
    }
}
