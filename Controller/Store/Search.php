<?php

namespace Smile\StoreLocator\Controller\Store;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Smile\StoreLocator\Api\LocatorInterface;

/**
 * Search action (displays the search page).
 */
class Search extends Action
{
    protected LocatorInterface $retailerLocator;

    public function __construct(Context $context, protected PageFactory $resultPageFactory)
    {
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        return $this->resultPageFactory->create();
    }
}
