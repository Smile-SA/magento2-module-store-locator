<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Smile\Retailer\Api\Data\RetailerInterface;
use Smile\StoreLocator\Model\Url;

/**
 * Store locator helper.
 */
class Data extends AbstractHelper
{
    public const SEARCH_PLACEHOLDER_XML_PATH = 'store_locator/search/placeholder';

    public function __construct(
        Context $context,
        private Url $urlModel
    ) {
        parent::__construct($context);
    }

    /**
     * Get config by config path.
     */
    public function getConfigByPath(string $path, string $scope = ScopeInterface::SCOPE_STORE): mixed
    {
        return $this->scopeConfig->getValue($path, $scope);
    }

    /**
     * Get placeholder for search input of store_locator, default: City, Zipcode, Address, ...
     */
    public function getSearchPlaceholder(): string
    {
        return (string) $this->getConfigByPath(self::SEARCH_PLACEHOLDER_XML_PATH) ?: 'City, Zipcode, Address ...';
    }

    /**
     * Store locator home URL.
     */
    public function getHomeUrl(?int $storeId = null): string
    {
        return $this->urlModel->getHomeUrl($storeId);
    }

    /**
     * Retailer URL.
     */
    public function getRetailerUrl(RetailerInterface $retailer): string
    {
        return $this->urlModel->getUrl($retailer);
    }
}
