<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Block;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\Store;
use Magento\Theme\Block\Html\Breadcrumbs;
use Smile\Retailer\Model\Retailer;
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
            /** @var Retailer $retailer */
            $retailer = $this->getRetailer();
            $identities = $retailer->getIdentities();
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

        /** @var AbstractBlock|bool $titleBlock */
        $titleBlock = $this->getLayout()->getBlock('page.main.title');

        if ($titleBlock) {
            $titleBlock->setPageTitle($retailer->getName());
        }

        $pageTitle = $retailer->getData('meta_title');
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

        $keywords = $retailer->getData('meta_keywords');
        if ($keywords) {
            $this->pageConfig->setKeywords($retailer->getData('meta_keywords'));
        }

        // Set the page description.
        $description = $retailer->getData('meta_description');
        if ($description) {
            $this->pageConfig->setDescription($retailer->getData('meta_description'));
        }

        return $this;
    }

    /**
     * Build breadcrumbs for the current page.
     */
    private function setBreadcrumbs(): self
    {
        /** @var Breadcrumbs|bool $breadcrumbsBlock */
        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbsBlock) {
            $retailer = $this->getRetailer();
            /** @var Store $currentStore */
            $currentStore = $this->_storeManager->getStore();
            $homeUrl = $currentStore->getBaseUrl();
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
