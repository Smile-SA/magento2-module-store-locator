<?php

declare(strict_types=1);

namespace Smile\StoreLocator\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Http\Context;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Smile\Map\Model\AddressFormatter;
use Smile\Retailer\Api\Data\RetailerInterface;
use Smile\Retailer\Api\RetailerRepositoryInterface;
use Smile\Seller\Api\Data\SellerInterface;
use Smile\StoreLocator\Model\Url;

/**
 * Current Store data.
 *
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class CurrentStore implements SectionSourceInterface
{
    /**
     * Will be added as a Vary to HTTP Context
     */
    public const CONTEXT_RETAILER = 'smile_retailer_id';

    public function __construct(
        private Session $customerSession,
        private RetailerRepositoryInterface $retailerRepository,
        private AddressFormatter $addressFormatter,
        private Url $urlModel,
        private Context $httpContext
    ) {
    }

    /**
     * @inheritdoc
     */
    public function getSectionData()
    {
        $data = [];
        /** @var DataObject|RetailerInterface|null $retailer */
        $retailer = $this->getRetailer();

        if ($retailer) {
            $data = $retailer->toArray(['entity_id', 'name']);

            $data['url'] = $this->urlModel->getUrl($retailer);
            $data['address'] = $this->addressFormatter->formatAddress(
                $retailer->getAddress(),
                AddressFormatter::FORMAT_HTML
            );
            $data['address_data'] = $retailer->getAddress()->toArray();
        }

        return $data;
    }

    /**
     * Get the current session retailer.
     */
    public function getRetailer(): ?SellerInterface
    {
        $retailer = null;
        $retailerId = $this->customerSession->getRetailerId();

        if (!$retailerId) {
            $retailerId = $this->httpContext->getValue(self::CONTEXT_RETAILER);
        }

        if ($retailerId) {
            try {
                $retailer = $this->retailerRepository->get((int) $retailerId);
            } catch (NoSuchEntityException) {
                $this->customerSession->unsRetailerId();
            }
        }

        return $retailer;
    }

    /**
     * Set a new retailer.
     */
    public function setRetailer(RetailerInterface $retailer): self
    {
        $this->customerSession->setRetailerId($retailer->getId());
        $this->httpContext->setValue(self::CONTEXT_RETAILER, $retailer->getId(), false);

        return $this;
    }
}
