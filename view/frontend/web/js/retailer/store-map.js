define([
    'ko',
    'smile-map',
], function(ko, SmileMap){

    return SmileMap.extend({

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
