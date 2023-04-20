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
namespace Smile\StoreLocator\Model\Data;

use Smile\Map\Model\GeolocalizedAddress;
use Smile\StoreLocator\Api\Data\RetailerAddressInterface;

/**
 * Retailer address default implementation.
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class RetailerAddress extends GeolocalizedAddress implements RetailerAddressInterface
{
    /**
     * {@inheritDoc}
     */
    public function getId(): int
    {
        return $this->getData(self::ADDRESS_ID);
    }

    /**
     * {@inheritDoc}
     */
    public function getRetailerId(): int
    {
        return $this->getData(self::RETAILER_ID);
    }

    /**
     * @SuppressWarnings(PHPMD.ShortVariable)
     *
     * {@inheritDoc}
     */
    public function setId(mixed $id): self
    {
        return $this->setData(self::ADDRESS_ID, $id);
    }

    /**
     * {@inheritDoc}
     */
    public function setRetailerId(string|int $retailerId): self
    {
        return $this->setData(self::RETAILER_ID, $retailerId);
    }
}
