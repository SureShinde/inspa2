'use strict';

var query = location.search.substr(1);
var options = {};
query.split("&").forEach(function (part) {
    var item = part.split("=");
    options[item[0]] = decodeURIComponent(item[1]);
});

self.addEventListener('install', function (evt) {
    evt.waitUntil(self.skipWaiting());
    console.log("The ServiceWorker was installed.");
});

self.addEventListener('activate', function (evt) {
    console.log("The ServiceWorker was activated.");
});

self.addEventListener('push', function (event) {
    console.log('Received a new push message', event);
    
    event.waitUntil(self.registration.pushManager.getSubscription().then(function (subscription) {
        console.log('Push sub data', subscription);
        
        if (subscription) {
            return fetch(options.url + '?endpoint=' + subscription.endpoint)
                .then(function (response) {
                    response.json().then(function (json) {
                        console.log('Push data received', json);
                        var promises = [];
                        for (var i = 0; i < json.messages.length; i++) {
                            var message = json.messages[i];
                            
                            message.data = message;
                            
                            promises.push(showNotification(message));
                        }
                        return Promise.all(promises);
                    })
                });
        } else {
            showNotification({"subject": "Push"})
        }
    }));
    
});

self.addEventListener('notificationclick', function (event) {
    console.log('Notification clicked: ', event);
    
    event.notification.close();
    
    var url = event.notification.data.url;
    var tag = event.notification.tag;
    
    fetch(options.url + '?tag=' + tag);
    
    event.waitUntil(
        clients.matchAll({
            type: 'window'
        })
            .then(function (windowClients) {
                for (var i = 0; i < windowClients.length; i++) {
                    var client = windowClients[i];
                    if (client.url === url && 'focus' in client) {
                        return client.focus();
                    }
                }
                if (clients.openWindow && url) {
                    return clients.openWindow(url);
                }
            })
    );
});

function showNotification(message) {
    return self.registration.showNotification(message['subject'], message);
}
