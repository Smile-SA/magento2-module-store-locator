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
namespace Smile\StoreLocator\Block;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\Element\Template\Context;
use Smile\StoreLocator\Helper\Data;

/**
 * Retailer View Block
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author    Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class View extends AbstractView implements IdentityInterface
{
    /**
     * @var Data
     */
    private Data $storeLocatorHelper;

    /**
     * Constructor.
     *
     * @param Context   $context            Application context.
     * @param Registry  $coreRegistry       Application registry.
     * @param Data      $storeLocatorHelper Store locator helper.
     * @param array     $data               Block Data.
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        Data $storeLocatorHelper,
        array $data = []
    ) {
        parent::__construct($context, $coreRegistry, $data);
        $this->storeLocatorHelper = $storeLocatorHelper;
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return string[]
     */
    public function getIdentities(): array
    {
        $identities = [];
        if ($this->getRetailer()) {
            $identities = $this->getRetailer()->getIdentities();
        }

        return $identities;
    }

    /**
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     * {@inheritdoc}
     */
    protected function _prepareLayout(): AbstractBlock
    {
        parent::_prepareLayout();

        if ($this->getRetailer()) {
            $this->setPageTitle()
                ->setPageMeta()
                ->setBreadcrumbs();
        }

        return $this;
    }

    /**
     * Set the current page title.
     *
     * @return $this
     */
    private function setPageTitle(): self
    {
        $retailer = $this->getRetailer();

        /** @var BlockInterface $titleBlock */
        $titleBlock = $this->getLayout()->getBlock('page.main.title');

        if ($titleBlock) {
            $titleBlock->setPageTitle($retailer->getName());
        }

        $pageTitle = $retailer->getMetaTitle();
        if (empty($pageTitle)) {
            $pageTitle = $retailer->getName();
        }

        $this->pageConfig->getTitle()->set(__($pageTitle));

        return $this;
    }

    /**
     * Set the current page meta attributes (keywords, description).
     *
     * @return $this
     */
    private function setPageMeta(): self
    {
        $retailer = $this->getRetailer();

        $keywords = $retailer->getMetaKeywords();
        if ($keywords) {
            $this->pageConfig->setKeywords($retailer->getMetaKeywords());
        }

        // Set the page description.
        $description = $retailer->getMetaDescription();
        if ($description) {
            $this->pageConfig->setDescription($retailer->getMetaDescription());
        }

        return $this;
    }

    /**
     * Build breadcrumbs for the current page.
     *
     * @return $this
     */
    private function setBreadcrumbs(): self
    {
        if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
            $retailer            = $this->getRetailer();
            $homeUrl             = $this->_storeManager->getStore()->getBaseUrl();
            $storeLocatorHomeUrl = $this->storeLocatorHelper->getHomeUrl();

            $breadcrumbsBlock->addCrumb('home', ['label' => __('Home'), 'title' => __('Go to Home Page'), 'link' => $homeUrl]);
            $breadcrumbsBlock->addCrumb('search', ['label' => __('Our stores'), 'title' => __('Our stores'), 'link' => $storeLocatorHomeUrl]);
            $breadcrumbsBlock->addCrumb('store', ['label' => $retailer->getName(), 'title' => $retailer->getName()]);
        }

        return $this;
    }
}
