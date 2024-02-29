<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Block;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\Store;
use Smile\Retailer\Api\Data\RetailerInterface;

/**
 * Retailer View Block.
 */
class AbstractView extends Template
{
    public function __construct(
        Context $context,
        protected Registry $coreRegistry,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Get the current shop.
     */
    public function getRetailer(): ?RetailerInterface
    {
        return $this->coreRegistry->registry('current_retailer');
    }

    /**
     * Get image name.
     */
    protected function getMediaPath(): string|bool
    {
        return $this->getRetailer()->getMediaPath() ?: false;
    }

    /**
     * Get full image url.
     */
    public function getImage(): string|bool
    {
        $mediaPath = $this->getMediaPath();
        $imageUrlRetailer = $this->getImageUrl() . 'seller/';

        return $mediaPath ? $imageUrlRetailer . $mediaPath : false;
    }

    /**
     * * Get base media url.
     *
     * @throws NoSuchEntityException
     */
    public function getImageUrl(): string
    {
        /** @var Store $currentStore */
        $currentStore = $this->_storeManager->getStore();

        return $currentStore->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
    }
}
