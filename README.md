## Smile Store Locator

This module is a plugin for [ElasticSuite](https://github.com/Smile-SA/elasticsuite).

This module adds a store locator to the website. You can search a retailer on map.

### Requirements

The module requires :

- [ElasticSuite](https://github.com/Smile-SA/elasticsuite) > 2.1.*
- [Retailer](https://github.com/Smile-SA/magento2-module-retailer) > 1.2.*
- [Map](https://github.com/Smile-SA/magento2-module-map) > 1.1.*

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

