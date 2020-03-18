<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\StoreLocator
 * @author    Fanny DECLERCK <fadec@smile.fr>
 * @copyright 2020 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\StoreLocator\Model\Autocomplete;

use Magento\Search\Model\QueryFactory;
use Magento\Search\Model\Autocomplete\ItemFactory;
use Magento\Store\Model\StoreManagerInterface;
use Smile\ElasticsuiteCore\Helper\Autocomplete as ConfigurationHelper;
use Smile\ElasticsuiteRetailer\Model\ResourceModel\Fulltext\CollectionFactory as RetailerCollectionFactory;

/**
 * Retailer autocomplete data provider.
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Fanny DECLERCK <fadec@smile.fr>
 */
class DataProvider
{
    /**
     * Autocomplete type
     */
    const AUTOCOMPLETE_TYPE = "search_retailer";

    /**
     * Autocomplete result item factory
     *
     * @var ItemFactory
     */
    protected $itemFactory;

    /**
     * Query factory
     *
     * @var QueryFactory
     */
    protected $queryFactory;

    /**
     * @var RetailerCollectionFactory
     */
    protected $retailerCollectionFactory;

    /**
     * @var ConfigurationHelper
     */
    protected $configurationHelper;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var string Autocomplete result type
     */
    private $type;

    /**
     * @var \Smile\StoreLocator\Helper\Data
     */
    protected $storeLocatorHelper;

    /**
     * Constructor.
     *
     * @param ItemFactory                     $itemFactory               Suggest item factory.
     * @param QueryFactory                    $queryFactory              Search query factory.
     * @param RetailerCollectionFactory       $retailerCollectionFactory Retailer collection factory.
     * @param ConfigurationHelper             $configurationHelper       Autocomplete configuration helper.
     * @param StoreManagerInterface           $storeManager              Store manager.
     * @param \Smile\StoreLocator\Helper\Data $storeLocatorHelper        StoreLocator Helper.
     * @param string                          $type                      Autocomplete provider type.
     */
    public function __construct(
        ItemFactory $itemFactory,
        QueryFactory $queryFactory,
        RetailerCollectionFactory $retailerCollectionFactory,
        ConfigurationHelper $configurationHelper,
        StoreManagerInterface $storeManager,
        \Smile\StoreLocator\Helper\Data $storeLocatorHelper,
        $type = self::AUTOCOMPLETE_TYPE
    ) {
        $this->itemFactory          = $itemFactory;
        $this->queryFactory         = $queryFactory;
        $this->retailerCollectionFactory = $retailerCollectionFactory;
        $this->configurationHelper  = $configurationHelper;
        $this->type                 = $type;
        $this->storeManager         = $storeManager;
        $this->storeLocatorHelper   = $storeLocatorHelper;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Return items.
     *
     * @param string $query Query text.
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getItems($query)
    {
        $result = [];
        $retailerCollection = $this->getRetailerCollection($query);
        if ($retailerCollection) {
            foreach ($retailerCollection as $retailer) {
                $result[] = $this->itemFactory->create(
                    [
                        'title' => $retailer->getName(),
                        'url'   => $this->storeLocatorHelper->getRetailerUrl($retailer),
                        'type'  => $this->getType(),
                    ]
                );
            }
        }

        return $result;
    }

    /**
     * Suggested retailer collection.
     * Returns null if no suggested search terms.
     *
     * @param string $query Query text.
     *
     * @return \Smile\ElasticsuiteRetailer\Model\ResourceModel\Fulltext\Collection|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getRetailerCollection($terms)
    {
        $retailerCollection = null;
        $retailerCollection = $this->retailerCollectionFactory->create();
        $retailerCollection->addAttributeToSelect(['name', 'url_key']);
        $retailerCollection->addSearchFilter($terms);

        return $retailerCollection;
    }
}