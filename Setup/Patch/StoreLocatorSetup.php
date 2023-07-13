<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Setup\Patch;

use Magento\Catalog\Model\Category\Attribute\Backend\Image;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Validator\ValidateException;
use Smile\Retailer\Api\Data\RetailerInterface;
use Smile\Seller\Api\Data\SellerInterface;
use Smile\StoreLocator\Model\Retailer\Attribute\Backend\UrlKey;

class StoreLocatorSetup
{
    /**
     * Add 'url_key' attribute to Retailers.
     *
     * @throws LocalizedException
     * @throws ValidateException
     */
    public function addUrlKeyAttribute(EavSetup $eavSetup): void
    {
        $entityId = SellerInterface::ENTITY;
        $attrSetId = RetailerInterface::ATTRIBUTE_SET_RETAILER;
        $groupId = 'general';

        $eavSetup->addAttribute(
            SellerInterface::ENTITY_TYPE_CODE,
            'url_key',
            [
                'type' => 'varchar',
                'label' => 'URL Key',
                'input' => 'text',
                'required' => false,
                'user_defined' => true,
                'sort_order' => 3,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'backend' => UrlKey::class,
            ]
        );

        $eavSetup->addAttributeToGroup($entityId, $attrSetId, $groupId, 'url_key', 35);
    }

    /**
     * Add contact information (phone, mail, etc..) attribute to Retailers.
     *
     * @throws LocalizedException
     * @throws ValidateException
     */
    public function addContactInformation(EavSetup $eavSetup): void
    {
        $entityId = SellerInterface::ENTITY;
        $attrSetId = RetailerInterface::ATTRIBUTE_SET_RETAILER;
        $groupId = 'Contact';

        $eavSetup->addAttributeGroup($entityId, $attrSetId, $groupId, 150);

        $eavSetup->addAttribute(
            SellerInterface::ENTITY,
            'contact_phone',
            [
                'type' => 'varchar',
                'label' => 'Contact Phone number',
                'input' => 'text',
                'required' => false,
                'user_defined' => true,
                'sort_order' => 10,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
            ]
        );

        $eavSetup->addAttribute(
            SellerInterface::ENTITY,
            'contact_fax',
            [
                'type' => 'varchar',
                'label' => 'Contact Fax number',
                'input' => 'text',
                'required' => false,
                'user_defined' => true,
                'sort_order' => 20,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
            ]
        );

        $eavSetup->addAttribute(
            SellerInterface::ENTITY,
            'contact_mail',
            [
                'type' => 'varchar',
                'label' => 'Contact Mail',
                'input' => 'email',
                'required' => false,
                'user_defined' => true,
                'sort_order' => 30,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'frontend_class' => 'validate-email',
            ]
        );

        $eavSetup->addAttribute(
            SellerInterface::ENTITY,
            'show_contact_form',
            [
                'type' => 'int',
                'label' => 'Show contact form',
                'input' => 'boolean',
                'required' => true,
                'user_defined' => true,
                'sort_order' => 40,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'source' => Boolean::class,
            ]
        );

        $eavSetup->addAttributeToGroup($entityId, $attrSetId, $groupId, 'contact_phone', 10);
        $eavSetup->addAttributeToGroup($entityId, $attrSetId, $groupId, 'contact_fax', 20);
        $eavSetup->addAttributeToGroup($entityId, $attrSetId, $groupId, 'contact_mail', 30);
        $eavSetup->addAttributeToGroup($entityId, $attrSetId, $groupId, 'show_contact_form', 40);
    }

    /**
     * Update show_contact_form to Required.
     */
    public function setContactFormRequired(EavSetup $eavSetup): void
    {
        $eavSetup->updateAttribute(
            SellerInterface::ENTITY,
            'show_contact_form',
            'is_required',
            1
        );
    }

    /**
     * Add image attribute to Retailers.
     */
    public function addImage(EavSetup $eavSetup): void
    {
        $entityId  = SellerInterface::ENTITY;
        $attrSetId = RetailerInterface::ATTRIBUTE_SET_RETAILER;
        $groupId   = 'General';

        $eavSetup->addAttribute(
            SellerInterface::ENTITY,
            'image',
            [
                'type' => 'varchar',
                'label' => 'Media',
                'input' => 'image',
                'required' => false,
                'user_defined' => true,
                'sort_order' => 17,
                'backend_model' => Image::class,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
            ]
        );

        $eavSetup->addAttributeToGroup($entityId, $attrSetId, $groupId, 'image', 50);
    }
}
