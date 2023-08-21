<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Api\Data;

use Smile\Map\Api\Data\GeolocalizedAddressInterface;

/**
 * Retailer Store Locator interface.
 */
interface RetailerAddressInterface extends GeolocalizedAddressInterface
{
    public const ADDRESS_ID  = 'address_id';
    public const RETAILER_ID = 'retailer_id';

    /**
     * Get id
     *
     * @return int
     */
    public function getAddressId(): int;

    /**
     * Get retailer id.
     *
     * @return int
     */
    public function getRetailerId(): int;

    /**
     * Set id.
     *
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @param mixed $id Address id.
     * @return $this
     */
    public function setAddressId(mixed $id): self;

    /**
     * Set retailer id.
     *
     * @param int $retailerId Retailer id.
     * @return $this
     */
    public function setRetailerId(int $retailerId): self;
}
