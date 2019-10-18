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

use Smile\Retailer\Api\Data\RetailerInterface;
use Magento\Store\Model\ScopeInterface;

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
     * @var \Smile\StoreLocator\Model\ResourceModel\Url
     */
    private $resourceModel;

    /**
     * @var \Magento\Framework\Filter\FilterManager
     */
    private $filter;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Constructor.
     *
     * @param \Smile\StoreLocator\Model\ResourceModel\Url        $resourceModel ResourceModel.
     * @param \Magento\Store\Model\StoreManagerInterface         $storeManager  Store manager.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig   Store config.
     * @param \Magento\Framework\UrlInterface                    $urlBuilder    URL builder.
     * @param \Magento\Framework\Filter\FilterManager            $filter        Filters.
     */
    public function __construct(
        \Smile\StoreLocator\Model\ResourceModel\Url $resourceModel,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Filter\FilterManager $filter
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
     * @return string
     */
    public function getUrlKey(RetailerInterface $retailer)
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
    public function getUrl(RetailerInterface $retailer)
    {
        $url = sprintf("%s/%s", $this->getRequestPathPrefix($retailer->getStoreId()), $this->getUrlKey($retailer));

        return $this->urlBuilder->getUrl(null, ['_direct' => $url]);
    }

    /**
     * Get store locator home URL.
     *
     * @param int|NULL $storeId Store Id
     *
     * @return string
     */
    public function getHomeUrl($storeId = null)
    {
        return $this->urlBuilder->getUrl(null, ['_direct' => $this->getRequestPathPrefix($storeId)]);
    }

    /**
     * Get URL prefix for the store locator.
     *
     * @param int|NULL $storeId Store Id
     *
     * @return string
     */
    public function getRequestPathPrefix($storeId = null)
    {
        if ($storeId === null) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        return $this->scopeConfig->getValue(self::BASE_URL_XML_PATH, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Check an URL key exists and returns the retailer id. False if no retailer found.
     *
     * @param urlKey $urlKey  URL key.
     * @param int    $storeId Store Id.
     *
     * @return int|false
     */
    public function checkIdentifier($urlKey, $storeId = null)
    {
        if ($storeId == null) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        return $this->resourceModel->checkIdentifier($urlKey, $storeId);
    }
}
