<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\LocalizedRetailer
 * @author    Romain Ruaud <romain.ruaud@smile.fr>
 * @author    Guillaume Vrac <guillaume.vrac@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\StoreLocator\Controller\Store;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use Smile\Retailer\Api\RetailerRepositoryInterface;

/**
 * Retailer view action (displays the retailer details page).
 *
 * @category Smile
 * @package  Smile\LocalizedRetailer
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 * @author   Guillaume Vrac <guillaume.vrac@smile.fr>
 */
class View extends Action
{
    /**
     * Page factory.
     *
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * Forward factory.
     *
     * @var ForwardFactory
     */
    private $resultForwardFactory;

    /**
     * Core registry.
     *
     * @var Registry
     */
    private $coreRegistry;

    /**
     * Store manager.
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var RetailerRepositoryInterface
     */
    private $retailerRepository;

    /**
     * Constructor.
     *
     * @param Context                     $context            Application Context
     * @param PageFactory                 $pageFactory        Result Page Factory
     * @param ForwardFactory              $forwardFactory     Forward Factory
     * @param Registry                    $coreRegistry       Application Registry
     * @param StoreManagerInterface       $storeManager       Store Manager
     * @param RetailerRepositoryInterface $retailerRepository Retailer Repository
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        ForwardFactory $forwardFactory,
        Registry $coreRegistry,
        StoreManagerInterface $storeManager,
        RetailerRepositoryInterface $retailerRepository
    ) {
        parent::__construct($context);

        $this->resultPageFactory    = $pageFactory;
        $this->resultForwardFactory = $forwardFactory;
        $this->coreRegistry         = $coreRegistry;
        $this->storeManager         = $storeManager;
        $this->retailerRepository   = $retailerRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $retailerId = $this->getRequest()->getParam('id');
        $storeId    = $this->storeManager->getStore()->getId();
        $retailer   = $this->retailerRepository->get($retailerId, $storeId);

        if (!$retailer->getId()) {
            $resultForward = $this->resultForwardFactory->create();

            return $resultForward->forward('noroute');
        }

        $this->coreRegistry->register('current_retailer', $retailer);

        return $this->resultPageFactory->create();
    }
}
