<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Plugin\Model\Layout;

use Magento\Customer\Model\Layout\DepersonalizePlugin;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\View\LayoutInterface;
use Magento\PageCache\Model\DepersonalizeChecker;
use Smile\StoreLocator\CustomerData\CurrentStore;

class RetailerDepersonalizePlugin
{
    public function __construct(
        protected DepersonalizeChecker $depersonalizeChecker,
        protected CustomerSession $customerSession,
        protected CurrentStore $currentStore
    ) {
    }

    /**
     * Prevent losing retailer_id chosen - show product offer price if shop has been selected, even in Retail mode
     */
    public function afterAfterGenerateElements(DepersonalizePlugin $subject, mixed $result, LayoutInterface $layout)
    {
        if ($this->depersonalizeChecker->checkIfDepersonalize($layout)) {
            $retailer = $this->currentStore->getRetailer();
            if ($retailer && $retailer->getId()) {
                $data = $this->customerSession->getData();
                $data['retailer_id'] = $retailer->getId();
                $this->customerSession->setData($data);
            }
        }
    }
}
