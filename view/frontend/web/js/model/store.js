define([
    'ko',
    'uiClass',
    'Smile_StoreLocator/js/model/store/schedule'
], function (ko, Class, Schedule) {
    'use strict';

    return Class.extend({

        initialize: function () {
            if (this.schedule) {
                this.schedule = new Schedule(this.schedule);
            }

            this._super().initObservable();

            return this;
        },

        initObservable: function () {
            if (this.schedule) {
                this.schedule = ko.observable(new Schedule(this.schedule));
            }

            return this;
        },

        getSchedule: function() {
            return this.schedule();
        }
    });
});
