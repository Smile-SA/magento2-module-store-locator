<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\StoreLocator
 * @author    Romain Ruaud <romain.ruaud@smile.fr>
 * @author    Guillaume Vrac <guillaume.vrac@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\StoreLocator\Api\Data;

/**
 * Retailer Store Locator interface
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 * @author   Guillaume Vrac <guillaume.vrac@smile.fr>
 */
interface RetailerInterface extends \Smile\Retailer\Api\Data\RetailerInterface
{
    /**#@+
     * Constants defined for keys of data array.
     */
    const CONTACT_FIRSTNAME = 'contact_firstname';
    const CONTACT_LASTNAME  = 'contact_lastname';
    const CONTACT_EMAIL     = 'contact_email';
    const CONTACT_PHONE     = 'contact_phone';
    const IMAGE1            = 'image1';
    const IMAGE1_ALT        = 'image1_alt';
    const IMAGE2            = 'image2';
    const IMAGE2_ALT        = 'image2_alt';
    const IMAGE3            = 'image3';
    const IMAGE3_ALT        = 'image3_alt';
    const STREET            = 'street';
    const POSTCODE          = 'postcode';
    const CITY              = 'city';
    const COUNTRY_ID        = 'country_id';
    const LATITUDE          = 'latitude';
    const LONGITUDE         = 'longitude';
    /**#@-*/

    /**
     * Base path for Retailer Images.
     */
    const BASE_IMAGE_PATH = 'retailer/';
}
