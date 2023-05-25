<?php

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
    public function getId(): int;

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
    public function setId(mixed $id): self;

    /**
     * Set retailer id.
     *
     * @param string|int $retailerId Retailer id.
     * @return $this
     */
    public function setRetailerId(string|int $retailerId): self;
}
