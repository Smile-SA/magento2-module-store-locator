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
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Smile\Retailer\Api\RetailerRepositoryInterface;
use Smile\StoreLocator\CustomerData\CurrentStore;

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
     * @var CurrentStore
     */
    private CurrentStore $customerData;

    /**
     * @var RetailerRepositoryInterface
     */
    private RetailerRepositoryInterface $retailerRepository;

    /**
     * Set constructor.
     *
     * @param Context                       $context            Action context.
     * @param RetailerRepositoryInterface   $retailerRepository Retailer repository.
     * @param CurrentStore                  $customerData       Store customer data.
     */
    public function __construct(
        Context $context,
        RetailerRepositoryInterface $retailerRepository,
        CurrentStore $customerData
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
     * @return ResponseInterface|ResultInterface
     */
    public function execute(): ResponseInterface|ResultInterface
    {
        $retailerId = $this->getRequest()->getParam('id', false);

        try {
            $retailer   = $this->retailerRepository->get($retailerId);
            $this->customerData->setRetailer($retailer);
        } catch (\Exception $exception) {
            $this->messageManager->addExceptionMessage($exception, __("We are sorry, an error occured when switching retailer."));
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());

        return $resultRedirect;
    }
}
