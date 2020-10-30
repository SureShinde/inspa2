define([
    'jquery',
    'underscore',
    'Mirasvit_PushNotification/js/lib/push'
], function ($, _, Push) {
    'use strict';
    
    return {
        subscription: null,
        
        isPermissionDenied: false,
        isSupported:        true,
        
        initialize: function () {
            _.bindAll(this, 'subscriptionUpdate', 'stateChangeListener');
            
            Push.init(this.stateChangeListener, this.subscriptionUpdate);
            
            this.registerServiceWorker();
        },
        
        /**
         * @returns {boolean}
         */
        registerServiceWorker: function () {
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register(window.PN_SERVICE_WORKER).then(function () {
                    if (window.PN_DEBUG) console.debug('Service Worker was registered');
                }).catch(function (err) {
                    console.debug('Unable to register Service Worker');
                    console.error(err);
                });
            } else {
                console.debug('Service Worker not Supported');
                return false;
            }
            
            return true;
        },
        
        /**
         * @returns {Promise}
         */
        subscribe: function () {
            var self = this;
            
            return new Promise(function (resolve, reject) {
                Push.subscribeDevice().then(function () {
                    resolve(self.subscription);
                }).catch(function (err) {
                    reject(err);
                });
            });
        },
        
        /**
         * @returns {boolean}
         */
        isSubscribed: function () {
            return this.subscription ? true : false;
        },
        
        stateChangeListener: function (state) {
            if (window.PN_DEBUG) console.debug(state.id);
            
            switch (state.id) {
                case 'PERMISSION_DENIED':
                    this.isPermissionDenied = true;
                    break;
                case 'UNSUPPORTED':
                    this.isSupported = false;
                    break;
            }
        },
        
        subscriptionUpdate: function (subscription) {
            if (window.PN_DEBUG) console.debug(subscription);
            
            if (!this.subscription) {
                this.subscription = subscription;
                this.ensureSubscription();
            }
        },
        
        isDenied: function () {
            return this.isPermissionDenied;
        },
        
        ensureSubscription: function () {
            if (window.PN_DEBUG) console.debug('ensureSubscription');
            $.ajax(window.PN_SUBSCRIBE_URL, {
                method: 'post',
                data:   {
                    'endpoint': this.subscription.endpoint
                }
            });
        }
    };
});