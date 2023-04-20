<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\StoreLocator
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\StoreLocator\Api\Data;

use Smile\Map\Api\Data\GeolocalizedAddressInterface;

/**
 * Retailer Store Locator interface
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
interface RetailerAddressInterface extends GeolocalizedAddressInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ADDRESS_ID  = 'address_id';
    const RETAILER_ID = 'retailer_id';
    /**#@-*/

    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @return int
     */
    public function getRetailerId(): int;

    /**
     * Set id.
     *
     * @SuppressWarnings(PHPMD.ShortVariable)
     *
     * @param mixed $id Address id.
     *
     * @return $this
     */
    public function setId(mixed $id): self;

    /**
     * Set retailer id.
     *
     * @param string|int $retailerId Retailer id.
     *
     * @return $this
     */
    public function setRetailerId(string|int $retailerId): self;
}
