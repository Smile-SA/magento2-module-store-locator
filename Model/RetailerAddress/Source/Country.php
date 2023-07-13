<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Model\RetailerAddress\Source;

use Magento\Directory\Model\ResourceModel\Country\CollectionFactory as CountryCollectionFactory;
use Magento\Eav\Model\Entity\Attribute\Source\Table;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory;

/**
 * Country attribute source model.
 */
class Country extends Table
{
    public function __construct(
        CollectionFactory $attrOptionCollectionFactory,
        OptionFactory $attrOptionFactory,
        protected CountryCollectionFactory $countriesFactory
    ) {
        parent::__construct($attrOptionCollectionFactory, $attrOptionFactory);
    }

    /**
     * @inheritdoc
     */
    public function getAllOptions($withEmpty = true, $defaultValues = false): array
    {
        if ($this->_options === null) {
            $this->_options = $this->countriesFactory->create()
                ->loadByStore()
                ->toOptionArray();
        }

        return $this->_options;
    }
}
