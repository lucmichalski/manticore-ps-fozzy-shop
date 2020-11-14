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

function isMobileDevice() {
    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) && window.matchMedia('(display-mode: standalone)').matches) {
        return true;
    }
    return false;
}
let deferredInstallPrompt = null;
$(document).ready(function() {
    // Loader will be displayed when page reload in PWA app
    $(window).on('beforeunload', function () {
        if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) && window.matchMedia('(display-mode: standalone)').matches) {
            if (!$('#wk-loader').hasClass('wkLoader')) {
                $('#wk-loader').addClass('wkLoader');
            }
        }
    });

    window.addEventListener('online', function () {
        if (isMobileDevice()) {
            $('#wk-connection-msg').html(appOnline).addClass('wk-msgOnline-typography');
            $('#wk-site-connection').fadeIn("slow");
            setTimeout(function () {
                $('#wk-site-connection').fadeOut("slow");
                $('#wk-connection-msg').html('');
                $('#wk-connection-msg').removeClass('wk-msgOnline-typography');
            }, 5000);
        }
    });
    window.addEventListener('offline', function () {
        if (isMobileDevice()) {
            $('#wk-connection-msg').html(appOffline).addClass('wk-msgOffline-typography');
            $('#wk-site-connection').fadeIn("slow");
            setTimeout(function () {
                $('#wk-site-connection').fadeOut("slow");
                $('#wk-connection-msg').html('');
                $('#wk-connection-msg').removeClass('wk-msgOffline-typography');
            }, 5000);
        }
    });

    var swRegistration = null;
    var isSubscribed = false;
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register(serviceWorkerPath).then(function(registration) {
            swRegistration = registration;
            navigator.serviceWorker.ready.then(function (serviceWorkerRegistration) {
                if ('PushManager' in window) {
                    // Push is supported
                    if (parseInt(WK_PWA_PUSH_NOTIFICATION_ENABLE) && WK_PWA_APP_PUBLIC_SERVER_KEY) {
                        initialiseUI();
                    }
                }
            });
        })
        .catch(function(err) {
            console.log("Service Worker Failed to Register. Reason: ", err);
        })
    }

    function initialiseUI() {
        subscribeUser();
        // Set the initial subscription value
        swRegistration.pushManager.getSubscription()
            .then(function (subscription) {
                isSubscribed = !(subscription === null);
                updateBtn();
            });
    }

    function updateBtn() {
        if (Notification.permission === 'denied') {
            // console.log('Push Messaging Blocked.');
            updateSubscriptionOnServer(null);
            return;
        }

        return true;
    }

    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/\-/g, '+')
            .replace(/_/g, '/');

        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }

    function subscribeUser() {
        swRegistration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(WK_PWA_APP_PUBLIC_SERVER_KEY),
        })
        .then(function(subscription) {
            // console.log('User is subscribed.');
            updateSubscriptionOnServer(subscription);
            isSubscribed = true;
            updateBtn();
        })
        .catch(function(err) {
            console.log('Failed to subscribe the user: ', err);
            updateBtn();
        });
    }

    function updateSubscriptionOnServer(subscription) {
        // TODO: Send subscription to application server
        if (subscription) {
            // Subscribe code here
            saveTokenInServer(subscription);
        } else {
            // Unsubscribe code here
        }

        return true;
    }

    function saveTokenInServer(subscription) {
        var subscriberId = subscription.endpoint.split("/").slice(-1)[0];
        var endpoint = subscription.endpoint;
        var userPublicKey = subscription.getKey('p256dh');
        var userAuthToken = subscription.getKey('auth');
        userPublicKey = userPublicKey ? btoa(String.fromCharCode.apply(null, new Uint8Array(userPublicKey))) : null,
        userAuthToken = userAuthToken ? btoa(String.fromCharCode.apply(null, new Uint8Array(userAuthToken))) : null,

        $.ajax({
            url: clientTokenUrl,
            data: {
                token: subscriberId,
                endpoint: endpoint,
                userPublicKey: userPublicKey,
                userAuthToken: userAuthToken,
                action: 'addToken',
            },
            method: 'POST',
            dataType: 'json',
            success: function(result) {
                if (result.success) {
                    console.log("You have successfully subscribed for push notifications!");
                }
            }
        })
    }

    window.addEventListener('beforeinstallprompt', saveBeforeInstallPromptEvent);

    function saveBeforeInstallPromptEvent(evt) {
        deferredInstallPrompt = evt;
    }
});
function installPWA(evt = false) {
    if (evt) {
        evt.prompt();
    } else {
        deferredInstallPrompt.prompt();
    }
}