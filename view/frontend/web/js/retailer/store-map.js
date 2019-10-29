define([
    'ko',
    'smile-map',
    'smile-map-markers',
    'smile-storelocator-store-collection'
], function(ko, SmileMap, Markers, StoreCollection){

    return SmileMap.extend({

        defaults: {  },

        initialize: function () {
            this._super();
        },

        /**
         * Init the list of markers.
         * Markers are a collection of Stores.
         */
        initMarkers: function() {
            var markersList = new StoreCollection({items : this.markers});
            this.markers = markersList.getList();
            this.markers.forEach(function(marker) {
                 marker.distance = ko.observable('');
                 marker.distanceBetween = ko.observable('');
                 marker.shopStatus = ko.observable('');
            });

            this.displayedMarkers = ko.observable(this.markers);
        },

        /**
         * Check is show copyright info.
         */
        isShowCopyrightInfo: function() {
            return ((this.provider == "osm") && (this.copyright_text != null) && (this.copyright_link != null));
        },

        /**
         * Get copyright text.
         */
        getCopyrightText: function() {
            return this.copyright_text;
        },

        /**
         * Get copyright link.
         */
        getCopyrightLink: function() {
            return this.copyright_link;
        }

    });
});
