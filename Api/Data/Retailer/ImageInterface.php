<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\StoreLocator
 * @author    Romain Ruaud <romain.ruaud@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Smile\StoreLocator\Api\Data\Retailer;

/**
 * Retailer Images interface
 *
 * @api
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
interface ImageInterface
{
    /**
     * Retrieve Image Url
     *
     * @return string
     */
    public function getUrl();

    /**
     * Retrieve Image Alt
     *
     * @return string
     */
    public function getAlt();
}
