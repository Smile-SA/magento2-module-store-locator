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

use Laminas\Validator\EmailAddress;
use Laminas\Validator\NotEmpty;
use Magento\Contact\Controller\Index;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
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
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @var TransportBuilder
     */
    private TransportBuilder $transportBuilder;

    /**
     * @var StateInterface
     */
    private StateInterface $inlineTranslation;

    /**
     * @var DataObject
     */
    private DataObject $dataObject;

    /**
     * @var RetailerInterface
     */
    private RetailerInterface $retailer;

    /**
     * @var NotEmpty
     */
    private NotEmpty $notEmptyValidator;

    /**
     * @var EmailAddress
     */
    private EmailAddress $emailAddressValidator;

    /**
     * ContactForm constructor.
     *
     * @param ScopeConfigInterface $scopeConfig       Scope Config
     * @param TransportBuilder     $transportBuilder  Transport Builder
     * @param StateInterface       $inlineTranslation Inline Translation
     * @param RetailerInterface    $retailer          Current Retailer
     * @param array                $data              Form Data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        RetailerInterface $retailer,
        NotEmpty $notEmptyValidator,
        EmailAddress $emailAddressValidator,
        array $data
    ) {
        $this->scopeConfig           = $scopeConfig;
        $this->transportBuilder      = $transportBuilder;
        $this->inlineTranslation     = $inlineTranslation;
        $this->retailer              = $retailer;
        $this->dataObject            = new DataObject($data);
        $this->notEmptyValidator     = $notEmptyValidator;
        $this->emailAddressValidator = $emailAddressValidator;
    }

    /**
     * Send contact form
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     */
    public function send(): void
    {
        $postObject = $this->dataObject;
        $this->inlineTranslation->suspend();

        try {
            $this->validate();
            $storeScope = ScopeInterface::SCOPE_STORE;
            $transport  = $this->transportBuilder
                ->setTemplateIdentifier($this->scopeConfig->getValue(Index::XML_PATH_EMAIL_TEMPLATE, $storeScope))
                ->setTemplateOptions(
                    [
                        'area'  => Area::AREA_FRONTEND,
                        'store' => Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars(['data' => $postObject])
                ->setFrom($this->scopeConfig->getValue(Index::XML_PATH_EMAIL_SENDER, $storeScope))
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
     * @return void
     * @throws \Exception
     */
    private function validate(): void
    {
        $post = $this->dataObject->getData();

        $error = false;

        if (!$this->notEmptyValidator->isValid(trim($post['name']))) {
            $error = __('Name cannot be empty');
        }
        if (!$this->notEmptyValidator->isValid(trim($post['comment']))) {
            $error = __('Contact form cannot be empty');
        }
        if (!$this->emailAddressValidator->isValid(trim($post['email']))) {
            $error = __('Contact mail cannot be empty');
        }
        if ($this->notEmptyValidator->isValid(trim($post['hideit']))) {
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
