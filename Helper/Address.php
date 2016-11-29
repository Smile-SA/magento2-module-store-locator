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
namespace Smile\StoreLocator\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\DataObject;
use Smile\StoreLocator\Config\Address as AddressParser;

/**
 * Address config helper.
 *
 * @category Smile
 * @package  Smile\LocalizedRetailer
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 * @author   Guillaume Vrac <guillaume.vrac@smile.fr>
 */
class Address extends AbstractHelper
{
    /**#@+
     * Address formats.
     */
    const FORMAT_DEFAULT = 'default';
    const FORMAT_SHORT = 'short';
    /**#@-*/

    /**
     * Address config parser.
     *
     * @var AddressParser
     */
    protected $addressParser;

    /**
     * Constructor.
     *
     * @param Context          $context       Application Context
     * @param AddressInterface $addressParser Address Parser
     */
    public function __construct(Context $context, AddressParser $addressParser)
    {
        parent::__construct($context);
        $this->addressParser = $addressParser;
    }

    /**
     * Get a shop address as HTML.
     *
     * @param \Magento\Framework\DataObject $object The Data Object
     * @param string                        $format The format to apply
     *
     * @return string
     */
    public function getFormattedAddress(DataObject $object, $format = self::FORMAT_DEFAULT)
    {
        $html = '';
        $address = $this->addressParser->getFormat($format);

        if ($address) {
            foreach ($address['lines'] as $line) {
                $lineHtml = '';
                foreach ($line as $element) {
                    $value = null;

                    switch ($element['type']) {
                        case 'attribute':
                            $value = (string) $object->getData($element['name']);
                            break;

                        case 'method':
                            $method = $element['name'];
                            $value = (string) $object->$method();
                            break;
                    }

                    if ($value) {
                        $lineHtml .= $element['separator'];
                        $lineHtml .= '<span class="' . $element['name'] . '">';
                        $lineHtml .= $value;
                        $lineHtml .= '</span>';
                    }
                }

                if ($lineHtml !== '') {
                    $html .= $lineHtml . '<br>';
                }
            }
        }

        return $html;
    }
}
