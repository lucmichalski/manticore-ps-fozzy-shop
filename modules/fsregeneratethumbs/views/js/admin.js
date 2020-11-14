/**
 *  2017 ModuleFactory.co
 *
 *  @author    ModuleFactory.co <info@modulefactory.co>
 *  @copyright 2017 ModuleFactory.co
 *  @license   ModuleFactory.co Commercial License
 */

var FSRT = FSRT || {};
FSRT.scrollExtra = 150;
FSRT.processIsPaused = false;
FSRT.processingType = null;
FSRT.processingFormat = null;
FSRT.processingCount = 0;
FSRT.processingHasMore = null;
FSRT.processingHasError = null;

$(document).ready(function(){
    $('#fsrt_type').change(function(){
        FSRT.imageTypeChangeCallback();
    });
});

FSRT.imageTypeChangeCallback = function() {
    var image_type = $('#fsrt_type').val();

    $('#fsrt_format').val('all');
    if (image_type != 'all') {
        $('#fsrt_format option').each(function() {
            var image_format_val = $(this).val();
            if (image_format_val != 'all') {
                $(this).hide();
                if (FSRT.isFormatEnabledForType(image_type, image_format_val)) {
                    $(this).show();
                }
            }
        });
    }
    else {
        $('#fsrt_format option').each(function() {
            $(this).show();
        });
    }
};

FSRT.isFormatEnabledForType = function(type, format) {
    for (i in FSRT.imageFormatsByType[type]) {
        format_obj = FSRT.imageFormatsByType[type][i];
        if (format_obj.name == format) {
            return true;
        }
    }
    return false;
};

FSRT.generateQueue = function() {
    FSRT.statusStart();
    $.ajax({
        url: FSRT.generateQueueUrl,
        type: 'GET',
        data: {
            fsrt_type: $('#fsrt_type').val(),
            fsrt_format: $('#fsrt_format').val()
        },
        async: true,
        dataType: 'html',
        cache: false,
        success: function(data) {
            $('#fsrt_queue_content').html(data);
            //$.scrollTo($('#fsrt_queue_content').offset().top - FSRT.scrollExtra, {duration:300});
            $('#fsrt_type').focus();
            FSRT.processNext();
        }
    });
};

FSRT.generateThumbnail = function(type, format, offset) {
    FSRT.processingType = type;
    FSRT.processingFormat = format;

    $.ajax({
        url: FSRT.generateThumbnailUrl,
        type: 'GET',
        data: {
            fsrt_type: type,
            fsrt_format: format,
            fsrt_offset: offset
        },
        async: true,
        dataType: 'json',
        cache: false,
        success: function(data) {
            var pbid = type + '_' + format + '_progress_bar';
            var pnid = type + '_' + format + '_progress_numeric';
            $('#'+pbid).css('width', data.progress_bar_percent + '%').attr('aria-valuenow', data.progress_bar_percent);
            $('#'+pnid).html(data.processed_count + ' / ' + data.total_count);
            $('#'+pbid).html(data.progress_bar_percent + '%');

            FSRT.processingCount = data.processed_count;
            FSRT.processingHasMore = data.has_more;
            FSRT.processingHasError = data.has_error;
            if (!FSRT.processIsPaused) {
                if (FSRT.processingHasMore) {
                    setTimeout(function () {
                        FSRT.generateThumbnail(FSRT.processingType, FSRT.processingFormat, FSRT.processingCount);
                    }, 500);
                }
                else {
                    setTimeout(function () {
                        FSRT.generateThumbnailFormatDone(FSRT.processingType, FSRT.processingFormat, FSRT.processingCount);
                    }, 500);
                }
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            FSRT.processingHasMore = true;
            FSRT.processPause();
            swal({
                title: FSRT.translateErrorTitle,
                text: FSRT.translateErrorText + '\n' + FSRT.translateErrorResume + '\n\n' + errorThrown,
                type: 'error',
                confirmButtonText: FSRT.translateOk,
                showCancelButton: true,
                cancelButtonText: FSRT.translateCancel
            },
            function(isConfirm){
                if (!isConfirm) {
                    var current = parseInt(FSRT.processingCount);
                    FSRT.processingCount = current + 1;
                }
            });
        }
    });
};

FSRT.generateThumbnailFormatDone = function(type, format, processed_count) {
    var rowid = type + '_' + format;
    var pbid = rowid + '_progress_bar';

    $('#'+rowid).addClass('fsrt_status_done');
    $('#'+pbid).addClass('progress-bar-success');
    if (processed_count > 1) {
        $('#'+pbid).html(processed_count + ' ' + FSRT.translateItemDonePlural);
    }
    else {
        $('#'+pbid).html(processed_count + ' ' + FSRT.translateItemDoneSingular);
    }

    if (processed_count < 1)
        $('#'+pbid).html(FSRT.translateNoImage);

    FSRT.processNext();
};

FSRT.processNext = function() {
    var next = $('.fsrt_queue_item:not(.fsrt_status_done):first', $('#fsrt_queue_list'));
    var type = next.data('imagetype');
    var format = next.data('imageformat');
    if (type && format) {
        var rowid = type + '_' + format;
        var pbid = rowid + '_progress_bar';
        $('#'+pbid).html('Preparing...');
        //$.scrollTo($('#'+rowid).offset().top - FSRT.scrollExtra, {duration:300});
        FSRT.generateThumbnail(type, format, 0);
    }
    else {
        FSRT.statusFinish();
        if (FSRT.processingHasError) {
            swal({
                title: FSRT.translateAlertTitle,
                text: FSRT.translateAlertText+"\n\n"+FSRT.translateHasError+"\n\n"+FSRT.translateDownloadLog,
                type: 'warning',
                confirmButtonText: FSRT.translateOk
            });
        } else {
            swal({
                title: FSRT.translateAlertTitle,
                text: FSRT.translateAlertText,
                type: 'success',
                confirmButtonText: FSRT.translateOk
            });
        }

    }
};

FSRT.statusStart = function() {
    FSRT.processingHasError = null;
    $('#fsrt_button_regenerate').addClass('hide');
    $('#fsrt_button_pause').removeClass('hide');
};

FSRT.statusPause = function() {
    $('#fsrt_button_pause').addClass('hide');
    $('#fsrt_button_resume').removeClass('hide');
};

FSRT.statusResume = function() {
    $('#fsrt_button_pause').removeClass('hide');
    $('#fsrt_button_resume').addClass('hide');
};

FSRT.statusFinish = function() {
    $('#fsrt_button_regenerate').removeClass('hide');
    $('#fsrt_button_pause').addClass('hide');
    $('#fsrt_button_resume').addClass('hide');
};

FSRT.processResume = function() {
    FSRT.processIsPaused = false;
    FSRT.statusResume();

    if (FSRT.processingHasMore) {
        FSRT.generateThumbnail(FSRT.processingType, FSRT.processingFormat, FSRT.processingCount);
    } else {
        FSRT.generateThumbnailFormatDone(FSRT.processingType, FSRT.processingFormat, FSRT.processingCount);
    }
};

FSRT.processPause = function() {
    FSRT.processIsPaused = true;
    FSRT.statusPause();
};