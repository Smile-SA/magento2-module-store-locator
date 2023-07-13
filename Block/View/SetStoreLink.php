<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Block\View;

use Smile\StoreLocator\Block\AbstractView;

/**
 * Set store link block.
 */
class SetStoreLink extends AbstractView
{
    /**
     * Get the JSON post data used to build the set store link.
     */
    public function getSetStorePostJson(): string
    {
        $setUrl   = $this->_urlBuilder->getUrl('storelocator/store/set', ['_secure' => true]);
        $postData = ['id' => $this->getRetailer()->getId()];

        return json_encode(['action' => $setUrl, 'data' => $postData]);
    }
}
