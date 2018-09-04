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
namespace Smile\StoreLocator\Model\RetailerAddress\Source;

use Magento\Directory\Model\ResourceModel\Country\CollectionFactory as CountryCollectionFactory;
use Magento\Eav\Model\Entity\Attribute\Source\Table;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory;

/**
 * Country attribute source model.
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 * @author   Guillaume Vrac <guillaume.vrac@smile.fr>
 */
class Country extends Table
{
    /**
     * Countries factory.
     *
     * @var CountryCollectionFactory
     */
    protected $countriesFactory;

    /**
     * Constructor.
     *
     * @param CollectionFactory        $attrOptionCollectionFactory Attributes options Collection factory
     * @param OptionFactory            $attrOptionFactory           Attribute option factory
     * @param CountryCollectionFactory $countriesFactory            Countries Factory
     */
    public function __construct(
        CollectionFactory $attrOptionCollectionFactory,
        OptionFactory $attrOptionFactory,
        CountryCollectionFactory $countriesFactory
    ) {
        $this->countriesFactory = $countriesFactory;
        parent::__construct($attrOptionCollectionFactory, $attrOptionFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = $this->countriesFactory->create()
                ->toOptionArray();
        }

        return $this->_options;
    }
}
