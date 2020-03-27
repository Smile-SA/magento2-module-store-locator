## Smile Store Locator

This module adds a store locator to the website. You can search a retailer on map.

### Requirements

The module requires :

- [Retailer](https://github.com/Smile-SA/magento2-module-retailer) > 1.2.*
- [Map](https://github.com/Smile-SA/magento2-module-map) > 2.0.*

### How to use

1. Install the module via Composer :

``` composer require smile/module-store-locator ```

2. Enable it

``` bin/magento module:enable Smile_StoreLocator ```

3. Install the module and rebuild the DI cache

``` bin/magento setup:upgrade ```

### How to configure

> Stores > Configuration > Services  > Smile Map > Map Settings

Maximum number of visible stores : Above this limit, the list of stores will be not display

### Add autocompletion

To add autocompletion you need to add this module :

[RetailerSearch](https://github.com/Smile-SA/magento2-module-retailer-elasticsuite-search)
/!\ be careful with dependencies