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

use Magento\CacheInvalidate\Model\PurgeCache;
use Magento\Framework\App\CacheInterface;
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
     * @var PurgeCache
     */
    protected $purgeCache;

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * CleanStoreLocatorCache constructor.
     *
     * @param PurgeCache     $purgeCache PurgeCache.
     * @param CacheInterface $cache      Cache.
     */
    public function __construct(PurgeCache $purgeCache, CacheInterface $cache)
    {
        $this->purgeCache = $purgeCache;
        $this->cache = $cache;
    }

    /**
     * Clean store locator marker cache when save a seller.
     *
     * @param Observer $observer Observer.
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     */
    public function execute(Observer $observer)
    {
        /** @var SellerInterface $seller */
        $seller = $observer->getEvent()->getSeller();

        if ($seller->hasDataChanges()) {
            $formattedTag = sprintf('((^|,)%s(,|$))', Search::CACHE_TAG);

            $this->purgeCache->sendPurgeRequest($formattedTag);

            $this->cache->clean([Search::CACHE_TAG]);
        }
    }
}
