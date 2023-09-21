<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Controller\Store;

use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Smile\Retailer\Api\Data\RetailerInterface;
use Smile\Retailer\Api\RetailerRepositoryInterface;
use Smile\StoreLocator\CustomerData\CurrentStore;

/**
 * Frontend Controller meant to set current Retailer to customer session.
 */
class Set extends Action implements HttpPostActionInterface
{
    public function __construct(
        Context $context,
        private RetailerRepositoryInterface $retailerRepository,
        private CurrentStore $customerData
    ) {
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $retailerId = $this->getRequest()->getParam('id', false);

        try {
            /** @var RetailerInterface $retailer */
            $retailer = $this->retailerRepository->get((int) $retailerId);
            $this->customerData->setRetailer($retailer);
        } catch (Exception $exception) {
            $this->messageManager->addExceptionMessage(
                $exception,
                __('We are sorry, an error occured when switching retailer.')
            );
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());

        return $resultRedirect;
    }
}
