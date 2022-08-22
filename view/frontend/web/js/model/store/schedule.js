/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 *
 * @category  Smile
 * @package   Smile\StoreLocator
 * @author    Romain Ruaud <romain.ruaud@smile.fr>
 * @author    Ihor KVASNYTSKYI <ihor.kvasnytskyi@smile-ukraine.com>
 * @copyright 2019 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */

/*jshint browser:true jquery:true*/
/*global alert*/

define(['jquery', 'uiClass', 'moment', 'ko', 'mage/translate', 'mage/dropdown'], function ($, Component, moment, ko) {

    "use strict";

    return Component.extend({

        defaults: {
            dateOptions : {weekday: "long", year: "numeric", month: "long", day: "numeric"},
            openingHoursTemplate : 'Smile_StoreLocator/retailer/opening-hours',
            specialOpeningHoursTemplate : 'Smile_StoreLocator/retailer/special-opening-hours'
        },

        initialize: function() {
            this._super();
            this.initOpeningHoursList();
            this.initSpecialOpeningHoursList();
        },

        /**
         * Check if the store is open
         *
         * @returns {boolean}
         */
        isOpenToday : function () {
            var now   = new Date();
            var index = moment(now).format(this.dateFormat);
            if (this.calendar.hasOwnProperty(index)) {
                if (this.calendar[index].length > 0) {
                    return true;
                }
            }
        },

        /**
         * Check if the retailer is currently Open
         *
         * @returns {boolean}
         */
        isOpenNow: function() {
            var now   = new Date();
            var index = moment(now).format(this.dateFormat);

            var result = false;
            if (this.calendar.hasOwnProperty(index)) {
                this.calendar[index].forEach(function(openingTime) {
                    if (this.isCurrentTimeSlot(openingTime)) {
                        result = true;
                    }
                }, this);
            }

            return result;
        },

        /**
         * Retrieve close time of today
         *
         * @returns {string}
         */
        getTodayNextCloseTime : function () {
            var now   = new Date();
            var index = moment(now).format(this.dateFormat);

            var result = false;
            if (this.calendar.hasOwnProperty(index)) {
                this.calendar[index].forEach(function(openingTime) {
                    if (this.isCurrentTimeSlot(openingTime)) {
                        result = openingTime.end_time;
                    }
                }, this);
            }

            return result;
        },

        /**
         * Test a time slot to find if we are currently in
         *
         * @param timeSlot
         * @returns {boolean}
         */
        isCurrentTimeSlot: function (timeSlot) {
            var result = false;
            var now    = new Date();

            if (timeSlot.hasOwnProperty('start_time') && timeSlot.hasOwnProperty('end_time')) {
                var from = moment(timeSlot.start_time, [this.timeFormat]).toDate();
                var to = moment(timeSlot.end_time, [this.timeFormat]).toDate();

                if ((now.getTime() <= to.getTime() && now.getTime() >= from.getTime())) {
                    result = true;
                }
            }

            return result;
        },

        /**
         * Retrieve the opening hours for today
         *
         * @returns {boolean}
         */
        getTodayOpeningHours : function() {
            var now    = new Date();
            var index  = moment(now).format(this.dateFormat);
            var hours  = [];
            var result = false;

            if (this.calendar.hasOwnProperty(index)) {
                this.calendar[index].forEach(function(openingTimes) {
                    if (openingTimes.hasOwnProperty('start_time') && openingTimes.hasOwnProperty('end_time')) {
                        hours.push(openingTimes.start_time + ' - ' + openingTimes.end_time);
                    }
                }, this);

                result = hours.join();
            }

            return result;
        },


        /**
         * Get current time store closure.
         *
         * @param isOpen
         * @returns {string}
         */
        getTodayCloseTime: function (isOpen) {
            var now    = new Date();
            var currentTime = now.getTime();
            var index  = moment(now).format(this.dateFormat);

            var result = false;
            if (this.calendar.hasOwnProperty(index)) {
                var exist = this.calendar[index];
                if(exist.length == 0){
                    return result;
                }
                if(!exist.length || isOpen === 'closeNow'){
                    exist = this.getNextDayData();
                }
                if(exist) {
                    if(isOpen === 'Opened') {
                        var currentStatus = exist[exist.length - 1].end_time;
                        var currDate = moment(currentStatus, [this.timeFormat]).toDate();
                        var currDateTime = currDate.getTime();

                        if( currDateTime < currentTime ) {
                            result = 'closeNow';
                        } else {
                            result = exist[exist.length - 1].end_time;
                        }
                    } else  {
                        var openDay  = exist[exist.length - 1];
                        var openTime = exist[0][0].start_time;
                        result = openDay + ' ' + openTime;
                    }
                }
            }

            return result;
        },
        /**
         * Get current time and day opening store, if he is closed.
         *
         * @param isOpen
         * @returns []
         */
        getNextDayData: function (){
            var nextDate, day;
            var indexCurrNexDate;
            var weekDay = 7;
            var i = 1;
            var choiseNextDate = [];
            while(!choiseNextDate.length && weekDay > i) {
                if(indexCurrNexDate != null) {
                    nextDate = new Date(indexCurrNexDate)
                } else {
                    nextDate = new Date();
                }
                nextDate.setDate(nextDate.getDate() + 1);
                indexCurrNexDate = moment(nextDate).format(this.dateFormat);
                choiseNextDate = this.calendar[indexCurrNexDate];
                i++
            }
            if (i == 2) {
                day = $.mage.__('tomorrow');
            } else {
                day = this.getDayWhenStoreOpen(indexCurrNexDate);
            }

            return [choiseNextDate, day];
        },
        /**
         * Get current day opening store, if he is closed.
         *
         * @param indexCurrNexDate
         * @returns {}
         */
        getDayWhenStoreOpen: function (indexCurrNexDate) {
            if(!indexCurrNexDate) {
                var day = null;
                return day;
            } else {
                var nextDate = new Date(indexCurrNexDate);
                var options = {weekday: 'long'};
                var day = nextDate.toLocaleDateString('en-us', options);
            }
            return day;
        },

        /**
         * Check if the store will close soon
         *
         * @returns {boolean}
         */
        isNearlyClosed : function () {
            var closingTime = this.getTodayNextCloseTime();
            if (closingTime) {
                var now = new Date();
                var closing = moment(closingTime, [this.timeFormat]).toDate();

                if (Math.floor(((closing - now) / 1000) / 60) <= parseInt(this.closingWarningThresold, 10)) {
                    return true;
                }
            }

            return false;
        },

        /**
         * Retrieve link label
         *
         * @returns {string}
         */
        getLinkLabel : function () {
            if (this.isOpenToday()) {
                var label = $.mage.__('Open Today');
                var closeTime = this.getTodayNextCloseTime();
                var todayHours = this.getTodayOpeningHours();

                if (closeTime) {
                    label = $.mage.__('Open Today (%1)')
                    label = label.replace('%1', todayHours);
                }

                if (this.isNearlyClosed() && closeTime) {
                    label = $.mage.__('Closing soon (%1)')
                    label = label.replace('%1', closeTime);
                }

                if (!this.isOpenNow() && todayHours) {
                    label = $.mage.__('Closed (%1)')
                    label = label.replace('%1', todayHours);
                }

                return label;
            }

            return $.mage.__('Closed');
        },

        /**
         * Init Opening Hours list in an iterable form
         */
        initOpeningHoursList : function() {
            var list = [];
            for (var day in this.openingHours) if (this.openingHours.hasOwnProperty(day) && day != 0) {
                if (Array.isArray(this.openingHours[day])) {
                    var object = {
                        "day": this.getDayLabel(day),
                        "hours": this.extractOpeningTimes(this.openingHours[day])
                    };

                    list.push(object);
                }
            }

            if (this.openingHours.hasOwnProperty(0) && Array.isArray(this.openingHours[0])) {
                var sunday = {
                    "day": this.getDayLabel(0),
                    "hours": this.extractOpeningTimes(this.openingHours[0])
                };
                list.push(sunday);
            }

            this.openingHoursList = ko.observableArray(list);
        },

        /**
         * Retrieve Special Opening Hours list in an iterable form
         */
        initSpecialOpeningHoursList : function() {
            var list = [];
            for (var day in this.specialOpeningHours) if (this.specialOpeningHours.hasOwnProperty(day) && day != 0) {
                if (Array.isArray(this.specialOpeningHours[day])) {
                    var object = {
                        "day": moment(day, this.dateFormat).toDate().toLocaleString(
                            this.getLocale(),
                            this.dateOptions
                        ),
                        "hours": this.extractOpeningTimes(this.specialOpeningHours[day])
                    };

                    list.push(object);
                }
            }
            if (this.specialOpeningHours.hasOwnProperty(0) && Array.isArray(this.specialOpeningHours[0])) {
                var sunday = {
                    "day": this.getDayLabel(0),
                    "hours": this.extractOpeningTimes(this.specialOpeningHours[0])
                };
                list.push(sunday);
            }

            this.specialOpeningHoursList = ko.observableArray(list);
        },

        /**
         * Extract Opening hours for a given day
         *
         * @param item
         * @returns {string}
         */
        extractOpeningTimes: function(item) {
            var hours = [];

            if (Array.isArray(item)) {
                item.forEach(function (openingTimes) {
                    var stringHours = this.openingTimesToString(openingTimes);
                    hours.push(stringHours);
                }, this);
            }

            if (hours.length === 0) {
                hours.push($.mage.__('Closed'));
            }

            return hours.join(', ');
        },

        /**
         * Check if a day given in parameter is the current day
         *
         * @param day
         * @returns {boolean}
         */
        isCurrentDay: function (day) {
            var now = new Date();
            return this.getDayLabel(now.getDay()) === day ;
        },

        /**
         * Retrieve day label from the number of day
         *
         * @param dayOfWeek
         * @returns {string}
         */
        getDayLabel : function(dayOfWeek) {
            var date = new Date();
            var locale = this.getLocale();

            date.setDate(date.getDate() - date.getDay() + parseInt(dayOfWeek, 10));

            return $.mage.__(date.toLocaleString(locale, {weekday: 'long'}));
        },

        /**
         * Get current locale
         *
         * @returns {*}
         */
        getLocale : function() {
            return this.locale.replace(new RegExp('_', 'g'), "-");
        },

        /**
         * Return true if having special opening hours
         *
         * @returns {boolean}
         */
        hasSpecialOpeningHours : function() {
            return Object.keys(this.specialOpeningHours).length > 0;
        },

        /**
         * Convert timeslot object to string
         *
         * @param openingTime
         * @returns {string}
         */
        openingTimesToString : function (openingTime) {

            var result = '';

            if (openingTime.hasOwnProperty('start_time') && openingTime.hasOwnProperty('end_time')) {
                result = openingTime.start_time + ' - ' + openingTime.end_time;
            }

            return result;
        },

        initDropdown : function (element, component) {
            $('[data-role=openingHoursDropDown]').dropdownDialog({
                'appendTo': '[data-block=opening-hours-dropdown]',
                'triggerTarget': '.showopeninghours',
                'timeout': '2000',
                'closeOnMouseLeave': false,
                'closeOnClickOutside': false,
                'closeOnEscape': false,
                'triggerClass': 'active',
                'parentClass': 'active',
                'buttons': []
            });
        }
    });
});
