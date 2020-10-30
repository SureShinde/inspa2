define([], function () {
    'use strict';
    
    return {
        stateChangeCallback:        null,
        subscriptionUpdateCallback: null,
        
        state: {
            UNSUPPORTED:          {
                id:          'UNSUPPORTED',
                interactive: false,
                pushEnabled: false
            },
            INITIALISING:         {
                id:          'INITIALISING',
                interactive: false,
                pushEnabled: false
            },
            PERMISSION_DENIED:    {
                id:          'PERMISSION_DENIED',
                interactive: false,
                pushEnabled: false
            },
            PERMISSION_GRANTED:   {
                id:          'PERMISSION_GRANTED',
                interactive: true
            },
            PERMISSION_PROMPT:    {
                id:          'PERMISSION_PROMPT',
                interactive: true,
                pushEnabled: false
            },
            ERROR:                {
                id:          'ERROR',
                interactive: false,
                pushEnabled: false
            },
            STARTING_SUBSCRIBE:   {
                id:          'STARTING_SUBSCRIBE',
                interactive: false,
                pushEnabled: true
            },
            SUBSCRIBED:           {
                id:          'SUBSCRIBED',
                interactive: true,
                pushEnabled: true
            },
            STARTING_UNSUBSCRIBE: {
                id:          'STARTING_UNSUBSCRIBE',
                interactive: false,
                pushEnabled: false
            },
            UNSUBSCRIBED:         {
                id:          'UNSUBSCRIBED',
                interactive: true,
                pushEnabled: false
            }
        },
        
        init: function (stateChangeCallback, subscriptionUpdateCallback) {
            this.stateChangeCallback = stateChangeCallback;
            this.subscriptionUpdateCallback = subscriptionUpdateCallback;
            
            if (!('serviceWorker' in navigator)) {
                this.stateChangeCallback(
                    this.state.UNSUPPORTED,
                    'Service worker not available on this browser'
                );
                return;
            }
            
            if (!('PushManager' in window)) {
                this.stateChangeCallback(
                    this.state.UNSUPPORTED,
                    'PushManager not  available on this browser'
                );
                return;
            }
            
            if (!('showNotification' in ServiceWorkerRegistration.prototype)) {
                this.stateChangeCallback(
                    this.state.UNSUPPORTED,
                    'Showing Notifications  from a service worker is not available on this browser'
                );
                return;
            }
            
            navigator.serviceWorker.ready.then(function () {
                this.stateChangeCallback(this.state.INITIALISING);
                this.setUpPushPermission();
            }.bind(this));
        },
        
        setUpPushPermission: function () {
            this.permissionStateChange(Notification.permission);
            
            return navigator.serviceWorker.ready.then(function (serviceWorkerRegistration) {
                // Let's see if we have a subscription already
                return serviceWorkerRegistration.pushManager.getSubscription();
            }).then(function (subscription) {
                if (!subscription) {
                    // NOOP since we have no subscription and the permission state
                    // will inform whether to enable or disable the push UI
                    return;
                }
                
                this.stateChangeCallback(this.state.SUBSCRIBED);
                
                // Update the current state with the subscription id and endpoint
                this.subscriptionUpdateCallback(subscription);
            }.bind(this)).catch(function (err) {
                this.stateChangeCallback(this.state.ERROR, err);
            }.bind(this));
        },
        
        permissionStateChange: function (permissionState) {
            // If the notification permission is denied, it's a permanent block
            switch (permissionState) {
                case 'denied':
                    this.stateChangeCallback(this.state.PERMISSION_DENIED);
                    break;
                case 'granted':
                    this.stateChangeCallback(this.state.PERMISSION_GRANTED);
                    break;
                case 'default':
                    this.stateChangeCallback(this.state.PERMISSION_PROMPT);
                    break;
                default:
                    console.error('Unexpected permission state: ', permissionState);
                    break;
            }
        },
        
        subscribeDevice: function () {
            this.stateChangeCallback(this.state.STARTING_SUBSCRIBE);
            
            return new Promise(function (resolve, reject) {
                if (Notification.permission === 'denied') {
                    return reject(new Error('Push messages are blocked.'));
                }
                
                if (Notification.permission === 'granted') {
                    return resolve();
                }
                
                if (Notification.permission === 'default') {
                    Notification.requestPermission(function (result) {
                        if (result !== 'granted') {
                            reject(new Error('Bad permission result'));
                        }
                        
                        resolve();
                    });
                }
            }).then(function () {
                // We need the service worker registration to access the push manager
                return navigator.serviceWorker.ready.then(function (serviceWorkerRegistration) {
                    return serviceWorkerRegistration.pushManager.subscribe({
                        userVisibleOnly: true
                    });
                }).then(function (subscription) {
                    this.stateChangeCallback(this.state.SUBSCRIBED);
                    this.subscriptionUpdateCallback(subscription);
                }.bind(this)).catch(function (subscriptionErr) {
                    this.stateChangeCallback(this.state.ERROR, subscriptionErr);
                }.bind(this));
            }.bind(this)).catch(function () {
                // Check for a permission prompt issue
                this.permissionStateChange(Notification.permission);
            }.bind(this));
        },
        
        unsubscribeDevice: function () {
            // Disable the switch so it can't be changed while
            // we process permissions
            
            this.stateChangeCallback(this.state.STARTING_UNSUBSCRIBE);
            
            navigator.serviceWorker.ready.then(function (serviceWorkerRegistration) {
                return serviceWorkerRegistration.pushManager.getSubscription();
            }).then(function (pushSubscription) {
                // Check we have everything we need to unsubscribe
                if (!pushSubscription) {
                    this.stateChangeCallback(this.state.UNSUBSCRIBED);
                    this.subscriptionUpdateCallback(null);
                    return;
                }
                
                // You should remove the device details from the server
                // i.e. the  pushSubscription.endpoint
                return pushSubscription.unsubscribe().then(function (successful) {
                    if (!successful) {
                        // The unsubscribe was unsuccessful, but we can
                        // remove the subscriptionId from our server
                        // and notifications will stop
                        // This just may be in a bad state when the user returns
                        console.error('We were unable to unregister from push');
                    }
                });
            }.bind(this)).then(function () {
                this.stateChangeCallback(this.state.UNSUBSCRIBED);
                this.subscriptionUpdateCallback(null);
            }.bind(this)).catch(function (err) {
                console.error('Error thrown while revoking push notifications.  Most likely because push was never registered', err);
            });
        }
    }
});