<?php

namespace Smile\StoreLocator\Block;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\Element\Template\Context;
use Smile\StoreLocator\Helper\Data;

/**
 * Retailer View Block.
 */
class View extends AbstractView implements IdentityInterface
{
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        private Data $storeLocatorHelper,
        array $data = []
    ) {
        parent::__construct($context, $coreRegistry, $data);
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    protected function _prepareLayout()
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
     */
    private function setBreadcrumbs(): self
    {
        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbsBlock) {
            $retailer = $this->getRetailer();
            $homeUrl = $this->_storeManager->getStore()->getBaseUrl();
            $storeLocatorHomeUrl = $this->storeLocatorHelper->getHomeUrl();

            $breadcrumbsBlock->addCrumb(
                'home',
                ['label' => __('Home'), 'title' => __('Go to Home Page'), 'link' => $homeUrl]
            );
            $breadcrumbsBlock->addCrumb(
                'search',
                ['label' => __('Our stores'), 'title' => __('Our stores'), 'link' => $storeLocatorHomeUrl]
            );
            $breadcrumbsBlock->addCrumb(
                'store',
                ['label' => $retailer->getName(), 'title' => $retailer->getName()]
            );
        }

        return $this;
    }
}
