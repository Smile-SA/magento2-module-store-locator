<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Controller\Store;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Smile\Seller\Model\Locator\LocatorInterface;

/**
 * Search action (displays the search page).
 */
class Search extends Action implements HttpGetActionInterface
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
