<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\StoreLocator
 * @author    Romain Ruaud <romain.ruaud@smile.fr>
 * @copyright 2017 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\StoreLocator\Block;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Template\Context;
use \Smile\StoreLocator\Helper\Data as StoreLocatorHelper;

/**
 * Store Locator Contact Form
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
class ContactForm extends AbstractView
{
    /**
     * @var StoreLocatorHelper
     */
    private StoreLocatorHelper $storeLocatorHelper;

    /**
     * @var ?array
     */
    private ?array $postData = null;

    /**
     * @var DataPersistorInterface
     */
    private DataPersistorInterface $dataPersistor;

    /**
     * ContactForm constructor.
     *
     * @param Context                $context                Application Context
     * @param Registry               $coreRegistry           Core Registry
     * @param StoreLocatorHelper     $storeLocatorHelper     Store Locator Helper
     * @param DataPersistorInterface $dataPersistorInterface Data Persistor Interface
     * @param array                  $data                   Block data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        StoreLocatorHelper $storeLocatorHelper,
        DataPersistorInterface $dataPersistorInterface,
        array $data
    ) {
        $this->storeLocatorHelper = $storeLocatorHelper;
        $this->dataPersistor      = $dataPersistorInterface;
        parent::__construct($context, $coreRegistry, $data);
        $this->_isScopePrivate    = true;
    }

    /**
     * Return form action url
     *
     * @return string
     */
    public function getFormAction(): string
    {
        return $this->getUrl('storelocator/store/contactPost', ['_secure' => true]);
    }

    /**
     * Get value from POST by key
     *
     * @param string $key The key
     *
     * @return string
     */
    public function getPostValue($key): string
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
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     * {@inheritdoc}
     */
    protected function _prepareLayout(): AbstractBlock
    {
        parent::_prepareLayout();

        $this->setPageTitle();
        $this->setBreadcrumbs();

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

        $titleBlock = $this->getLayout()->getBlock('page.main.title');

        if ($titleBlock) {
            $titleBlock->setPageTitle(__("Contact %1", $retailer->getName()));
        }

        $this->pageConfig->getTitle()->set(__("Contact %1", $retailer->getName()));

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
            $storeUrl            = $this->storeLocatorHelper->getRetailerUrl($retailer);

            $breadcrumbsBlock->addCrumb('home', ['label' => __('Home'), 'title' => __('Go to Home Page'), 'link' => $homeUrl]);
            $breadcrumbsBlock->addCrumb('search', ['label' => __('Our stores'), 'title' => __('Our stores'), 'link' => $storeLocatorHomeUrl]);
            $breadcrumbsBlock->addCrumb('store', ['label' => $retailer->getName(), 'title' => $retailer->getName(), 'link' => $storeUrl]);
            $breadcrumbsBlock->addCrumb('contact', ['label' => __('Contact'), 'title' => __('Contact')]);
        }

        return $this;
    }
}
