/**
 * 2010-2020 Webkul.
 *
 * NOTICE OF LICENSE
 *
 * All right is reserved,
 * Please go through this link for complete license : https://store.webkul.com/license.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
 *
 *  @author    Webkul IN <support@webkul.com>
 *  @copyright 2010-2020 Webkul IN
 *  @license   https://store.webkul.com/license.html
 */


var wkPwaV = 'v001';
var dynamicCacheName = 'wk-ps-pwa-dynamic-' + wkPwaV;

// var staticCacheName = 'wk-ps-pwa-static-' + wkPwaV;
// var filesToCache = [
//     './',
// ];

self.addEventListener('install', function (e) {
    console.log('[ServiceWorker] Install');

    // e.waitUntil Delays the event until the Promise is resolved
    e.waitUntil(
        self.skipWaiting()
        // Open the cache
        // caches.open(staticCacheName).then(function(cache) {
        //     // Add all the default files to the cache
        //     // console.log('[ServiceWorker] Caching app shell');
        //     return cache.addAll(filesToCache);
        // }).then(function () {
        //     return self.skipWaiting();
        // })
    ); // end e.waitUntil
});

self.addEventListener('activate', function (e) {
    console.log('[ServiceWorker] Activate');
    e.waitUntil(
        // Get all the cache keys (keyList)
        caches.keys().then(function (keyList) {
            return Promise.all(keyList.map(function (key) {
                // If a cached item is saved under a previous cacheName
                // if (key !== staticCacheName && key !== dynamicCacheName) {
                if (key !== dynamicCacheName) {
                    // Delete that cached file
                    console.log('[ServiceWorker] Removing old cache', key);
                    return caches.delete(key);
                }
            }));
        })
    ); // end e.waitUntil
    return self.clients.claim();
});

self.addEventListener('fetch', function (event) {
    if (event.request.method == 'GET') {
        var requestUrl = new URL(event.request.url);
        if (requestUrl.pathname == '/wk-service-worker.js' || (requestUrl.pathname.indexOf('.mp4') > -1)) return;

        event.respondWith(
            caches.match(event.request).then(function (resp) {
                return fetch(event.request).then(function (response) {
                    return caches.open(dynamicCacheName).then(function (cache) {
                        if (response.ok == true) {
                            cache.put(event.request, response.clone());
                        }
                        return response;
                    });
                }).catch(function (rejectMsg) {
                    return resp || function () {
                        console.log("Error");
                    };
                });
            }).catch(function () {
                return caches.match('Error');
            })
        );
    }
});

self.addEventListener('message', function (event) {
    if (event.data.action === 'skipWaiting') {
        self.skipWaiting();
    }
});

function checkStatus(status) {
    return (status == 200) ? true : false;
}

self.addEventListener('push', function (event) {
    if (!(self.Notification && self.Notification.permission === 'granted')) {
        return;
    }
    if (event.data) {
        // console.log(`[Service Worker] Push had this data: "${event.data.text()}"`);
        if (event.data) {
            const notificationData = event.data.json();
            const options = {
                body: notificationData.body,
                icon: notificationData.icon,
                vibrate: [300, 100, 400],
                data: event.data.text(),
                badge: notificationData.badge
            };

            return event.waitUntil(
                self.registration.showNotification(notificationData.title, options)
            );
        };
    };
});

self.addEventListener('notificationclick', function (event) {
    const notificationData = JSON.parse(event.notification.data);
    const url = notificationData.target_url;
    if (url) {
        event.notification.close();
        if (typeof notificationData.identifier != 'undefined') {
            const notificationClickUrl = './modules/wkpwa/notificationClick.php?identifier=' + notificationData.identifier + '&targetId=' + notificationData.targetId;

            event.waitUntil(
                fetch(notificationClickUrl, {
                    mode: 'cors'
                }).then(function (response) {
                    if (response.status !== 200) {
                        console.log('Looks like there was a problem. Status Code: ' + response.status);
                        return;
                    }
                })
            );
        }

        event.waitUntil(
            Promise.all([
                clients.matchAll({
                    type: 'window'
                }).then(function (windowClients) {
                    if (clients.openWindow) {
                        return clients.openWindow(url);
                    }
                })
            ])
        );
    }
});
