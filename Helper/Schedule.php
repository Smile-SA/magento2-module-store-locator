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
    private Resolver $localeResolver;

    /**
     * @var TimezoneInterface
     */
    private TimezoneInterface $localeDate;

    /**
     * Schedule constructor.
     *
     * @param Context           $context        Application Context
     * @param Resolver          $localeResolver Locale Resolver
     * @param TimezoneInterface $localeDate     Locale Format
     */
    public function __construct(
        Context $context,
        Resolver $localeResolver,
        TimezoneInterface $localeDate,
    ) {
        parent::__construct($context);

        $this->localeResolver = $localeResolver;
        $this->localeDate     = $localeDate;
    }

    /**
     * Retrieve configuration used by schedule components
     *
     * @return array
     */
    public function getConfig(): array
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
    private function getLocale(): null|string
    {
        return $this->localeResolver->getLocale();
    }

    /**
     * Retrieve Time Format
     *
     * @return string
     */
    private function getTimeFormat(): string
    {
        return $this->localeDate->getTimeFormat($this->getLocale());
    }

    /**
     * Return the date format used for schedule component
     *
     * @return string
     */
    private function getDateFormat(): string
    {
        return strtoupper(DateTime::DATE_INTERNAL_FORMAT);
    }

    /**
     * Retrieve default closing warning thresold, in minutes.
     *
     * @return int
     */
    private function getClosingWarningThresold(): int
    {
        return self::DEFAULT_WARNING_THRESOLD;
    }
}
