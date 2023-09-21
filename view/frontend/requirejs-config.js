var config = {
    map: {
        '*': {
            'smile-storelocator-opening-hours': 'Smile_StoreLocator/js/retailer/opening-hours',
            'smile-storelocator-map': 'Smile_StoreLocator/js/retailer/store-map',
            'smile-storelocator-store': 'Smile_StoreLocator/js/model/store',
            'smile-storelocator-store-collection': 'Smile_StoreLocator/js/model/stores',
            'smile-storelocator-store-schedule': 'Smile_StoreLocator/js/model/store/schedule'
        }
    },
    config: {
        mixins: {
            'Smile_Map/js/map': {
                'Smile_StoreLocator/js/map-mixin': true
            }
        }
    }
};
