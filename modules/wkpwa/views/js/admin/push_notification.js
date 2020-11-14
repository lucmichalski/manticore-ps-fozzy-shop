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

$(document).ready(function () {
    // NOTE:: JS copied from prestashop for notification icon upload
    $('#icon-selectbutton').click(function (e) {
        $('#icon').trigger('click');
    });

    $('#icon-name').click(function (e) {
        $('#icon').trigger('click');
    });

    $('#icon-name').on('dragenter', function (e) {
        e.stopPropagation();
        e.preventDefault();
    });

    $('#icon-name').on('dragover', function (e) {
        e.stopPropagation();
        e.preventDefault();
    });

    $('#icon-name').on('drop', function (e) {
        e.preventDefault();
        var files = e.originalEvent.dataTransfer.files;
        $('#icon')[0].files = files;
        $(this).val(files[0].name);
    });

    $('#icon').change(function (e) {
        if ($(this)[0].files !== undefined) {
            var files = $(this)[0].files;
            var name = '';

            $.each(files, function (index, value) {
                name += value.name + ', ';
            });

            $('#icon-name').val(name.slice(0, -2));
        } else // Internet Explorer 9 Compatibility
        {
            var name = $(this).val().split(/[\\/]/);
            $('#icon-name').val(name[name.length - 1]);
        }
    });

    if (typeof icon_max_files !== 'undefined') {
        $('#icon').closest('form').on('submit', function (e) {
            if ($('#icon')[0].files.length > icon_max_files) {
                e.preventDefault();
                alert('You can upload a maximum of  files');
            }
        });
    }
    // notification icon upload js ends here

    var elementSearchAjax = null;
    $("body").on('keyup', "#customer-suggestion-input", function () {
        var suggestionElement = $(this).siblings('#wk_customer_suggestion_cont');
        // Clear DOM Elements
        suggestionElement.hide().empty();
        $("#customer_type_idCustomer_value").val(0);

        if ($(this).val().trim().length) {
            var searchText = $(this).val().trim();

            if (elementSearchAjax) {
                elementSearchAjax.abort();
            }

            elementSearchAjax = $.ajax({
                url: pushNotificationContLink,
                type: 'POST',
                dataType: 'json',
                data: {
                    ajax: true,
                    action: 'customerSearch',
                    searchText: searchText,
                },
                success: function (result) {
                    var html = '';
                    if (Object.keys(result).length) {
                        $.each(result, function (index, element) {
                            html += "<li class='wk_customer_suggestion_list' data-id-customer='" + element.id_customer + "' data-customer-name='" + element.firstname + " " + element.lastname + "'>" + element.firstname + " " + element.lastname + " (" + element.email + ")" + "</li>"
                        });
                    } else {
                        html += "<li>" + noResultFound + "</li>";
                    }
                    if (html) {
                        suggestionElement.append(html).show();
                    }
                },
                error: function (error) {
                    console.log(error);
                }
            });
        }
    });

    $("body").on("click", ".wk_customer_suggestion_list", function () {
        $("#customer_type_idCustomer_value").val($(this).attr("data-id-customer"));
        $("#customer-suggestion-input").val($(this).attr("data-customer-name"));
        $("#wk_customer_suggestion_cont").hide().empty();
    });

    $(document).on("click", "body", function () {
        if ($('#wk_customer_suggestion_cont li').is(":visible")) {
            $("#wk_customer_suggestion_cont").hide().empty();
        }
    });

    $("#customer_type").on('change', function () {
        var customerType = parseInt($(this).val());
        displayCustomerTypeFileds(customerType);
    });
    displayCustomerTypeFileds(parseInt($("#customer_type").val()));

    // Schedule push time feature code Start here
    if ($(".wk_datepicker").length) {
        $(".wk_datepicker").datepicker({
            dateFormat: 'yy-mm-dd',
            beforeShow: function (input, instance) {
                var selectedDate = new Date($.datepicker.formatDate('yy-mm-dd', new Date()));
                selectedDate.setDate(selectedDate.getDate() + 1);
                $(".wk_datepicker").datepicker("option", "minDate", selectedDate);
            }
        });
    }

    $("input[name='schedule_push_switch']").on("change", function () {
        displaySchedulePushInput($(this).val());
    });
    if ($("input[name='schedule_push_switch']").length) {
        displaySchedulePushInput($("input[name='schedule_push_switch']:checked").val());
    }
    // Schedule push time feature code end here

    $(".wk-move-left").on("click", function () {
        var right_select = $(this).siblings("select");
        var right_option = right_select.find("option:selected");
        var left_select = right_select.parents("td").siblings("td").find("select");
        left_select.append(right_option);
    });

    $(".wk-move-right").on("click", function () {
        var left_select = $(this).siblings("select");
        var left_option = left_select.find("option:selected");
        var right_select = left_select.parents("td").siblings("td").find("select");
        right_select.append(left_option);
    });

    // Send Push Noitification
    $(".sendPushNotification").on('click', function () {
        var idPushNotification = $(this).attr('data-id-push-notification');
        var idElement = ($(this).attr('data-id-element') == 'undefined') ? 0 : $(this).attr('data-id-element');
        // var directSendNotificationLink = $(this).data('notification-direct-link');

        if (!$(".sendPushNotification.disablePushBtn").length) {
            $(".sendPushNotification").addClass('disablePushBtn');

            getTotalSubscriber(idPushNotification, idElement, function (totalSubscribers) {
                totalSubscribers = parseInt(totalSubscribers);
                if (totalSubscribers) {
                    // Reset Progress bar
                    resetPushNotificationBar();

                    $('#noti-subscriber-total').html(totalSubscribers);

                    // Send Push Notification
                    subscriberTokenExpireCount = 0;
                    sendPushNotification(totalSubscribers, idPushNotification, idElement);
                } else {
                    showErrorMessage(noSubscriberError);
                    resetPushNotificationBar(0);
                }
            });
        } else {
            // Error if push button is clicked while other push notification button is clicked
            showErrorMessage(anotherPushProcessError);
        }
    });
});

