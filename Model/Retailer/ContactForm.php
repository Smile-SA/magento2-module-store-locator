<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Model\Retailer;

use Exception;
use Laminas\Validator\EmailAddress;
use Laminas\Validator\NotEmpty;
use Magento\Contact\Controller\Index;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Smile\Retailer\Api\Data\RetailerInterface;

/**
 * Store Contact Form model.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ContactForm
{
    private DataObject $dataObject;

    public function __construct(
        private ScopeConfigInterface $scopeConfig,
        private TransportBuilder $transportBuilder,
        private StateInterface $inlineTranslation,
        private RetailerInterface $retailer,
        private NotEmpty $notEmptyValidator,
        private EmailAddress $emailAddressValidator,
        array $data
    ) {
        $this->dataObject = new DataObject($data);
    }

    /**
     * Send contact form.
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @throws LocalizedException
     * @throws MailException
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
                        'area' => Area::AREA_FRONTEND,
                        'store' => Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars(['data' => $postObject])
                ->setFrom($this->scopeConfig->getValue(Index::XML_PATH_EMAIL_SENDER, $storeScope))
                ->addTo($this->retailer->getData('contact_mail'))
                ->setReplyTo($this->dataObject->getData('email'))
                ->getTransport();

            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (Exception $exception) {
            $this->inlineTranslation->resume();
            throw $exception;
        }
    }

    /**
     * Send contact form.
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @throws Exception
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

        if (!$this->retailer->getId() || (!$this->retailer->getData('contact_mail'))) {
            $error = __('Unable to retrieve retailer informations');
        }

        if (false !== $error) {
            throw new Exception($error->render());
        }
    }
}
