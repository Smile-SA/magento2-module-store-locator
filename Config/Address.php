<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\LocalizedRetailer
 * @author    Romain Ruaud <romain.ruaud@smile.fr>
 * @author    Guillaume Vrac <guillaume.vrac@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\StoreLocator\Config;

use Magento\Framework\Config\CacheInterface;
use Magento\Framework\Config\Data;
use Smile\StoreLocator\Config\Address\Reader;

/**
 * Address config parser.
 *
 * @category Smile
 * @package  Smile\LocalizedRetailer
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 * @author   Guillaume Vrac <guillaume.vrac@smile.fr>
 */
class Address extends Data
{
    /**
     * Constructor.
     *
     * @param \Smile\LocalizedRetailer\Config\Address\Reader $reader  Address Reader
     * @param \Magento\Framework\Config\CacheInterface       $cache   Configuration Cache
     * @param string                                         $cacheId Cache Id
     */
    public function __construct(
        Reader $reader,
        CacheInterface $cache,
        $cacheId = 'smile_store_locator_retailer_addresses'
    ) {
        parent::__construct($reader, $cache, $cacheId);
    }

    /**
     * {@inheritdoc}
     */
    public function getFormat($name)
    {
        return $this->get($name, []);
    }

    /**
     * {@inheritdoc}
     */
    public function getFormats()
    {
        return $this->get();
    }
}
