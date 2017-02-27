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

define(['jquery', 'uiComponent', 'Magento_Customer/js/customer-data', 'mage/translate'], function ($, Component, storage) {

    "use strict";
     
    var retailer = storage.get('current-store');

    return Component.extend({

        /**
         * Component Constructor
         */
        initialize: function () {
            this._super();
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

        getStoreName : function () {
           return retailer().name;
        },

        getStoreAddress: function () {
           return retailer().address;
        },

        geolocalize: function(element) {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(this.setPosition);
            } else {
                element.innerHTML = "Geolocation is not supported by this browser.";
            }
        },

        onSubmit: function() {
            if (!this.fulltextSearch() || this.fulltextSearch().trim().length === 0) {
                return false;
            }

            return true;
        }
    });
});