<?php

namespace Smile\StoreLocator\Controller\Store;

use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Store\Model\StoreManagerInterface;
use Smile\Retailer\Api\RetailerRepositoryInterface;
use Smile\StoreLocator\Helper\Contact as ContactHelper;
use Smile\StoreLocator\Model\Retailer\ContactFormFactory;

/**
 * Store Contact form submit.
 */
class ContactPost extends Action
{
    public function __construct(
        Context $context,
        private StoreManagerInterface $storeManager,
        private RetailerRepositoryInterface $retailerRepository,
        private DataPersistorInterface $dataPersistor,
        private ContactFormFactory $contactFormFactory,
        private ContactHelper $contactHelper,
        private ForwardFactory $forwardFactory
    ) {
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $postData   = $this->getRequest()->getPostValue();
        $retailerId = $this->getRequest()->getParam('id');
        $retailer   = $this->retailerRepository->get($retailerId, $this->storeManager->getStore()->getId());

        if (!$retailer->getId() || !$this->contactHelper->canDisplayContactForm($retailer)) {
            $resultForward = $this->forwardFactory->create();

            return $resultForward->forward('noroute');
        }

        try {
            if (!$postData) {
                $this->_redirect($this->contactHelper->getContactFormUrl($retailer));
                return null;
            }

            $contactForm = $this->contactFormFactory->create(['retailer' => $retailer, 'data' => $postData]);
            $contactForm->send();

            $this->messageManager->addSuccessMessage(
                __('Thanks for contacting us with your comments and questions. We\'ll respond to you very soon.')
            );
            $this->dataPersistor->clear('contact_store');
            $this->_redirect($this->contactHelper->getContactFormUrl($retailer));

            return null;
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
            );
            $this->dataPersistor->set('contact_store', $postData);
            $this->_redirect($this->contactHelper->getContactFormUrl($retailer));

            return null;
        }
    }
}
