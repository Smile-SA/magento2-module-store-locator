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
 * @author    Fanny DECLERCK <fadec@smile.fr>
 * @copyright 2020 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */

/*jshint browser:true jquery:true*/
/*global alert*/

define([
    'jquery',
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'uiRegistry',
    'mage/translate',
    'mage/template',
    ],
    function ($, Component, storage, registry, mageTranslate, mageTemplate) {

    "use strict";
     
    var retailer = storage.get('current-store');

    return Component.extend({
        xhrJson: null,

        /**
         * Component Constructor
         */
        initialize: function () {
            this._super();
            this.fulltextSearch = '';
            this.observe(['fulltextSearch']);
            this.autoComplete = $('#search_store_autocomplete');
            this.responseList = {
                indexList: null,
                selected: null
            };
            this.options = {
                autocomplete: 'off',
                responseFieldElements: 'ul li',
                url: $('#suggest_url').val(),
                template:
                '<li class="<%- data.row_class %>" id="qs-option-<%- data.index %>" role="option">' +
                '<span class="qs-option-name">' +
                ' <a class="store-url-link" href="<%- data.url %>"><%- data.title %></a>' +
                '</span>' +
                '</li>',
            }
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

        onKeydown: function (data, event) {
            if (event.keyCode == 13) {
                return this.onSubmit();
            }
            if (event.keyCode == 8) {
                var keepElt = this.fulltextSearch().split('').splice(0, this.fulltextSearch().trim().length-1).join('');
                this.fulltextSearch(keepElt);
            }
            if (event.keyCode == 46) {
                this.fulltextSearch('');
                this.autoComplete.hide();
            }

            if (event.key.length == 1 && /^[a-zA-Z0-9 ]+$/.test(event.key)) {
                this.fulltextSearch(this.fulltextSearch() + event.key);
            }

            if (this.fulltextSearch().trim().length > 2) {
                var searchField = $('#search_store'),
                    clonePosition = {
                        position: 'absolute',
                        width: searchField.outerWidth()
                    },
                    source = this.options.template,
                    template = mageTemplate(source),
                    dropdown = $('<ul role="listbox"></ul>'),
                    value = this.fulltextSearch();

                if (this.xhrJson !== null) this.xhrJson.abort();
                this.xhrJson = $.getJSON(this.options.url, {
                    q: value
                }, $.proxy(function (data) {
                    if (data.length) {
                        $.each(data, function (index, element) {
                            var html;

                            element.index = index;
                            html = template({
                                data: element
                            });
                            dropdown.append(html);
                        });

                        this._resetResponseList(true);

                        this.responseList.indexList = this.autoComplete.html(dropdown)
                            .css(clonePosition)
                            .show()
                            .find(this.options.responseFieldElements + ':visible');

                        this.responseList.indexList
                            .on('click', function (e) {
                                this.responseList.selected = $(e.currentTarget);
                                this.onSubmit();
                            }.bind(this));
                    } else {
                        this.autoComplete.hide();
                    }
                }, this));
            }
        },

        geolocationSuccess: function(position, query) {
            if (position.coords && position.coords.latitude && position.coords.longitude) {
                var url = this.storeLocatorHomeUrl;
                if (query !== undefined && query.trim !== "") {
                    url += '?query=' + query;
                }
                window.location.href =  url + "#" + position.coords.latitude + "," + position.coords.longitude;
            }
        },

        /**
         * Clears the item selected from the suggestion list and resets the suggestion list.
         * @private
         * @param {Boolean} all - Controls whether to clear the suggestion list.
         */
        _resetResponseList: function (all) {
            this.responseList.selected = null;

            if (all === true) {
                this.responseList.indexList = null;
            }
        }
    });
});
