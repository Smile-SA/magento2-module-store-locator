<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Block;

use Magento\Customer\Helper\View as CustomerHelperView;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\Store;
use Magento\Theme\Block\Html\Breadcrumbs;
use Smile\Map\Model\AddressFormatter;
use Smile\StoreLocator\Api\Data\RetailerAddressInterface;
use Smile\StoreLocator\Helper\Data as StoreLocatorHelper;

/**
 * Store Locator Contact Form.
 */
class ContactForm extends AbstractView
{
    private ?array $postData = null;

    private DataPersistorInterface $dataPersistor;

    public function __construct(
        Context $context,
        Registry $coreRegistry,
        private StoreLocatorHelper $storeLocatorHelper,
        protected Session $customerSession,
        protected CustomerHelperView $customerViewHelper,
        DataPersistorInterface $dataPersistorInterface,
        private AddressFormatter $addressFormatter,
        array $data
    ) {
        $this->dataPersistor = $dataPersistorInterface;
        parent::__construct($context, $coreRegistry, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Return form action url.
     */
    public function getFormAction(): string
    {
        return $this->getUrl('storelocator/store/contactPost', ['_secure' => true]);
    }

    /**
     * Get value from POST by key.
     */
    public function getPostValue(string $key): string
    {
        if (null === $this->postData) {
            $this->postData = (array) $this->dataPersistor->get('contact_store');
            $this->dataPersistor->clear('contact_store');
        }

        if (isset($this->postData[$key])) {
            return (string) $this->postData[$key];
        }

        return '';
    }

    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->setPageTitle()
            ->setBreadcrumbs();

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
            $titleBlock->setPageTitle(__("Contact %1", $retailer->getName()));
        }

        $this->pageConfig->getTitle()->set(__("Contact %1", $retailer->getName()));

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
            $storeUrl = $this->storeLocatorHelper->getRetailerUrl($retailer);

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
                ['label' => $retailer->getName(), 'title' => $retailer->getName(), 'link' => $storeUrl]
            );

            $breadcrumbsBlock->addCrumb(
                'contact',
                ['label' => __('Contact'), 'title' => __('Contact')]
            );
        }

        return $this;
    }

    /**
     * Get customer user name if logged in
     */
    public function getUserName(): string
    {
        if (!$this->customerSession->isLoggedIn()) {
            return '';
        }
        $customer = $this->customerSession->getCustomerDataObject();

        return trim($this->customerViewHelper->getCustomerName($customer));
    }

    /**
     * Get customer user email if logged in
     */
    public function getUserEmail(): string
    {
        if (!$this->customerSession->isLoggedIn()) {
            return '';
        }
        $customer = $this->customerSession->getCustomerDataObject();

        return $customer->getEmail();
    }

    /**
     * Returns current store address.
     */
    public function getAddress(): ?RetailerAddressInterface
    {
        return $this->getRetailer()->getData('address');
    }

    /**
     * Get address formatted in HTML.
     */
    public function getAddressHtml(): string
    {
        return $this->getAddress()
            ? $this->addressFormatter->formatAddress($this->getAddress(), AddressFormatter::FORMAT_HTML)
            : '';
    }
}
