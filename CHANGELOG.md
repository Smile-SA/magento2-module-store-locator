# Changelog

All notable changes to this project will be documented in this file.

## [2.2.5] - Unreleased
[2.2.5]: https://github.com/Smile-SA/magento2-module-store-locator/compare/2.2.4...2.2.5

- Supported platforms: Magento 2.4.4 and 2.4.5 only, PHP 8.1
- `composer.json` now states it: `php ^8.1` and `magento/framework >=103.0.4 <103.0.6` (previously `php ^8.1` and `magento/framework >=103.0.4`)
- Magento 2.4.6 and later and Mage-OS are supported by the 2.3.x line
- Fix `setup:di:compile` on Magento 2.4.4 and 2.4.5: native `mixed` and union types are removed from method signatures and kept in docblocks
- Require the Smile modules of the same line: `smile/module-map ~2.1.4`, `smile/module-retailer ~2.0.5`
- CI: static analysis on Magento 2.4.5, `setup:di:compile` on Magento 2.4.4 and 2.4.5

## [2.2.4] - 2026-11-24
[2.2.4]: https://github.com/Smile-SA/magento2-module-store-locator/compare/2.2.3...2.2.4

- Answer with a 404 page when the store, or the store of a contact form, cannot be found
- Require `smile/module-retailer` 2.0.4 or later

## [2.2.3] - 2026-07-22
[2.2.3]: https://github.com/Smile-SA/magento2-module-store-locator/compare/2.2.2...2.2.3

- Add missing translations (day of week, store status)
- Return type of `checkIdentifier` method changed from `int` to `int|bool` (returns false when no retailer is found)

## [2.2.2] - 2026-06-03
[2.2.2]: https://github.com/Smile-SA/magento2-module-store-locator/compare/2.2.1...2.2.2

- Remove dependency to unused jQuery UI components

## [2.2.1] - 2024-05-03
[2.2.1]: https://github.com/Smile-SA/magento2-module-store-locator/compare/2.2.0...2.2.1

- Refactor - same search design everywhere
- Update contact form

## [2.2.0] - 2023-09-20
[2.2.0]: https://github.com/Smile-SA/magento2-module-store-locator/compare/2.1.0...2.2.0

Dataset compatibility ES 2.11.x and PHP 8.2

- Fix Dynamic type declaration
- Fix Type hinting
- Replace `Zend_Date` by `DateTime`
- Remove `MutationObserver` support
- Fix UI Component Retailer Offer editing
- Replace `Zend_Validate` by `Laminas\Validator`
- Fix Retailer Grid Column Action
- Fix Retailer Grid Mass Actions
- Fix some translations
- Remove Temando/Shipping Plugin
