define([
    'jquery',
    'uiComponent',
    'ko',
    'underscore',
    'Mirasvit_PushNotification/js/subscriber'
], function ($, Component, ko, _, subscriber) {
    'use strict';
    return Component.extend({
        defaults: {
            template:     'Mirasvit_PushNotification/prompt',
            container:    '.push-notification__prompt',
            localStorage: $.initNamespaceStorage('pushNotification').localStorage,
            
            prompt: {
                headline:    '',
                body:        '',
                accept_text: '',
                reject_text: '',
                delay:       0
            }
        },
        
        initialize: function () {
            var self = this;
            
            this._super();
            
            _.bindAll(this, 'handleDocumentClick', 'handleAccept', 'handleReject');
            
            if (!subscriber.isSupported) {
                return;
            }
            
            if (this.prompt.headline) {
                $(document).on('click', this.handleDocumentClick);
                
                setTimeout(function () {
                    if (subscriber.isSubscribed()) {
                        console.log('IS SUBSCRIBED');
                    } else if (subscriber.isDenied() === false && self.localStorage.get('status') !== 'reject') {
                        console.log('IS NOT SUBSCRIBED');
                        self.isVisible(true);
                    }
                    
                }, this.prompt.delay);
            } else {
                subscriber.subscribe();
            }
        },
        
        initObservable: function () {
            this._super()
                .observe('isVisible', false);
            
            return this;
        },
        
        handleDocumentClick: function (e) {
            if (!$(e.target).closest(this.container).length) {
                if (this.isVisible()) {
                    this.isVisible(false);
                }
            }
        },
        
        handleAccept: function (_, e) {
            var self = this;
            
            e.preventDefault();
            
            self.isVisible(false);
            subscriber.subscribe().then(function () {
            });
            
            this.localStorage.set('status', 'accept');
        },
        
        handleReject: function (_, e) {
            e.preventDefault();
            
            this.isVisible(false);
            
            this.localStorage.set('status', 'reject');
        }
    });
});