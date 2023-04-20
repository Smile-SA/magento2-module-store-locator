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
namespace Smile\StoreLocator\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Smile\Retailer\Api\Data\RetailerInterface;

/**
 * Contact information Helper
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
class Contact extends AbstractHelper
{
    /**
     * Check if a retailer has contact information.
     *
     * @param RetailerInterface $retailer The retailer
     *
     * @return bool
     */
    public function hasContactInformation($retailer): bool
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
     *
     * @param RetailerInterface $retailer The retailer
     *
     * @return bool
     */
    public function canDisplayContactForm(RetailerInterface $retailer): bool
    {
        return true === (bool) $retailer->getCustomAttribute('show_contact_form')->getValue();
    }

    /**
     * Retrieve contact form submit Url.
     *
     * @param RetailerInterface $retailer The retailer
     *
     * @return string
     */
    public function getContactFormUrl(RetailerInterface $retailer): string
    {
        return $this->_getUrl('storelocator/store/contact', ['id' => $retailer->getId(), '_secure' => true]);
    }
}
