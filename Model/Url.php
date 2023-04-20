<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\StoreLocator
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
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
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class Url
{
    /**
     * @var string
     */
    const BASE_URL_XML_PATH = 'store_locator/seo/base_url';

    /**
     * @var ResourceModelUrl
     */
    private ResourceModelUrl $resourceModel;

    /**
     * @var FilterManager
     */
    private FilterManager $filter;

    /**
     * @var UrlInterface
     */
    private UrlInterface $urlBuilder;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * Constructor.
     *
     * @param ResourceModelUrl      $resourceModel ResourceModel.
     * @param StoreManagerInterface $storeManager  Store manager.
     * @param ScopeConfigInterface  $scopeConfig   Store config.
     * @param UrlInterface          $urlBuilder    URL builder.
     * @param FilterManager         $filter        Filters.
     */
    public function __construct(
        ResourceModelUrl $resourceModel,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        UrlInterface $urlBuilder,
        FilterManager $filter
    ) {
        $this->resourceModel = $resourceModel;
        $this->storeManager  = $storeManager;
        $this->scopeConfig   = $scopeConfig;
        $this->urlBuilder    = $urlBuilder;
        $this->filter        = $filter;
    }

    /**
     * Get retailer URL key.
     *
     * @param RetailerInterface $retailer Retailer.
     *
     * @return ?string
     */
    public function getUrlKey(RetailerInterface $retailer): ?string
    {
        $urlKey = !empty($retailer->getUrlKey()) ? $retailer->getUrlKey() : $retailer->getName();

        return $urlKey !== null ? $this->filter->translitUrl($urlKey) : null;
    }

    /**
     * Get retailer URL.
     *
     * @param RetailerInterface $retailer Retailer.
     *
     * @return string
     */
    public function getUrl(RetailerInterface $retailer): string
    {
        $url = sprintf("%s/%s", $this->getRequestPathPrefix($retailer->getStoreId()), $this->getUrlKey($retailer));

        return $this->urlBuilder->getUrl(null, ['_direct' => $url]);
    }

    /**
     * Get store locator home URL.
     *
     * @param ?int $storeId Store Id
     *
     * @return string
     */
    public function getHomeUrl(?int $storeId = null): string
    {
        return $this->urlBuilder->getUrl(null, ['_direct' => $this->getRequestPathPrefix($storeId)]);
    }

    /**
     * Get URL prefix for the store locator.
     *
     * @param ?int $storeId Store Id
     *
     * @return string
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
     *
     * @param ?string   $urlKey  URL key.
     * @param ?int      $storeId Store Id.
     *
     * @return int|false
     */
    public function checkIdentifier(?string $urlKey, ?int $storeId = null): int|false
    {
        if ($storeId == null) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        return $this->resourceModel->checkIdentifier($urlKey, $storeId);
    }
}
