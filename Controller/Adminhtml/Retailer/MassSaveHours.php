<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Controller\Adminhtml\Retailer;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use Smile\Retailer\Api\Data\RetailerInterface;
use Smile\Retailer\Api\Data\RetailerInterfaceFactory;
use Smile\Retailer\Api\RetailerRepositoryInterface;
use Smile\Retailer\Controller\Adminhtml\AbstractRetailer;
use Smile\Retailer\Model\ResourceModel\Retailer\CollectionFactory;
use Smile\StoreLocator\Model\Retailer\OpeningHoursPostDataHandler;
use Smile\StoreLocator\Model\Retailer\SpecialOpeningHoursPostDataHandler;

/**
 * Retailer Adminhtml MassSaveHours controller.
 */
class MassSaveHours extends AbstractRetailer implements HttpPostActionInterface
{
    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory,
        Registry $coreRegistry,
        RetailerRepositoryInterface $retailerRepository,
        RetailerInterfaceFactory $retailerFactory,
        Filter $filter,
        CollectionFactory $collectionFactory,
        protected OpeningHoursPostDataHandler $openingHoursHandler,
        protected SpecialOpeningHoursPostDataHandler $specialOpeningHoursHandler
    ) {
        parent::__construct(
            $context,
            $resultPageFactory,
            $resultForwardFactory,
            $coreRegistry,
            $retailerRepository,
            $retailerFactory,
            $filter,
            $collectionFactory
        );
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $retailerIds = json_decode($this->getRequest()->getParam('retailer_ids'));
        $data = [];

        $openingHoursPost = $this->getRequest()->getParam('opening_hours', false);
        if ($openingHoursPost) {
            $data['opening_hours'] = $openingHoursPost;
        }

        $specialOpeningHoursPost = $this->getRequest()->getParam('special_opening_hours', false);
        if ($specialOpeningHoursPost) {
            $data['special_opening_hours'] = $specialOpeningHoursPost;
        }

        if (is_iterable($retailerIds)) {
            foreach ($retailerIds as $id) {
                /** @var RetailerInterface $model */
                $model = $this->retailerRepository->get((int) $id);

                $openingHours = $this->openingHoursHandler->getData($model, $data);
                if (isset($openingHours['opening_hours'])) {
                    $model->setData('opening_hours', $openingHours['opening_hours']);
                }

                $specialOpeningHours = $this->specialOpeningHoursHandler->getData($model, $data);
                if (isset($specialOpeningHours['special_opening_hours'])) {
                    $model->setData('special_opening_hours', $specialOpeningHours['special_opening_hours']);
                }

                $this->retailerRepository->save($model);
            }

            $this->messageManager->addSuccessMessage(
                __('A total of %1 record(s) have been saved.', count($retailerIds))
            );
        }
        if (!is_iterable($retailerIds)) {
            $this->messageManager->addErrorMessage(
                __('An Error occured, please retry.')
            );
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('smile_retailer/retailer/');
    }
}
