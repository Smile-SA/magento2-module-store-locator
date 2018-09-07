<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\StoreLocator
 * @author    Maxime Leclercq <maxime.leclercq@smile.fr>
 * @copyright 2018 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\StoreLocator\Observer;

use Magento\PageCache\Model\Cache\Type as Cache;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Smile\Seller\Api\Data\SellerInterface;
use Smile\StoreLocator\Block\Search;

/**
 * Clean store locator cache observer.
 *
 * @category Smile
 * @package  Smile\StoreLocator
 */
class CleanStoreLocatorCache implements ObserverInterface
{
    /**
     * @var Cache
     */
    private $cache;

    /**
     * CleanStoreLocatorCache constructor.
     *
     * @param Cache $cache Cache.
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Clean store locator marker cache when save a seller.
     *
     * @param Observer $observer
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     */
    public function execute(Observer $observer)
    {
        /** @var SellerInterface $seller */
        $seller = $observer->getEvent()->getSeller();
        if ($seller->hasDataChanges()) {
            $this->cache->clean(\Zend_Cache::CLEANING_MODE_MATCHING_TAG, [Search::CACHE_TAG]);
        }
    }
}
