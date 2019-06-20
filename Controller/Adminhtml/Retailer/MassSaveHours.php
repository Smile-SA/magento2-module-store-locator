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

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Smile\Retailer\Controller\Adminhtml\AbstractRetailer;
use Smile\StoreLocator\Model\Retailer\OpeningHoursPostDataHandler;
use Smile\StoreLocator\Model\Retailer\SpecialOpeningHoursPostDataHandler;

/**
 * Retailer Adminhtml MassSaveHours controller.
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Fanny DECLERCK <fadec@smile.fr>
 */
class MassSaveHours extends AbstractRetailer
{
    /**
     * @var OpeningHoursPostDataHandler
     */
    protected $openingHoursHandler;

    /**
     * @var SpecialOpeningHoursPostDataHandler
     */
    protected $specialOpeningHoursHandler;

    /**
     * Abstract constructor.
     *
     * @param \Magento\Backend\App\Action\Context                 $context                    Application context.
     * @param \Magento\Framework\View\Result\PageFactory          $resultPageFactory          Result Page factory.
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory       Result forward factory.
     * @param \Magento\Framework\Registry                         $coreRegistry               Application registry.
     * @param \Smile\Retailer\Api\RetailerRepositoryInterface     $retailerRepository         Retailer Repository
     * @param \Smile\Retailer\Api\Data\RetailerInterfaceFactory   $retailerFactory            Retailer Factory.
     * @param OpeningHoursPostDataHandler                         $openingHoursHandler        Opening Hours Handler.
     * @param SpecialOpeningHoursPostDataHandler                  $specialOpeningHoursHandler Special Opening Hours
     *                                                                                        Handler.
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Smile\Retailer\Api\RetailerRepositoryInterface $retailerRepository,
        \Smile\Retailer\Api\Data\RetailerInterfaceFactory $retailerFactory,
        OpeningHoursPostDataHandler $openingHoursHandler,
        SpecialOpeningHoursPostDataHandler $specialOpeningHoursHandler
    ) {
        $this->openingHoursHandler        = $openingHoursHandler;
        $this->specialOpeningHoursHandler = $specialOpeningHoursHandler;

        parent::__construct(
            $context,
            $resultPageFactory,
            $resultForwardFactory,
            $coreRegistry,
            $retailerRepository,
            $retailerFactory
        );
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $retailerIds = json_decode($this->getRequest()->getParam('retailer_ids'));
        $data = [];

        if ($openingHoursPost = $this->getRequest()->getParam('opening_hours', false)) {
            $data['opening_hours'] = $openingHoursPost;
        }

        if ($specialOpeningHoursPost = $this->getRequest()->getParam('special_opening_hours', false)) {
            $data['special_opening_hours'] = $specialOpeningHoursPost;
        }

        foreach ($retailerIds as $id) {
            $model = $this->retailerRepository->get($id);

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

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('smile_retailer/retailer/');
    }
}
