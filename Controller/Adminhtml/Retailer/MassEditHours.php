<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\StoreLocator
 * @author    Fanny DECLERCK <fadec@smile.fr>
 * @copyright 2019 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Smile\StoreLocator\Controller\Adminhtml\Retailer;

use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Smile\Retailer\Controller\Adminhtml\AbstractRetailer;

/**
 * Retailer Adminhtml MassEditHours controller.
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Fanny DECLERCK <fadec@smile.fr>
 */
class MassEditHours extends AbstractRetailer
{
    /**
     * {@inheritdoc}
     */
    public function execute(): ResponseInterface|ResultInterface
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
