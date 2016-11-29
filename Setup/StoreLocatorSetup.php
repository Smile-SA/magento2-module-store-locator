<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade Smile Elastic Suite to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\StoreLocator
 * @author    Romain Ruaud <romain.ruaud@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\StoreLocator\Setup;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Smile\Retailer\Setup\RetailerSetup;
use Smile\StoreLocator\Api\Data\RetailerInterface;

/**
 * Store Locator Setup.
 * Creates all attributes used by the Store Locator.
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
class StoreLocatorSetup extends RetailerSetup
{
    /**
     * Default entities and attributes
     *
     * @return array
     */
    public function getDefaultEntities()
    {
        $entities = parent::getDefaultEntities();
        $entities[RetailerInterface::ENTITY]['attributes'] = array_merge(
            $entities[RetailerInterface::ENTITY]['attributes'],
            $this->getAttributes()
        );

        return $entities;
    }

    /**
     * Get the group definition
     *
     * @return array
     */
    public function getGroupsDefinition()
    {
        $parentGroups  = parent::getGroupsDefinition();
        $lastSortorder = max(array_values($parentGroups));

        $groups = [
            'Images'          => $lastSortorder + 10,
            'Contact'         => $lastSortorder + 20,
            'Address'         => $lastSortorder + 30,
            'Coordinates'     => $lastSortorder + 40,
        ];

        return array_merge($parentGroups, $groups);
    }

    /**
     * Get the attributes set definition for store locator
     *
     * @return array
     */
    public function getAttributeSetDefinition()
    {
        $retailerAttributesSet  = parent::getAttributeSetDefinition();
        $storeLocatorAttributes = [
            RetailerInterface::ATTRIBUTE_SET_RETAILER => [
                'Images' => [
                    RetailerInterface::IMAGE1,
                    RetailerInterface::IMAGE1_ALT,
                    RetailerInterface::IMAGE2,
                    RetailerInterface::IMAGE2_ALT,
                    RetailerInterface::IMAGE3,
                    RetailerInterface::IMAGE3_ALT,
                ],
                'Contact' => [
                    RetailerInterface::CONTACT_FIRSTNAME,
                    RetailerInterface::CONTACT_LASTNAME,
                    RetailerInterface::CONTACT_EMAIL,
                    RetailerInterface::CONTACT_PHONE,
                ],
                'Address' => [
                    RetailerInterface::STREET,
                    RetailerInterface::POSTCODE,
                    RetailerInterface::CITY,
                    RetailerInterface::COUNTRY_ID,
                ],
                'Coordinates' => [
                    RetailerInterface::LATITUDE,
                    RetailerInterface::LONGITUDE,
                ],
            ],
        ];

        return array_merge_recursive($retailerAttributesSet, $storeLocatorAttributes);
    }

    /**
     * Retrieve store locator specific attributes
     *
     * @return array
     */
    private function getAttributes()
    {
        return array_merge(
            $this->getContactAttributes(),
            $this->getAddressAttributes(),
            $this->getCoordinatesAttributes(),
            $this->getImagesAttributes()
        );
    }

    /**
     * Get Store Locator Images attributes data
     *
     * @return array
     */
    private function getImagesAttributes()
    {
        return [
            RetailerInterface::IMAGE1 => [
                'label'         => 'Image 1',
                'type'          => 'varchar',
                'input'         => 'image',
                'backend'       => 'Smile\StoreLocator\Model\Retailer\Attribute\Backend\Image',
                'required'      => false,
                'global'        => ScopedAttributeInterface::SCOPE_STORE,
            ],
            RetailerInterface::IMAGE1_ALT => [
                'label'         => 'Image 1 Alt',
                'type'          => 'varchar',
                'input'         => 'text',
                'required'      => false,
                'global'        => ScopedAttributeInterface::SCOPE_STORE,
            ],
            RetailerInterface::IMAGE2 => [
                'label'         => 'Image 2',
                'type'          => 'varchar',
                'input'         => 'image',
                'backend'       => 'Smile\StoreLocator\Model\Retailer\Attribute\Backend\Image',
                'required'      => false,
                'global'        => ScopedAttributeInterface::SCOPE_STORE,
            ],
            RetailerInterface::IMAGE2_ALT => [
                'label'         => 'Image 2 Alt',
                'type'          => 'varchar',
                'input'         => 'text',
                'required'      => false,
                'global'        => ScopedAttributeInterface::SCOPE_STORE,
            ],
            RetailerInterface::IMAGE3 => [
                'label'         => 'Image 3',
                'type'          => 'varchar',
                'input'         => 'image',
                'backend'       => 'Smile\StoreLocator\Model\Retailer\Attribute\Backend\Image',
                'required'      => false,
                'global'        => ScopedAttributeInterface::SCOPE_STORE,
            ],
            RetailerInterface::IMAGE3_ALT => [
                'label'         => 'Image 3 Alt',
                'type'          => 'varchar',
                'input'         => 'text',
                'required'      => false,
                'global'        => ScopedAttributeInterface::SCOPE_STORE,
            ],
        ];
    }

    /**
     * Get Store Locator contact attributes data.
     *
     * @return array
     */
    private function getContactAttributes()
    {
        return [
            RetailerInterface::CONTACT_FIRSTNAME => [
                'label'         => 'Firstname',
                'type'          => 'varchar',
                'input'         => 'text',
                'required'      => false,
                'sort_order'    => 10,
                'global'        => ScopedAttributeInterface::SCOPE_STORE,
            ],
            RetailerInterface::CONTACT_LASTNAME => [
                'label'         => 'Lastname',
                'type'          => 'varchar',
                'input'         => 'text',
                'required'      => false,
                'sort_order'    => 20,
                'global'        => ScopedAttributeInterface::SCOPE_STORE,
            ],
            RetailerInterface::CONTACT_EMAIL => [
                'label'         => 'Email',
                'type'          => 'varchar',
                'input'         => 'text',
                'required'      => false,
                'sort_order'    => 30,
                'global'        => ScopedAttributeInterface::SCOPE_STORE,
            ],
            RetailerInterface::CONTACT_PHONE => [
                'label'         => 'Phone Number',
                'type'          => 'varchar',
                'input'         => 'text',
                'required'      => false,
                'sort_order'    => 40,
                'global'        => ScopedAttributeInterface::SCOPE_STORE,
            ],
        ];
    }

    /**
     * Get store locator address attributes
     *
     * @return array
     */
    private function getAddressAttributes()
    {
        return [
            RetailerInterface::STREET => [
                'label'         => 'Street',
                'type'          => 'varchar',
                'input'         => 'text',
                'required'      => true,
                'global'        => ScopedAttributeInterface::SCOPE_STORE,
            ],
            RetailerInterface::POSTCODE => [
                'label'         => 'Postcode',
                'type'          => 'varchar',
                'input'         => 'text',
                'required'      => false,
                'global'        => ScopedAttributeInterface::SCOPE_STORE,
            ],
            RetailerInterface::CITY => [
                'label'         => 'City',
                'type'          => 'varchar',
                'input'         => 'text',
                'required'      => true,
                'global'        => ScopedAttributeInterface::SCOPE_STORE,
            ],
            RetailerInterface::COUNTRY_ID => [
                'label'         => 'Country',
                'type'          => 'varchar',
                'input'         => 'select',
                'source'        => 'Smile\StoreLocator\Model\Retailer\Attribute\Source\Country',
                'required'      => true,
                'global'        => ScopedAttributeInterface::SCOPE_GLOBAL,
            ],
        ];
    }

    /**
     * Get store locator attributes data
     *
     * @return array
     */
    private function getCoordinatesAttributes()
    {
        return [
            RetailerInterface::LATITUDE => [
                'label'         => 'Latitude',
                'type'          => 'varchar',
                'input'         => 'text',
                'required'      => true,
                'global'        => ScopedAttributeInterface::SCOPE_GLOBAL,
            ],
            RetailerInterface::LONGITUDE => [
                'label'         => 'Longitude',
                'type'          => 'varchar',
                'input'         => 'text',
                'required'      => true,
                'global'        => ScopedAttributeInterface::SCOPE_GLOBAL,
            ],
        ];
    }
}
