<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\StoreLocator
 * @author    Aurelien FOUCRET <aurelien.foucret@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\StoreLocator\Controller\Store;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;

/**
 * Frontend Controller meant to set current Retailer to customer session
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class Set extends Action
{
    /**
     * @var \Smile\StoreLocator\CustomerData\CurrentStore
     */
    private $customerData;

    /**
     * @var \Smile\Retailer\Api\RetailerRepositoryInterface
     */
    private $retailerRepository;

    /**
     * Set constructor.
     *
     * @param \Magento\Framework\App\Action\Context           $context            Action context.
     * @param \Smile\Retailer\Api\RetailerRepositoryInterface $retailerRepository Retailer repository.
     * @param \Smile\StoreLocator\CustomerData\CurrentStore   $customerData       Store customer data.
     * @param \Magento\Framework\Event\ManagerInterface       $eventManager       Event manager.
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Smile\Retailer\Api\RetailerRepositoryInterface $retailerRepository,
        \Smile\StoreLocator\CustomerData\CurrentStore $customerData
    ) {
        parent::__construct($context);

        $this->retailerRepository = $retailerRepository;
        $this->customerData       = $customerData;
    }

    /**
     * Dispatch request. Will bind submitted retailer id (if any) to current customer session
     *
     * @throws \Magento\Framework\Exception\NotFoundException
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     */
    public function execute()
    {
        $retailerId = $this->getRequest()->getParam('id', false);

        try {
            $retailer   = $this->retailerRepository->get($retailerId);
            $this->customerData->setRetailer($retailer);

            $this->_eventManager->dispatch(
              'store_locator_controller_store_set_after',
              ['myRetailer'=>$retailer]);
        } catch (\Exception $exception) {
            $this->messageManager->addExceptionMessage($exception, __("We are sorry, an error occured when switching retailer."));
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());

        return $resultRedirect;
    }
}
