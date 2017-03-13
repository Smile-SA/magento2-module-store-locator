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
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use Smile\Retailer\Api\RetailerRepositoryInterface;
use Smile\StoreLocator\Helper\Contact as ContactHelper;
use Smile\StoreLocator\Model\Retailer\ContactFormFactory;

/**
 * Store Contact form submit.
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
class ContactPost extends Action
{
    /**
     * Page factory.
     *
     * @var PageFactory
     */
    private $resultPageFactory;

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
     * @var \Smile\StoreLocator\Model\Retailer\ContactFormFactory
     */
    private $contactFormFactory;

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var \Smile\StoreLocator\Helper\Contact
     */
    private $contactHelper;

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    private $forwardFactory;

    /**
     * Constructor.
     *
     * @param Context                     $context                Application Context
     * @param PageFactory                 $pageFactory            Result Page Factory
     * @param StoreManagerInterface       $storeManager           Store Manager
     * @param RetailerRepositoryInterface $retailerRepository     Retailer Repository
     * @param DataPersistorInterface      $dataPersistorInterface Data Persistor
     * @param ContactFormFactory          $contactFormFactory     Contact Form Factory
     * @param ContactHelper               $contactHelper          Contact Helper
     * @param ForwardFactory              $forwardFactory         Forward Factory
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        StoreManagerInterface $storeManager,
        RetailerRepositoryInterface $retailerRepository,
        DataPersistorInterface $dataPersistorInterface,
        ContactFormFactory $contactFormFactory,
        ContactHelper $contactHelper,
        ForwardFactory $forwardFactory
    ) {
        parent::__construct($context);

        $this->resultPageFactory  = $pageFactory;
        $this->storeManager       = $storeManager;
        $this->retailerRepository = $retailerRepository;
        $this->contactFormFactory = $contactFormFactory;
        $this->dataPersistor      = $dataPersistorInterface;
        $this->contactHelper      = $contactHelper;
        $this->forwardFactory     = $forwardFactory;
    }

    /**
     * Post user question
     *
     * @throws \Exception
     * @return void|ResultInterface
     */
    public function execute()
    {
        $postData   = $this->getRequest()->getPostValue();
        $retailerId = $this->getRequest()->getParam('id');
        $retailer   = $this->retailerRepository->get($retailerId, $this->storeManager->getStore()->getId());

        if (!$retailer->getId() || !$this->contactHelper->canDisplayContactForm($retailer)) {
            $resultForward = $this->forwardFactory->create();

            return $resultForward->forward('noroute');
        }

        try {
            if (!$postData) {
                $this->_redirect($this->contactHelper->getContactFormUrl($retailer));

                return;
            }

            $contactForm = $this->contactFormFactory->create(['retailer' => $retailer, 'data' => $postData]);
            $contactForm->send();

            $this->messageManager->addSuccessMessage(
                __('Thanks for contacting us with your comments and questions. We\'ll respond to you very soon.')
            );
            $this->dataPersistor->clear('contact_store');
            $this->_redirect($this->contactHelper->getContactFormUrl($retailer));

            return;
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
            );
            $this->dataPersistor->set('contact_store', $postData);
            $this->_redirect($this->contactHelper->getContactFormUrl($retailer));

            return;
        }
    }
}
