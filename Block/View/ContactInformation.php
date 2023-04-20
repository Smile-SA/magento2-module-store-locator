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
namespace Smile\StoreLocator\Block\View;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Smile\StoreLocator\Block\AbstractView;
use Smile\StoreLocator\Helper\Contact;

/**
 * Contact Information block for StoreLocator page
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
class ContactInformation extends AbstractView
{
    /**
     * @var Contact
     */
    private Contact $contactHelper;

    /**
     * ContactInformation constructor.
     *
     * @param Context  $context       Application Context
     * @param Registry $coreRegistry  Core Registry
     * @param Contact  $contactHelper Contact Helper
     * @param array    $data          Block data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        Contact $contactHelper,
        array $data
    ) {
        $this->contactHelper = $contactHelper;
        parent::__construct($context, $coreRegistry, $data);
    }

    /**
     * Check if current retailer has contact information.
     *
     * @return bool
     */
    public function hasContactInformation(): bool
    {
        return $this->contactHelper->hasContactInformation($this->getRetailer());
    }

    /**
     * Check if we can display contact form for current retailer.
     *
     * @return bool
     */
    public function showContactForm(): bool
    {
        return $this->contactHelper->canDisplayContactForm($this->getRetailer());
    }

    /**
     * Retrieve Contact form Url for current retailer
     *
     * @return string
     */
    public function getContactFormUrl(): string
    {
        return $this->contactHelper->getContactFormUrl($this->getRetailer());
    }
}