var getTotalSubscribers = '';
var sendPushNotificationAjax = '';
var subscriberTokenExpireCount = 0;
function getTotalSubscriber(idPushNotification, idElement = 0, callback) {
    getTotalSubscribers = $.ajax({
        url: sendNotificationContLink,
        type: 'POST',
        dataType: 'text',
        data: {
            ajax: true,
            action: 'getTotalSubscribers',
            idElement: idElement,
            idPushNotification: idPushNotification,
        },
        success: function (totalSubscribers) {
            callback(totalSubscribers);
        },
        error: function (error) {
            console.log(error);
        }
    });
}

function sendPushNotification(totalSubscribers, idPushNotification, idElement = 0, iteration = 0, idPushNotificationHistory = 0) {
    var dataSelectionLimit = 10;
    var startIndex = parseInt(iteration * dataSelectionLimit);

    sendPushNotificationAjax = $.ajax({
        url: sendNotificationContLink,
        type: 'POST',
        dataType: 'json',
        data: {
            ajax: true,
            action: 'sendPushNotification',
            // As we only select working subscriber (expired = 0). So, we need to exclude expired token count
            // from starting index as we have expired = 0 on each call of push notification API
            startIndex: parseInt(startIndex - parseInt(subscriberTokenExpireCount)),
            dataSelectionLimit: dataSelectionLimit,
            idElement: idElement,
            idPushNotification: idPushNotification,
            idPushNotificationHistory: idPushNotificationHistory,
        },
        success: function (result) {
            if (result.success) {
                if (!idPushNotificationHistory) {
                    idPushNotificationHistory = result.idPushNotificationHistory;
                }

                var percentage = 0;
                if (totalSubscribers > parseInt(startIndex + dataSelectionLimit)) {
                    percentage = ((parseInt(startIndex + dataSelectionLimit) / totalSubscribers) * 100);
                    percentage = Number(percentage.toFixed(1));
                } else {
                    percentage = 100;
                }
                subscriberTokenExpireCount += result.subscriberTokenExpireCount;

                $('#wk-notifcation-percent').html(percentage + '%');
                $('#wk-notifcation-progress-bar').css('width', percentage + '%');
                $('#noti-succes-total').html(result.notificationDeliveredCount);
                $('#noti-expire-total').html(subscriberTokenExpireCount);

                if (totalSubscribers > parseInt(startIndex + dataSelectionLimit)) {
                    iteration += 1;
                    sendPushNotification(
                        totalSubscribers,
                        idPushNotification,
                        idElement,
                        iteration,
                        idPushNotificationHistory
                    );
                } else {
                    setTimeout(function () {
                        // All Push Noitfication sent
                        // Reset push notification bar
                        resetPushNotificationBar(0);

                        // Final success message
                        $("#wk-notification-success-msg").html(successMsgPrefix + ' ' + result.notificationDeliveredCount + ' ' + successMsgSuffix);
                        $("#wk-notification-msg-cont").show();
                    }, 3000);
                }
            } else {
                // Show Error
                showErrorMessage(result.message);
                resetPushNotificationBar(0);
            }
        },
        error: function (error) {
            console.log(error);
        }
    });
}

function resetPushNotificationBar(forStart = 1) {
    $('#wk-notifcation-percent').html('0%');
    $('#wk-notifcation-progress-bar').css('width', '0%');
    $('#noti-subscriber-total').html(0);
    $('#noti-succes-total').html(0);
    $('#noti-expire-total').html(0);
    if (parseInt(forStart)) {
        $("#wk-notification-msg-cont").hide();

        $('#wk-notifcation-cont').show();
    } else {
        // re-enable the push notification button
        $(".sendPushNotification").removeClass('disablePushBtn');

        $('#wk-notifcation-cont').hide();
    }
}

function displayCustomerTypeFileds(customerType) {
    $(".customer-type-option-fields").hide();
    if (customerType > 0) {
        if (customerType == 1) {
            $("#customer-type-group").show();
        } else if (customerType == 2) {
            $("#customer-type-particular-customer").show();
        }
    }
}

function displaySchedulePushInput(pushSwitchValue) {
    if (parseInt(pushSwitchValue)) {
        $("#schedule-push-input-wrapper").show();
    } else {
        $("#schedule-push-input-wrapper").hide();
    }
}
