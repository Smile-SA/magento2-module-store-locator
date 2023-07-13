<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Controller\Adminhtml\Retailer;

use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Smile\Retailer\Controller\Adminhtml\AbstractRetailer;

/**
 * Retailer Adminhtml MassEditHours controller.
 */
class MassEditHours extends AbstractRetailer implements HttpPostActionInterface
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        $retailerIds = $this->getAllSelectedIds();
        $this->coreRegistry->register('retailer_ids', $retailerIds);

        $resultPage->getConfig()->getTitle()->prepend(__('Edit retailers informations'));
        $resultPage->addBreadcrumb(__('Retailer'), __('Retailer'));

        return $resultPage;
    }
}
