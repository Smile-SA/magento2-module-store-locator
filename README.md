## Smile Store Locator

This module adds a store locator to the website. You can search a retailer on map.

### Compatibility

| Module version | Magento | Mage-OS | PHP |
| --- | --- | --- | --- |
| 2.3.x | 2.4.6 and later (tested up to 2.4.9) | 1.0.0 and later (tested up to 4.0) | 8.1 to 8.5, as supported by the platform |
| 2.2.x, from 2.2.5 | 2.4.4 and 2.4.5 | not available | 8.1 |

Releases up to 2.2.4 are not covered by this table: they declared `php ^8.1` and `magento/framework >=103.0.4`, and their method signatures use native `mixed` or union types, which the code generator of Magento 2.4.4 and 2.4.5 rejects (`setup:di:compile` fails).

### Requirements

The module requires:

- [Retailer](https://github.com/Smile-SA/magento2-module-retailer) >= 2.0.*
- [Map](https://github.com/Smile-SA/magento2-module-map) >= 2.1.*

### How to use

1. Install the module via Composer:

``` composer require smile/module-store-locator ```

2. Enable it:

``` bin/magento module:enable Smile_StoreLocator ```

3. Optionnal : Drop old SMILE_RETAILER_ADDRESS_RETAILER_ID unique key:

_if you already used older Smile_StoreLocator module on your projects, and you want to upgrade it,_
_before upgrading, you will have to DROP your current UNIQUE KEY from table smile_retailer_address : SMILE_RETAILER_ADDRESS_RETAILER_ID_
_This is necessary in order to get a db_schema.xml working correctly._

``` ALTER TABLE smile_retailer_address DROP INDEX SMILE_RETAILER_ADDRESS_RETAILER_ID ```

4. Install the module and rebuild the DI cache:

``` bin/magento setup:upgrade ```

### How to configure

> Stores > Configuration > Services  > Smile Map > Map Settings

Maximum number of visible stores: Above this limit, the list of stores will be not display

### Add autocompletion

To add autocompletion you need to add this module:

[RetailerSearch](https://github.com/Smile-SA/magento2-module-retailer-elasticsuite-search)
/!\ be careful with dependencies
