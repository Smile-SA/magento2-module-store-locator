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

use Magento\Framework\App\CacheInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Indexer\CacheContext;
use Magento\Framework\Indexer\CacheContextFactory;
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
     * @var CacheContextFactory
     */
    protected $cacheContextFactory;

    /**
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * CleanStoreLocatorCache constructor.
     *
     * @param CacheContextFactory $cacheContextFactory CacheContextFactory.
     * @param ManagerInterface $eventManager EventManager.
     * @param CacheInterface $cache Cache.
     */
    public function __construct(CacheContextFactory $cacheContextFactory, ManagerInterface $eventManager, CacheInterface $cache)
    {
        $this->cacheContextFactory = $cacheContextFactory;
        $this->eventManager = $eventManager;
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
            /** @var CacheContext $cacheContext */
            $cacheContext = $this->cacheContextFactory->create();
            $cacheContext->registerTags([Search::CACHE_TAG]);
            $this->eventManager->dispatch('clean_cache_by_tags', ['object' => $cacheContext]);

            $this->cache->clean([Search::CACHE_TAG]);
        }
    }
}
