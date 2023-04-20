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

use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use Smile\Retailer\Api\Data\RetailerInterfaceFactory;
use Smile\Retailer\Api\RetailerRepositoryInterface;
use Smile\Retailer\Controller\Adminhtml\AbstractRetailer;
use Smile\Retailer\Model\ResourceModel\Retailer\CollectionFactory;
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
    protected OpeningHoursPostDataHandler $openingHoursHandler;

    /**
     * @var SpecialOpeningHoursPostDataHandler
     */
    protected SpecialOpeningHoursPostDataHandler $specialOpeningHoursHandler;

    /**
     * Abstract constructor.
     *
     * @param Context                               $context                    Application context.
     * @param PageFactory                           $resultPageFactory          Result Page factory.
     * @param ForwardFactory                        $resultForwardFactory       Result forward factory.
     * @param Registry                              $coreRegistry               Application registry.
     * @param RetailerRepositoryInterface           $retailerRepository         Retailer Repository
     * @param RetailerInterfaceFactory              $retailerFactory            Retailer Factory.
     * @param OpeningHoursPostDataHandler           $openingHoursHandler        Opening Hours Handler.
     * @param SpecialOpeningHoursPostDataHandler    $specialOpeningHoursHandler Special Opening Hours Handler.
     * @param Filter                                $filter                     Mass Action Filter.
     * @param CollectionFactory                     $collectionFactory          Retailer collection for Mass Action.
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
            $retailerFactory,
            $filter,
            $collectionFactory
        );
    }

    /**
     * Execute action
     *
     * @return Redirect
     * @throws LocalizedException|\Exception
     */
    public function execute(): Redirect
    {
        $retailerIds = json_decode($this->getRequest()->getParam('retailer_ids'));
        $data = [];

        if ($openingHoursPost = $this->getRequest()->getParam('opening_hours', false)) {
            $data['opening_hours'] = $openingHoursPost;
        }

        if ($specialOpeningHoursPost = $this->getRequest()->getParam('special_opening_hours', false)) {
            $data['special_opening_hours'] = $specialOpeningHoursPost;
        }

        if (is_iterable($retailerIds)) {
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
