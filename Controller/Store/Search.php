<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\StoreLocator
 * @author    Romain Ruaud <romain.ruaud@smile.fr>
 * @author    Guillaume Vrac <guillaume.vrac@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\StoreLocator\Controller\Store;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Smile\StoreLocator\Api\LocatorInterface;

/**
 * Search action (displays the search page).
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 * @author   Guillaume Vrac <guillaume.vrac@smile.fr>
 */
class Search extends Action
{
    /**
     * Page factory.
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Store locator.
     *
     * @var \Smile\StoreLocator\Api\LocatorInterface
     */
    protected $retailerLocator;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\App\Action\Context      $context     Application Context.
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory Result Page Factory.
     */
    public function __construct(Context $context, PageFactory $pageFactory)
    {
        parent::__construct($context);

        $this->resultPageFactory = $pageFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        return $this->resultPageFactory->create();
    }
}
