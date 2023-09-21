<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Ui\Component\Retailer\Form;

use Magento\Framework\Registry;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Smile\Retailer\Model\ResourceModel\Retailer\CollectionFactory;

/**
 * Ui DataProvider controller.
 */
class DataProvider extends AbstractDataProvider
{
    protected array $loadData = [];

    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CollectionFactory $collectionFactory,
        private Registry $registry,
        array $meta = [],
        array $data = []
    ) {
        // extends from \Magento\Eav\Model\Entity\Collection\AbstractCollection
        // instead of \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
        // @phpstan-ignore-next-line - ignore for backward compatibility
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data.
     */
    public function getData(): array
    {
        $retailerIds = $this->getRetailerIds();
        if ($retailerIds) {
            $this->loadData = [
                'retailer_ids' => json_encode($retailerIds),
            ];
        }

        return $this->loadData;
    }

    /**
     * Get retailer ids.
     */
    private function getRetailerIds(): array
    {
        return $this->registry->registry('retailer_ids');
    }
}
