<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\StoreLocator
 * @author    Fanny DECLERCK <fadec@smile.fr>
 * @copyright 2019 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Smile\StoreLocator\Ui\Component\Retailer\Form;

use Smile\Retailer\Model\ResourceModel\Retailer\CollectionFactory;

/**
 * Ui DataProvider controller.
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Fanny DECLERCK <fadec@smile.fr>
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @param string                      $name              Name
     * @param string                      $primaryFieldName  PrimaryFieldName
     * @param string                      $requestFieldName  RequestFieldName
     * @param CollectionFactory           $collectionFactory Collection
     * @param \Magento\Framework\Registry $registry          Registry.
     * @param array                       $meta              Meta
     * @param array                       $data              Data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $registry,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->registry = $registry;

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (!isset($this->loadData)) {
            $this->loadData = [];
            if ($retailerIds = $this->getRetailerIds()) {
                $this->registry->unregister('retailer_ids');
                $this->loadData = [
                    'retailer_ids' => json_encode($retailerIds),
                ];
            }
        }

        return $this->loadData;
    }

    /**
     * Get retailer ids
     *
     * @return int
     */
    private function getRetailerIds()
    {
        return $this->registry->registry('retailer_ids');
    }
}
