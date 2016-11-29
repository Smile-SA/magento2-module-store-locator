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
namespace Smile\StoreLocator\Model\Retailer;

use Smile\StoreLocator\Api\Data\Retailer\ImageInterface;

/**
 * Retailer Images (pseudo-gallery implementation).
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
class Image implements ImageInterface
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $alt;

    /**
     * Image constructor.
     *
     * @param string $url The Image Url
     * @param string $alt The Image Alt
     */
    public function __construct($url, $alt)
    {
        $this->url = $url;
        $this->alt = $alt;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * {@inheritdoc}
     */
    public function getAlt()
    {
        return $this->alt;
    }
}
