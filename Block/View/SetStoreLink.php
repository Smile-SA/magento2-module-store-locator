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
namespace Smile\StoreLocator\Block\View;

use Smile\StoreLocator\Block\AbstractView;

/**
 * Set store link block.
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class SetStoreLink extends AbstractView
{
    /**
     * Get the JSON post data used to build the set store link.
     *
     * @return string
     */
    public function getSetStorePostJson()
    {
        $setUrl   = $this->_urlBuilder->getUrl('storelocator/store/set', ['_secure' => true]);
        $postData = ['id' => $this->getRetailer()->getId()];

        return json_encode(['action' => $setUrl, 'data' => $postData]);
    }
}
