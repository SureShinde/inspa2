define([
    'underscore',
    'jquery',
    'uiComponent',
    'Magento_Ui/js/modal/alert',
    'Mirasvit_PushNotification/js/subscriber'
], function (_, $, Component, alert, subscriber) {
    'use strict';
    
    return Component.extend({
        defaults: {
            template:   'Mirasvit_PushNotification/template/preview',
            previewUrl: '',
            
            imports: {
                title:   '${ $.provider }:data.title',
                subject: '${ $.provider }:data.subject',
                body:    '${ $.provider }:data.body',
                url:     '${ $.provider }:data.url',
                image:   '${ $.provider }:data.image',
                icon:    '${ $.provider }:data.icon'
            },
            
            listens: {
                icon:  'handleImage',
                image: 'handleImage'
            }
        },
        
        initialize: function () {
            this._super();
            
            return this;
        },
        
        initObservable: function () {
            this._super()
                .observe({
                    title:   '',
                    subject: '',
                    body:    '',
                    url:     '',
                    
                    image:     '',
                    imageUrl:  '',
                    imagePath: '',
                    
                    icon:     '',
                    iconUrl:  '',
                    iconPath: '',
                    
                    sendState: 0,
                    sendText:  'Send test push'
                });
            
            return this;
        },
        
        handleImage: function () {
            this.iconUrl('');
            this.iconPath('');
            
            if (this.icon()) {
                this.icon().map(function (icon) {
                    this.iconUrl(icon['url']);
                    this.iconPath(icon['name']);
                }, this);
            }
        },
        
        handleSend: function () {
            var self = this;
            
            this.sendState(1);
            this.sendText('Sending...');
            
            if (subscriber.isPermissionDenied) {
                this.sendText('Permission Denied');
                self.sendState(0);
                return;
            }
            
            if (!subscriber.isSupported) {
                this.sendText('Unsupported Browser');
                self.sendState(0);
                return;
            }
            
            subscriber.subscribe()
                .then(function (subscriber) {
                    $.ajax({
                        type:     "POST",
                        url:      self.previewUrl,
                        dataType: "json",
                        data:     {
                            title:    self.title(),
                            subject:  self.subject(),
                            body:     self.body(),
                            url:      self.url(),
                            image:    self.imagePath(),
                            icon:     self.iconPath(),
                            endpoint: subscriber.endpoint
                        }
                    }).done(function (data) {
                        self.sendState(2);
                        self.sendText('Push was sent');
                        setTimeout(function () {
                            self.sendText('Send test push');
                            self.sendState(0);
                        }, 3000);
                    })
                        .fail(function (e) {
                            alert({
                                content: e.status + " " + e.responseText
                            });
                            
                            self.sendText('Send test push');
                            self.sendState(0);
                        });
                }).catch(function (e) {
                console.log(e);
                
                alert({
                    content: e
                });
                
                self.sendText('Send test push');
                self.sendState(0);
            });
        }
    });
});
