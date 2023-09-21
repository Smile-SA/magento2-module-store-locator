<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Controller\Store;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use Smile\Retailer\Api\RetailerRepositoryInterface;

/**
 * Retailer view action (displays the retailer details page).
 */
class View extends Action implements HttpGetActionInterface
{
    public function __construct(
        Context $context,
        private PageFactory $resultPageFactory,
        private ForwardFactory $resultForwardFactory,
        private Registry $coreRegistry,
        private StoreManagerInterface $storeManager,
        private RetailerRepositoryInterface $retailerRepository
    ) {
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $retailerId = $this->getRequest()->getParam('id');
        $storeId = (int) $this->storeManager->getStore()->getId();
        $retailer = $this->retailerRepository->get((int) $retailerId, $storeId);

        if (!$retailer->getId()) {
            $resultForward = $this->resultForwardFactory->create();

            return $resultForward->forward('noroute');
        }

        $this->coreRegistry->register('current_retailer', $retailer);

        return $this->resultPageFactory->create();
    }
}
