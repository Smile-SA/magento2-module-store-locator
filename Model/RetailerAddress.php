<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Smile\Map\Api\Data\GeoPointInterface;
use Smile\Map\Api\Data\GeoPointInterfaceFactory;
use Smile\StoreLocator\Api\Data\RetailerAddressInterface;
use Smile\StoreLocator\Model\ResourceModel\RetailerAddress as RetailerAddressResource;

/**
 * Retailer address model.
 */
class RetailerAddress extends AbstractModel
{
    private const STREET_SEPARATOR = "\n";

    public function __construct(
        Context $context,
        Registry $registry,
        private GeoPointInterfaceFactory $geoPointFactory,
        ?AbstractResource $resource = null,
        ?AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(RetailerAddressResource::class);
    }

    /**
     * @inheritdoc
     */
    public function getId()
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
     * @inheritdoc
     */
    public function setId($value)
    {
        return $this->setData(RetailerAddressInterface::ADDRESS_ID, $value);
    }

    /**
     * Populate the string field.
     *
     * @param string[]|string $street
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
     */
    public function setCoordinates(GeoPointInterface $coordinates): self
    {
        $latitude  = $coordinates->getLatitude();
        $longitude = $coordinates->getLongitude();

        return $this->setLatitude($latitude)->setLongitude($longitude);
    }
}
