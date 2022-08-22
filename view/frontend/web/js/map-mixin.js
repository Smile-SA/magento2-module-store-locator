define([
    'jquery',
    'leaflet',
    'ko',
    'smile-storelocator-store-collection',
    'Smile_StoreLocator/js/model/store/schedule',
    'jquery/ui',
    'mage/translate'
], function ($, L, ko, MarkersList, Schedule) {
    'use strict';

    var mixin = {

        markerHasDistance: false,

        /**
         * Init markers on the map
         */
        initMarkers: function() {
            this.markers = new MarkersList({items : this.markers}).getList();
            this.markers.forEach(function(marker) {
                marker.distance = ko.observable(0);
                marker.distanceBetween = ko.observable(0);
                marker.shopStatus = ko.observable(0);
                marker.closestShopsDisplay = ko.observable();
            });

            if(this.markers[0] && this.markers[0].closestShops) {
                this.markers[0].closestShops.forEach(function (marker) {
                    marker.distance = ko.observable(0);
                    marker.shopStatus = ko.observable(0);
                    marker.shopStatus = ko.observable(0);
                });
            }

            this.displayedMarkers = ko.observableArray(this.markers);
        },

        /**
         * Observe events on elements
         */
        observeElements: function() {
            this.observe([
                'markers',
                'displayedMarkers',
                'nearbyMarkers',
                'selectedMarker',
                'fulltextSearch',
                'distanceBetween',
                'shopStatus',
                'closestShopsDisplay'
            ]);
            this.markers.subscribe(this.loadMarkers.bind(this));
        },

        /**
         * Center the map on a given position
         *
         * @param position
         */
        applyPosition: function(position) {
            if( position === undefined || position.coords.longitude === undefined ) {
                this.displayedMarkers(this.markers());
            } else {
                var coords = new L.latLng(position.coords.latitude, position.coords.longitude);

                var isMarker = false;
                this.markers().forEach(function(marker) {
                    if (marker.latitude === position.coords.latitude && marker.longitude === position.coords.longitude) {
                        isMarker = marker;
                    }
                }, this);

                if (isMarker) {
                    this.currentBounds = this.initialBounds;
                    this.selectedMarker(isMarker);
                    this.refreshNearByMarkers(new L.latLng(isMarker.latitude, isMarker.longitude), true);
                    this.map.setView(coords, 14);
                } else {
                    this.map.setView(coords, 14);
                    this.currentBounds = this.map.getBounds();
                }

                this.setHashFromLocation(position);
            }
        },

        /**
         * Show user position and distance to each displayed markers,
         * if geolocation is 'true'
         *
         * @param position
         */
        displayPositionAndDistance: function(position) {
            if(position.coords.longitude !== undefined) {
                this.addMarkerWithMyPosition(position);
                this.applyDistanceBetween(position);
                this.changeDisplayList(
                    this.markers(),
                    new L.latLng(position.coords.latitude, position.coords.longitude)
                );
            }
        },

        /**
         * Add distance from position to marker
         *
         * @param position
         */
        applyDistanceBetween: function (position) {
            let coords = new L.latLng(position.coords.latitude, position.coords.longitude);

            this.markers().forEach(function (marker) {
                let itemPosition = new L.LatLng(marker.latitude, marker.longitude),
                    distanceFromCoords = itemPosition.distanceTo(coords),
                    result = (distanceFromCoords / 1000).toFixed(1);

                if(result === '0.0') {
                    result = (distanceFromCoords / 1000).toFixed(3) + ' m';
                } else {
                    result = (distanceFromCoords / 1000).toFixed(1) + ' km';
                }

                marker.distance(distanceFromCoords);
                marker.distanceBetween(result);
            });

            this.markerHasDistance = true;
        },

        /**
         * Change list markers relatively to position
         *
         * @param markers
         * @param bounds
         */
        changeDisplayList: function (markers, bounds) {
            let nearbyMarkers = markers;

            if (this.geocoder) {
                nearbyMarkers = this.geocoder.filterMarkersListByPositionRadius(markers, bounds);
            }

            this.displayedMarkers(nearbyMarkers);
        },

        /**
         * Geolocalize the user with geocoder and apply position to map.
         */
        geolocalize: function() {
            if (this.geocoder) {
                this.geocoder.geolocalize(this.applyPosition.bind(this));
                this.geocoder.geolocalize(this.displayPositionAndDistance.bind(this));
            }
        },

        /**
         * Load the markers and centers the map on them.
         */
        loadMarkers: function() {
            let markers = [],
                isMarkerCluster = this.marker_cluster === '1';

            this.markers().forEach(function(markerData) {
                let marker = this.generateMarker(markerData);
                if (!isMarkerCluster) {
                    marker.addTo(this.map);
                }

                markers.push(marker);
                markerData.shopStatus(this.prepareShopStatus(markerData));
            }.bind(this));

            let group = new L.featureGroup(markers);

            if (isMarkerCluster) {
                group = new L.markerClusterGroup();
                group.addLayers(markers);
                this.map.addLayer(group);
            }

            this.initialBounds = group.getBounds();
        },

        /**
         * Show the current status of shop
         *
         * @param markerData
         * @returns {string}
         */
        prepareShopStatus: function (markerData) {
            if(!markerData.nerby) {
                var schedule = markerData.getSchedule();
            } else {
                var schedule = markerData.schedule;
            }
            var isOpen = schedule.isOpenToday();
            var statusClass;
            if(!isOpen) {
                isOpen = 'Closed';
                statusClass = 'close-shop';
            } else {
                isOpen = 'Opened';
                statusClass = 'open-shop';
            }
            var time = schedule.getTodayCloseTime(isOpen);
            if(time === 'closeNow') {
                isOpen = 'closeNow';
                statusClass = 'close-shop';
                time = schedule.getTodayCloseTime(isOpen);
                isOpen = 'Closed';
            }
            var html = '<span class="'+ statusClass +'">'+ $.mage.__(isOpen) +'</span>';
            if (time) {
                html = html + ' - ' + $.mage.__('today') + ' ' + $.mage.__('until') + ' <span>'+ time +'</span>';
            }
            var openDay = schedule.getDayWhenStoreOpen();
            if (openDay) {
                html = html + '<span>'+ $.mage.__(openDay) +'</span>';
            }
            return html;
        },

        /**
         * Select a given marker
         *
         * @param marker
         */
        selectMarker: function(marker) {
            // Set current bounds before zooming in : to allow returning to these bounds after.
            if (!this.selectedMarker()) {
                this.currentBounds = this.map.getBounds();
            }

            this.selectedMarker(marker);
            var coords = new L.latLng(marker.latitude, marker.longitude);
            this.refreshNearByMarkers(coords);
            this.setHashFromLocation({coords : marker});
            this.map.setView(coords, 18);

        },

        /**
         * Refresh markers according to current bounds.
         */
        refreshDisplayedMarkers: function () {
            var bounds = this.map.getBounds();
            var displayedMarkers = this.filterMarkersByBounds(this.markers(), bounds);

            var zoom = this.map.getZoom();

            if (displayedMarkers.length === 0 && this.disabled_zoom_out !== '1') {
                zoom = zoom - 1;
                this.map.setZoom(zoom);
            }

            if (this.markerHasDistance) {
                displayedMarkers = displayedMarkers.sort(this.sortMarkersByDistance);
            }

            var position = this.getLocationFromHash();
            if(position === null) {
                this.displayedMarkers(displayedMarkers);
            } else if ( position === undefined || position.coords.longitude === undefined ) {
                this.displayedMarkers(this.markers());
            } else {
                this.displayedMarkers(displayedMarkers);
            }
        },


        /**
         * Add marker with user position to the map
         *
         * @param position
         */
        addMarkerWithMyPosition: function (position) {
            if (position && position.coords) {
                var positionMe = position;
                var markerd;
                var newLat = position.coords.latitude;
                var newLon = position.coords.longitude;
                var coords = new L.latLng(newLat, newLon);
                var markerOpt = L.divIcon({
                    iconSize: null,
                    html: '<div class="custum-lf-popup position my-position" data-lat="'+ newLat +'" data-lon="'+ newLon +'"><div class="button-decor"></div></div>'
                });

                if($('.position').length > 0) {
                    $('.position').parent().remove();
                }
                markerd = L.marker(coords, {icon: markerOpt}).addTo(this.map);
            }
        },

        /**
         * Close view store details
         */
        closeDetails: function () {
            this.resetSelectedMarker();
            this.resetBounds();
            this.geolocalize();
        },

        /**
         * Function for store-view-page.
         * For display closest shops to the current shop
         */
        closestShopDisplayRender: function () {
            var self = this;
            var lat = this.displayedMarkers()[0].latitude;
            var lon = this.displayedMarkers()[0].longitude;
            var bounds =  new L.latLng(lat, lon);
            var markers = this.displayedMarkers()[0].closestShops;
            if (this.geocoder) {
                var nearbyMarkers = this.geocoder.filterMarkersListByPositionRadius(markers, bounds);
                nearbyMarkers = nearbyMarkers.sort(function(a, b) {
                    var distanceA = ko.isObservable(a['distance']) ? a['distance']() : a['distance'],
                        distanceB = ko.isObservable(b['distance']) ? b['distance']() : b['distance'];
                    return ((distanceA < distanceB) ? - 1 : ((distanceA > distanceB) ? 1 : 0));
                });
            }
            nearbyMarkers.shift(0);
            if (nearbyMarkers.length > 3 ) {
                nearbyMarkers = nearbyMarkers.slice(0, 3);
            }
            nearbyMarkers.forEach(function (markerData) {
                markerData.schedule = new Schedule(markerData.schedule);
                markerData.nerby =  'nearby-shop';
                markerData.shopStatus(self.prepareShopStatus(markerData));
            });
            this.closestShopsDisplay(nearbyMarkers);
        },

        /**
         * Locate the map to current target.
         * Target = place name || postcode || city.
         */
        searchCurrentPlaces: function () {
            var coords, cityTarget, resultMarker;
            var resultArray = [];
            var searchTarget = $('#searchMarker').val();
            var nameRequest = parseInt(searchTarget.replace( /\D/g, '')) || 0;
            searchTarget = searchTarget.toLowerCase();
            searchTarget = searchTarget.trim();
            this.markers().forEach(function (marker) {
                var name = marker.name;
                var postCode = marker.postCode;
                var city = marker.city;
                var positionLan = marker.latitude;
                var positionLon = marker.longitude;
                name = name.toLowerCase();
                name = name.trim();
                city = city.toLowerCase();
                city = city.trim();
                if(searchTarget === name || searchTarget === postCode || searchTarget === city || searchTarget === name + ', ' + city) {
                    coords = new L.latLng(positionLan, positionLon);
                    if( searchTarget === city) {
                        cityTarget = city;
                    }
                    if(searchTarget === name + ', ' + city) {
                        resultMarker = marker;
                    }
                }
            });
            if(coords != undefined && nameRequest === 0 && cityTarget === undefined) {
                this.map.setView(coords, 17);
                resultArray.push(resultMarker);
                this.displayedMarkers(resultArray);
            } else if (coords === undefined) {
                alert('wrong required');
            } else {
                this.map.setView(coords, 12);
            }
        },

        /**
         * Create list for autocomplete in search field for markers.
         * @returns {[]}
         */
        markerAutocompleteBase: function () {
            var titlesListArr = [];
            this.markers().forEach(function (marker) {
                var name = marker.name;
                name = name.trim();
                var postCode = marker.postCode;
                var city = marker.city;
                if(!titlesListArr.includes(name)) {
                    titlesListArr.push(name + ', ' + city);
                }
                if(!titlesListArr.includes(postCode)) {
                    titlesListArr.push(postCode);
                }
                if(!titlesListArr.includes(city)) {
                    titlesListArr.push(city);
                }
            });
            return titlesListArr;
        },

        /**
         * Map search.
         */
        markerAutocompleteSearch: function () {
            var parrent = $('.shop-search .fulltext-search-wrapper .ui-widget');
            var markerInfoBase =  this.markerAutocompleteBase();
            $('#searchMarker').autocomplete({
                appendTo: parrent,
                minLength: 3,
                position: {
                    my: "left top",
                    at: "left bottom",
                    collision: "none"
                },
                source: markerInfoBase
            });
        },

        /**
         * Generate marker
         *
         * @param {Object} markerData
         * @return {Object}
         */
        generateMarker: function (markerData) {
            let currentMarker = [markerData.latitude, markerData.longitude],
                markerOptionLocator = L.divIcon({
                    iconSize: null,
                    html: this.getMarkerIconHtmlString(markerData)
                });

            return L.marker(currentMarker, {icon: markerOptionLocator});
        },

        /**
         * Get marker icon html string
         *
         * @param {Object} markerData
         * @returns {string}
         */
        getMarkerIconHtmlString: function (markerData) {
            let html = '<div class="custum-lf-popup" data-lat="' + markerData.latitude + '" data-lon="' +
                markerData.longitude + '" data-n="' + markerData.name + '"><div class="button-decor"></div>';

            if (typeof markerData.url !== 'undefined') {
                html += '<a href="' + markerData.url + '"></a>';
            }

            html += '</div>';

            return html;
        },

        /**
         * Sort markers by distance
         *
         * @param {Object} markerA
         * @param {Object} markerB
         *
         * @returns {Number}
         */
        sortMarkersByDistance: function (markerA, markerB) {
            let distanceA = ko.isObservable(markerA.distance) ? markerA.distance() : markerA.distance,
                distanceB = ko.isObservable(markerB.distance) ? markerB.distance() : markerB.distance;

            return ((distanceA < distanceB) ? - 1 : ((distanceA > distanceB) ? 1 : 0));
        }
    };

    return function (Component) {
        return Component.extend(mixin);
    }
});
