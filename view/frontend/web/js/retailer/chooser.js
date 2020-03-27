/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 *
 * @category  Smile
 * @package   Smile\Retailer
 * @author    Aurelien FOUCRET <aurelien.foucret@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */

/*jshint browser:true jquery:true*/
/*global alert*/

define(['jquery', 'uiComponent', 'Magento_Customer/js/customer-data', 'uiRegistry', 'mage/translate'], function ($, Component, storage, registry) {

    "use strict";

    var retailer = storage.get('current-store');

    return Component.extend({

        /**
         * Component Constructor
         */
        initialize: function () {
            this._super();
            this.fulltextSearch = '';
            this.observe(['fulltextSearch']);
        },

        hasStore : function () {
            return retailer().entity_id != null;
        },

        getLinkLabel : function () {
            var label = $.mage.__('Find a store ...');

            if (this.hasStore()) {
                label = $.mage.__('My store : %s').replace("%s", this.getStoreName());
            }

            return label;
        },

        getStoreUrl : function() {
            return retailer().url;
        },

        getStoreName : function () {
            return retailer().name;
        },

        getStoreAddress: function () {
            return retailer().address;
        },

        geolocalize: function() {
            registry.get(this.name + '.geocoder', function (geocoder) {
                this.geocoder = geocoder;
                this.geocoder.geolocalize(this.geolocationSuccess.bind(this))
            }.bind(this));
        },

        onSubmit: function() {
            if (!this.fulltextSearch() || this.fulltextSearch().trim().length === 0) {
                return false;
            }
            registry.get(this.name + '.geocoder', function (geocoder) {
                this.geocoder = geocoder;
                this.geocoder.fulltextSearch(this.fulltextSearch());
                this.geocoder.currentResult.subscribe(function (result) {
                    if (result && result.location) {
                        this.geolocationSuccess({coords: {latitude: result.location.lat, longitude: result.location.lng}}, this.fulltextSearch());
                    }
                }.bind(this));
                this.geocoder.onSearch();
            }.bind(this));
        },

        geolocationSuccess: function(position, query) {
            if (position.coords && position.coords.latitude && position.coords.longitude) {
                var url = this.storeLocatorHomeUrl;
                if (query !== undefined && query.trim !== "") {
                    url += '?query=' + query;
                }
                window.location.href =  url + "#" + position.coords.latitude + "," + position.coords.longitude;
            }
        }
    });
});