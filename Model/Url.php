<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Filter\FilterManager;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Smile\Retailer\Api\Data\RetailerInterface;
use Smile\StoreLocator\Model\ResourceModel\Url as ResourceModelUrl;

/**
 * Retailer URL model.
 */
class Url
{
    private const BASE_URL_XML_PATH = 'store_locator/seo/base_url';

    public function __construct(
        private ResourceModelUrl $resourceModel,
        private StoreManagerInterface $storeManager,
        private ScopeConfigInterface $scopeConfig,
        private UrlInterface $urlBuilder,
        private FilterManager $filter
    ) {
    }

    /**
     * Get retailer URL key.
     */
    public function getUrlKey(RetailerInterface $retailer): ?string
    {
        $urlKey = $retailer->getData('url_key') ?: $retailer->getName();

        return $this->filter->translitUrl($urlKey) ?: null;
    }

    /**
     * Get retailer URL.
     */
    public function getUrl(RetailerInterface $retailer): string
    {
        $url = sprintf(
            "%s/%s",
            $this->getRequestPathPrefix($retailer->getData('store_id')),
            $this->getUrlKey($retailer)
        );

        return $this->urlBuilder->getUrl(null, ['_direct' => $url]);
    }

    /**
     * Get store locator home URL.
     */
    public function getHomeUrl(?int $storeId = null): string
    {
        return $this->urlBuilder->getUrl(null, ['_direct' => $this->getRequestPathPrefix($storeId)]);
    }

    /**
     * Get URL prefix for the store locator.
     */
    public function getRequestPathPrefix(?int $storeId = null): string
    {
        if ($storeId === null) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        return $this->scopeConfig->getValue(self::BASE_URL_XML_PATH, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Check an URL key exists and returns the retailer id. False if no retailer found.
     */
    public function checkIdentifier(string $urlKey, ?int $storeId = null): int
    {
        if ($storeId == null) {
            $storeId = (int) $this->storeManager->getStore()->getId();
        }

        return $this->resourceModel->checkIdentifier($urlKey, $storeId);
    }
}
