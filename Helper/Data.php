<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\StoreLocator
 * @author   Aurelien FOUCRET <aurelien.foucret@gmail.com>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\StoreLocator\Helper;

use Magento\Store\Model\ScopeInterface;

/**
 * Store locator helper.
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Aurelien FOUCRET <aurelien.foucret@gmail.com>
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const BASE_URL_XML_PATH = 'store_locator/seo/base_url';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\App\Helper\Context      $context      Helper context.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager Store manager.
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        parent::__construct($context);

        $this->storeManager = $storeManager;
    }

    public function getBaseUrlPrefix()
    {
        $storeId = $this->storeManager->getStore()->getId();

        return $this->scopeConfig->getValue(self::BASE_URL_XML_PATH, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getBaseUrl()
    {
        return $this->_urlBuilder->getUrl(null, ['_direct' => $this->getBaseUrlPrefix()]);
    }

    public function getStoreUrl($store)
    {
        $url = sprintf("%s/%s", $this->getBaseUrlPrefix(), "test");

        return $this->_urlBuilder->getUrl(null, ['_direct' => $url]);
    }
}
