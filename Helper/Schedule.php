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

use IntlDateFormatter;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Locale\Resolver;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

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
     * @var Resolver
     */
    private $localeResolver;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * Schedule constructor.
     *
     * @param Context           $context        Application Context
     * @param Resolver          $localeResolver Locale Resolver
     * @param TimezoneInterface $timezone       Locale Format
     */
    public function __construct(
        Context $context,
        Resolver $localeResolver,
        TimezoneInterface $timezone
    ) {
        parent::__construct($context);

        $this->localeResolver = $localeResolver;
        $this->timezone = $timezone;
    }

    /**
     * Retrieve configuration used by schedule components
     *
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
     */
    private function getTimeFormat()
    {
        return $this->timezone->getTimeFormat(IntlDateFormatter::MEDIUM);
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
