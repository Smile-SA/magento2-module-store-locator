<?php

declare(strict_types=1);

namespace Smile\StoreLocator\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Locale\Resolver;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Schedule Helper.
 */
class Schedule extends AbstractHelper
{
    /**
     * Default delay (in minutes) before displaying the "Closing soon" message.
     */
    private const DEFAULT_WARNING_THRESHOLD = 60;

    public function __construct(
        Context $context,
        private Resolver $localeResolver,
        private TimezoneInterface $localeDate,
    ) {
        parent::__construct($context);
    }

    /**
     * Retrieve configuration used by schedule components.
     */
    public function getConfig(): array
    {
        return [
            'locale' => $this->getLocale(),
            'dateFormat' => $this->getDateFormat(),
            'timeFormat' => $this->getTimeFormat(),
            'closingWarningThreshold' => $this->getClosingWarningThreshold(),
        ];
    }

    /**
     * Retrieve current locale.
     */
    private function getLocale(): string
    {
        return $this->localeResolver->getLocale();
    }

    /**
     * Retrieve Time Format.
     */
    private function getTimeFormat(): string
    {
        return $this->localeDate->getTimeFormat($this->getLocale());
    }

    /**
     * Return the date format used for schedule component.
     */
    private function getDateFormat(): string
    {
        return strtoupper(DateTime::DATE_INTERNAL_FORMAT);
    }

    /**
     * Retrieve default closing warning threshold, in minutes.
     */
    private function getClosingWarningThreshold(): int
    {
        return self::DEFAULT_WARNING_THRESHOLD;
    }
}
