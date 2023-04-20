<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\StoreLocator
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\StoreLocator\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Smile\Map\Api\Data\GeoPointInterfaceFactory;
use Smile\StoreLocator\Api\Data\RetailerAddressInterface;
use Smile\Map\Api\Data\GeoPointInterface;

/**
 * Retailer address model.
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class RetailerAddress extends AbstractModel
{
    /**
     * @var GeoPointInterfaceFactory
     */
    private GeoPointInterfaceFactory $geoPointFactory;

    /**
     * Constructor.
     *
     * @param Context                   $context            Context.
     * @param Registry                  $registry           Registry.
     * @param GeoPointInterfaceFactory  $geoPointFactory    Geo point factory.
     * @param ?AbstractResource         $resource           Resource.
     * @param ?AbstractDb               $resourceCollection Collection resource.
     * @param array                     $data               Additional data.
     */
    public function __construct(
        Context $context,
        Registry $registry,
        GeoPointInterfaceFactory $geoPointFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->geoPointFactory = $geoPointFactory;
    }

    /**
     * @var string
     */
    const STREET_SEPARATOR = "\n";

    /**
     * {@inheritDoc}
     */
    public function getId(): mixed
    {
        return $this->getData(RetailerAddressInterface::ADDRESS_ID);
    }

    /**
     * Return the string formatted as an array containing several lines.
     *
     * @return string[]
     */
    public function getStreet(): array
    {
        return explode(self::STREET_SEPARATOR, $this->getData(RetailerAddressInterface::STREET));
    }

    /**
     * Returns coordinates as a GeoPoint.
     *
     * @return GeoPointInterface
     */
    public function getCoordinates(): GeoPointInterface
    {
        $coords = null;
        if ($this->hasLatitude() && $this->hasLongitude()) {
            $coordsArray = [
                GeoPointInterface::LATITUDE  => $this->getLatitude(),
                GeoPointInterface::LONGITUDE => $this->getLongitude(),
            ];
            $coords = $this->geoPointFactory->create($coordsArray);
        }

        return $coords;
    }

    /**
     * @SuppressWarnings(PHPMD.ShortVariable)
     *
     * {@inheritDoc}
     */
    public function setId($id): AbstractModel
    {
        return $this->setData(RetailerAddressInterface::ADDRESS_ID, $id);
    }

    /**
     * Populate the string field.
     *
     * @param string[]|string $street Street.
     *
     * @return RetailerAddress
     */
    public function setStreet(array|string $street): RetailerAddress
    {
        if (is_array($street)) {
            $street = implode(self::STREET_SEPARATOR, $street);
        }

        return $this->setData(RetailerAddressInterface::STREET, $street);
    }

    /**
     * Set latitude and longitude from coordinates.
     *
     * @param GeoPointInterface $coordinates Coordinates.
     *
     * @return $this
     */
    public function setCoordinates(GeoPointInterface $coordinates): self
    {
        $latitude  = $coordinates->getLatitude();
        $longitude = $coordinates->getLongitude();

        return $this->setLatitude($latitude)->setLongitude($longitude);
    }

    /**
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     *
     * {@inheritDoc}
     */
    protected function _construct(): void
    {
        $this->_init('Smile\StoreLocator\Model\ResourceModel\RetailerAddress');
    }
}
