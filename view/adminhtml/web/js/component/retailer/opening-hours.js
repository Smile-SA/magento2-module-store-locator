define([
    'Magento_Ui/js/form/components/html',
    'jquery'
], function (Component, $) {
    'use strict';

    return Component.extend({
        defaults: {
            value: {},
            links: {
                value: '${ $.provider }:${ $.dataScope }'
            },
            additionalClasses: "admin__fieldset"
        },

        /**
         * Initialize the component
         */
        initialize: function ()
        {
            this._super();
            this.initOpeningHoursListener();
        },

        /**
         * Init Observation on fields
         *
         * @returns {exports}
         */
        initObservable: function ()
        {
            this._super();
            this.openingHoursObject = {};
            this.observe('openingHoursObject value');

            return this;
        },

        /**
         * Init Observer on the opening hours fields.
         */
        initOpeningHoursListener: function ()
        {
            var observer = new MutationObserver(function () {
                var rootNode = document.getElementById(this.index);
                if (rootNode !== null) {
                    this.rootNode = document.getElementById(this.index);
                    observer.disconnect();
                    var openingHoursObserver = new MutationObserver(this.updateOpeningHours.bind(this));
                    var openingHoursObserverConfig = {childList:true, subtree: true, attributes: true};
                    openingHoursObserver.observe(rootNode, openingHoursObserverConfig);
                    this.updateOpeningHours();
                }
            }.bind(this));
            var observerConfig = {childList: true, subtree: true};
            observer.observe(document, observerConfig)
        },

        /**
         * Update value of the Opening Hours Object
         */
        updateOpeningHours: function ()
        {
            var openingHoursObject = {};
            var hashValues = [];

            $(this.rootNode).find("[name*=" + this.index + "]").each(function () {
                hashValues.push(this.name + this.value.toString());
                var currentOpeningHoursObject = openingHoursObject;

                var path = this.name.match(/\[([^[\[\]]+)\]/g)
                    .map(function (pathItem) { return pathItem.substr(1, pathItem.length-2); });

                while (path.length > 1) {
                    var currentKey = path.shift();

                    if (currentOpeningHoursObject[currentKey] === undefined) {
                        currentOpeningHoursObject[currentKey] = {};
                    }

                    currentOpeningHoursObject = currentOpeningHoursObject[currentKey];
                }

                currentKey = path.shift();
                currentOpeningHoursObject[currentKey] = $(this).val();
            });

            var newHashValue = hashValues.sort().join('');

            if (newHashValue !== this.currentHashValue) {
                if (this.currentHashValue !== undefined) {
                    this.bubble('update', true);
                }
                this.currentHashValue = newHashValue;
                this.openingHoursObject(openingHoursObject);

                this.value(openingHoursObject);
            }
        }
    })
});
