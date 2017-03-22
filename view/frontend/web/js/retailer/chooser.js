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
            this.initGeocoderBinding();
        },

        /**
         * Init the geocoding component binding
         */
        initGeocoderBinding: function() {
            registry.get(this.name + '.geocoder', function (geocoder) {
                this.geocoder = geocoder;
            }.bind(this));
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

        getStoreName : function () {
           return retailer().name;
        },

        getStoreAddress: function () {
           return retailer().address;
        },

        geolocalize: function(element) {
            this.geocoder.geolocalize(this.geolocationSuccess.bind(this))
        },

        onSubmit: function() {
            return !(!this.fulltextSearch() || this.fulltextSearch().trim().length === 0);
        },

        geolocationSuccess: function(position) {
            if (position.coords && position.coords.latitude && position.coords.longitude) {
                window.location.href = this.storeLocatorHomeUrl + "?lat=" + position.coords.latitude + "&long=" + position.coords.longitude;
            }
        }
    });
});
