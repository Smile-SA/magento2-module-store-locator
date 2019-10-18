<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\StoreLocator
 * @author    Romain Ruaud <romain.ruaud@smile.fr>
 * @copyright 2017 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\StoreLocator\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Locale\Resolver;
use Magento\Framework\Stdlib\DateTime;

/**
 * Schedule Helper
 *
 * @category Smile
 * @package  Smile\StoreLocator
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
class Schedule extends AbstractHelper
{
    /**
     * Default delay (in minutes) before displaying the "Closing soon" message.
     */
    const DEFAULT_WARNING_THRESOLD = 60;

    /**
     * @var \Magento\Framework\Locale\Resolver
     */
    private $localeResolver;

    /**
     * @var \Zend_Locale_Format
     */
    private $localeFormat;

    /**
     * Schedule constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context        Application Context
     * @param \Magento\Framework\Locale\Resolver    $localeResolver Locale Resolver
     * @param \Zend_Locale_Format                   $localeFormat   Locale Format
     */
    public function __construct(
        Context $context,
        Resolver $localeResolver,
        \Zend_Locale_Format $localeFormat
    ) {
        parent::__construct($context);

        $this->localeResolver = $localeResolver;
        $this->localeFormat   = $localeFormat;
    }

    /**
     * Retrieve configuration used by schedule components
     *
     * @throws \Zend_Locale_Exception
     * @return array
     */
    public function getConfig()
    {
        return [
            'locale'                 => $this->getLocale(),
            'dateFormat'             => $this->getDateFormat(),
            'timeFormat'             => $this->getTimeFormat(),
            'closingWarningThresold' => $this->getClosingWarningThresold(),
        ];
    }

    /**
     * Retrieve current locale
     *
     * @return null|string
     */
    private function getLocale()
    {
        return $this->localeResolver->getLocale();
    }

    /**
     * Retrieve Time Format
     *
     * @return string
     * @throws \Zend_Locale_Exception
     */
    private function getTimeFormat()
    {
        return $this->localeFormat->getTimeFormat($this->localeResolver->getLocale());
    }

    /**
     * Return the date format used for schedule component
     *
     * @return string
     */
    private function getDateFormat()
    {
        return strtoupper(DateTime::DATE_INTERNAL_FORMAT);
    }

    /**
     * Retrieve default closing warning thresold, in minutes.
     *
     * @return int
     */
    private function getClosingWarningThresold()
    {
        return self::DEFAULT_WARNING_THRESOLD;
    }
}
