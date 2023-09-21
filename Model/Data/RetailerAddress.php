<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Model\Data;

use Smile\Map\Model\GeolocalizedAddress;
use Smile\StoreLocator\Api\Data\RetailerAddressInterface;

/**
 * Retailer address default implementation.
 */
class RetailerAddress extends GeolocalizedAddress implements RetailerAddressInterface
{
    /**
     * @inheritdoc
     */
    public function getAddressId(): int
    {
        return (int) $this->getData(self::ADDRESS_ID);
    }

    /**
     * @inheritdoc
     */
    public function getRetailerId(): int
    {
        return (int) $this->getData(self::RETAILER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setAddressId(mixed $id): self
    {
        return $this->setData(self::ADDRESS_ID, $id);
    }

    /**
     * @inheritdoc
     */
    public function setRetailerId(int $retailerId): self
    {
        return $this->setData(self::RETAILER_ID, $retailerId);
    }
}
