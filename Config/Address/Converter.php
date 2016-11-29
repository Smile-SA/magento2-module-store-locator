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

use Magento\Framework\Config\ConverterInterface;

/**
 * Address class converter.
 *
 * @category Smile
 * @package  Smile\LocalizedRetailer
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 * @author   Guillaume Vrac <guillaume.vrac@smile.fr>
 */
class Converter implements ConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function convert($source)
    {
        $output = [];

        /** @var $addressNode \DOMNode */
        foreach ($source->getElementsByTagName('format') as $addressNode) {
            $addressName = $this->getAttributeValue($addressNode, 'name');

            $output[$addressName] = [
                'name'  => $addressName,
                'lines' => [],
            ];

            foreach ($addressNode->getElementsByTagName('line') as $lineNode) {
                $line = [];

                foreach ($lineNode->getElementsByTagName('element') as $attributeNode) {
                    $attributeCode = $this->getAttributeValue($attributeNode, 'name');

                    $line[] = [
                        'name'      => $attributeCode,
                        'type'      => $this->getAttributeValue($attributeNode, 'type'),
                        'separator' => $this->getAttributeValue($attributeNode, 'separator', ' '),
                    ];
                }

                $output[$addressName]['lines'][] = $line;
            }
        }

        return $output;
    }

    /**
     * Get attribute value.
     *
     * @param \DOMNode    $node          The Dom Node
     * @param string      $attributeName The attribute Name
     * @param string|null $defaultValue  Default value
     *
     * @return string|null
     */
    protected function getAttributeValue(\DOMNode $node, $attributeName, $defaultValue = null)
    {
        $attributeNode = $node->attributes->getNamedItem($attributeName);
        $output = $defaultValue;
        if ($attributeNode) {
            $output = $attributeNode->nodeValue;
        }

        return $output;
    }
}
