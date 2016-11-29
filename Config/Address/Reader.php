<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\LocalizedRetailer
 * @author    Romain Ruaud <romain.ruaud@smile.fr>
 * @author    Guillaume Vrac <guillaume.vrac@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\StoreLocator\Config\Address;

use Magento\Framework\Config\FileResolverInterface;
use Magento\Framework\Config\Reader\Filesystem;
use Magento\Framework\Config\ValidationStateInterface;
use Smile\StoreLocator\Config\Address\Converter;
use Smile\StoreLocator\Config\Address\SchemaLocator;

/**
 * Address file reader.
 *
 * @category Smile
 * @package  Smile\LocalizedRetailer
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 * @author   Guillaume Vrac <guillaume.vrac@smile.fr>
 */
class Reader extends Filesystem
{
    /**
     * Constructor.
     *
     * @param \Magento\Framework\Config\FileResolverInterface       $fileResolver     File Resolver
     * @param \Smile\LocalizedRetailer\Config\Address\Converter     $converter        Converter
     * @param \Smile\LocalizedRetailer\Config\Address\SchemaLocator $schemaLocator    Schema Locator
     * @param \Magento\Framework\Config\ValidationStateInterface    $validationState  Validation State
     * @param string                                                $fileName         The filename
     * @param array                                                 $idAttributes     Id Attributes
     * @param string                                                $domDocumentClass Document class
     * @param string                                                $defaultScope     Default scope
     */
    public function __construct(
        FileResolverInterface $fileResolver,
        Converter $converter,
        SchemaLocator $schemaLocator,
        ValidationStateInterface $validationState,
        $fileName = 'addresses.xml',
        $idAttributes = ['/config/format' => 'name'],
        $domDocumentClass = 'Magento\Framework\Config\Dom',
        $defaultScope = 'global'
    ) {
        parent::__construct(
            $fileResolver,
            $converter,
            $schemaLocator,
            $validationState,
            $fileName,
            $idAttributes,
            $domDocumentClass,
            $defaultScope
        );
    }
}
