<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\StoreLocator
 * @author    Romain Ruaud <romain.ruaud@smile.fr>
 * @copyright 2017 Smile
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
use \Smile\StoreLocator\Helper\Contact as ContactHelper;

/**
 * Contact Form action for Shops
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
class Contact extends Action
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
     * @var RetailerRepositoryInterface
     */
    private $retailerRepository;

    /**
     * @var \Smile\StoreLocator\Helper\Contact
     */
    private $contactHelper;

    /**
     * Constructor.
     *
     * @param Context                     $context            Application Context
     * @param PageFactory                 $pageFactory        Result Page Factory
     * @param ForwardFactory              $forwardFactory     Forward Factory
     * @param Registry                    $coreRegistry       Application Registry
     * @param RetailerRepositoryInterface $retailerRepository Retailer Repository
     * @param ContactHelper               $contactHelper      Contact Helper
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        ForwardFactory $forwardFactory,
        Registry $coreRegistry,
        RetailerRepositoryInterface $retailerRepository,
        ContactHelper $contactHelper
    ) {
        parent::__construct($context);

        $this->resultPageFactory    = $pageFactory;
        $this->resultForwardFactory = $forwardFactory;
        $this->coreRegistry         = $coreRegistry;
        $this->retailerRepository   = $retailerRepository;
        $this->contactHelper        = $contactHelper;
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
        $retailerId = $this->getRequest()->getParam('id');
        $retailer   = $this->retailerRepository->get($retailerId);

        if (!$retailer->getId() || !$this->contactHelper->canDisplayContactForm($retailer)) {
            $resultForward = $this->resultForwardFactory->create();

            return $resultForward->forward('noroute');
        }

        $this->coreRegistry->register('current_retailer', $retailer);

        return $this->resultPageFactory->create();
    }
}
