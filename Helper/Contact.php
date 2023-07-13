<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Smile\Retailer\Api\Data\RetailerInterface;

/**
 * Contact information Helper.
 */
class Contact extends AbstractHelper
{
    /**
     * Check if a retailer has contact information.
     */
    public function hasContactInformation(RetailerInterface $retailer): bool
    {
        return (($retailer->getCustomAttribute('contact_mail')
                && $retailer->getCustomAttribute('contact_mail')->getValue())
            || ($retailer->getCustomAttribute('contact_phone')
                && $retailer->getCustomAttribute('contact_phone')->getValue())
            || ($retailer->getCustomAttribute('contact_fax')
                && $retailer->getCustomAttribute('contact_fax')->getValue())
        );
    }

    /**
     * Check if a retailer can display contact form.
     */
    public function canDisplayContactForm(RetailerInterface $retailer): bool
    {
        return true === (bool) $retailer->getCustomAttribute('show_contact_form')->getValue();
    }

    /**
     * Retrieve contact form submit Url.
     */
    public function getContactFormUrl(RetailerInterface $retailer): string
    {
        return $this->_getUrl('storelocator/store/contact', ['id' => $retailer->getId(), '_secure' => true]);
    }
}
