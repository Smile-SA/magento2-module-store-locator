define([
    'ko',
    'uiRegistry',
    'smile-map'
], function(ko, registry, SmileMap) {

    return SmileMap.extend({

        /**
         * Component Constructor
         */
        initialize: function () {
            this._super();
            this.fulltextSearch = '';
            this.observe(['fulltextSearch']);
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
        },

        /**
         * Event triggered by form#shop-search-form element into search.phtml
         * @see /templates/search.phtml
         * @returns {boolean}
         */
        updateMap: function() {
            if (!this.fulltextSearch() || this.fulltextSearch().trim().length === 0) {
                return false;
            }
            registry.get(this.name + '.geocoder', function (geocoder) {
                this.geocoder = geocoder;
                this.geocoder.fulltextSearch(this.fulltextSearch());
                this.geocoder.currentResult.subscribe(function (result) {
                    if (result && result.location) {
                        this.searchSuccess(
                            {coords: {latitude: result.location.lat, longitude: result.location.lng}},
                            this.fulltextSearch()
                        );
                    }
                }.bind(this));
                this.geocoder.onSearch();
            }.bind(this));
        },

        /**
         * Method to handle what you want on success
         *
         * @param position
         * @param query
         */
        searchSuccess: function(position, query) {
            // do what ever you want.
        }

    });
});
