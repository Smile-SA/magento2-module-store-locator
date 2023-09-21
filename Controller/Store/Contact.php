<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Controller\Store;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Smile\Retailer\Api\Data\RetailerInterface;
use Smile\Retailer\Api\RetailerRepositoryInterface;
use Smile\StoreLocator\Helper\Contact as ContactHelper;

/**
 * Contact Form action for Shops.
 */
class Contact extends Action implements HttpGetActionInterface
{
    public function __construct(
        Context $context,
        private PageFactory $resultPageFactory,
        private ForwardFactory $resultForwardFactory,
        private Registry $coreRegistry,
        private RetailerRepositoryInterface $retailerRepository,
        private ContactHelper $contactHelper
    ) {
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $retailerId = $this->getRequest()->getParam('id');
        /** @var RetailerInterface $retailer */
        $retailer   = $this->retailerRepository->get((int) $retailerId);

        if (!$retailer->getId() || !$this->contactHelper->canDisplayContactForm($retailer)) {
            $resultForward = $this->resultForwardFactory->create();

            return $resultForward->forward('noroute');
        }

        $this->coreRegistry->register('current_retailer', $retailer);

        return $this->resultPageFactory->create();
    }
}
