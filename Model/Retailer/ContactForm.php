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
namespace Smile\StoreLocator\Model\Retailer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;
use Smile\Retailer\Api\Data\RetailerInterface;

/**
 * Store Contact Form model.
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
class ContactForm
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    private $inlineTranslation;

    /**
     * @var \Magento\Framework\DataObject
     */
    private $dataObject;

    /**
     * ContactForm constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig       Scope Config
     * @param \Magento\Framework\Mail\Template\TransportBuilder  $transportBuilder  Transport Builder
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation Inline Translation
     * @param RetailerInterface                                  $retailer          Current Retailer
     * @param array                                              $data              Form Data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        RetailerInterface $retailer,
        $data
    ) {
        $this->scopeConfig       = $scopeConfig;
        $this->transportBuilder  = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->retailer          = $retailer;
        $this->dataObject        = new \Magento\Framework\DataObject($data);
    }

    /**
     * Send contact form
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * @throws \Exception
     * @throws \Zend_Validate_Exception
     */
    public function send()
    {
        $postObject = $this->dataObject;
        $this->inlineTranslation->suspend();

        try {
            $this->validate();
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $transport  = $this->transportBuilder
                ->setTemplateIdentifier($this->scopeConfig->getValue(\Magento\Contact\Controller\Index::XML_PATH_EMAIL_TEMPLATE, $storeScope))
                ->setTemplateOptions(
                    [
                        'area'  => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars(['data' => $postObject])
                ->setFrom($this->scopeConfig->getValue(\Magento\Contact\Controller\Index::XML_PATH_EMAIL_SENDER, $storeScope))
                ->addTo($this->retailer->getContactMail())
                ->setReplyTo($this->dataObject->getData('email'))
                ->getTransport();

            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $exception) {
            $this->inlineTranslation->resume();
            throw $exception;
        }
    }

    /**
     * Send contact form
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * @throws \Exception
     * @throws \Zend_Validate_Exception
     */
    private function validate()
    {
        $post = $this->dataObject->getData();

        $error = false;

        if (!\Zend_Validate::is(trim($post['name']), 'NotEmpty')) {
            $error = __('Name cannot be empty');
        }
        if (!\Zend_Validate::is(trim($post['comment']), 'NotEmpty')) {
            $error = __('Contact form cannot be empty');
        }
        if (!\Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
            $error = __('Contact mail cannot be empty');
        }
        if (\Zend_Validate::is(trim($post['hideit']), 'NotEmpty')) {
            $error = __('Unable to validate form');
        }

        if ((!$this->retailer->getId()) || (!$this->retailer->getContactMail())) {
            $error = __('Unable to retrieve retailer informations');
        }

        if (false !== $error) {
            throw new \Exception($error);
        }
    }
}
