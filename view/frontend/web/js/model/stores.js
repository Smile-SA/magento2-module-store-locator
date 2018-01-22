define([
    'ko',
    'uiClass',
    'Smile_StoreLocator/js/model/store'
], function (ko, Class, Store) {
    'use strict';

    return Class.extend({

        initialize: function () {
            this._super()
                .initObservable();

            return this;
        },

        initObservable: function () {

            this.items = ko.observableArray(ko.utils.arrayMap(this.items, function(store) {
                return new Store(store);
            }));

            return this;
        },

        getList: function() {
            return this.items();
        },

        filter: function (callback) {
            return callback(this.items());
        }
    });
});
