<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Controller\Store;

use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\HTTP\PhpEnvironment\Request;
use Magento\Store\Model\StoreManagerInterface;
use Smile\Retailer\Api\Data\RetailerInterface;
use Smile\Retailer\Api\RetailerRepositoryInterface;
use Smile\StoreLocator\Helper\Contact as ContactHelper;
use Smile\StoreLocator\Model\Retailer\ContactFormFactory;

/**
 * Store Contact form submit.
 */
class ContactPost extends Action implements HttpPostActionInterface
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
    public function execute(): ResponseInterface|ResultInterface|null
    {
        /** @var Request $request */
        $request    = $this->getRequest();
        $postData   = $request->getPostValue();
        $retailerId = $request->getParam('id');
        $storeId    = (int) $this->storeManager->getStore()->getId();
        /** @var RetailerInterface $retailer */
        $retailer   = $this->retailerRepository->get((int) $retailerId, $storeId);

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
