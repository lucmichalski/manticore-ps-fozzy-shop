/**
 * Отображение блоков с фамилиями упаковщико, зборщико и менеджеров.
 */
$(document).ready(function(){
    $('#graphsall').find('#packer_personal').parent().parent().addClass('hide');
    $('#graphsall').find('#manager_personal').parent().parent().addClass('hide');
    $("body").on('change', '#fozzy_staff_schedule_stype' ,function(){
        event.preventDefault();
        var type_employee = $('#fozzy_staff_schedule_stype').val();

       	if(type_employee == 1) {
            $('#graphsall').find('#picker_personal').parent().parent().removeClass('hide');
            $('#graphsall').find('#packer_personal').parent().parent().addClass('hide');
            $('#graphsall').find('#manager_personal').parent().parent().addClass('hide');
        } else if(type_employee == 2) {
            $('#graphsall').find('#picker_personal').parent().parent().addClass('hide');
            $('#graphsall').find('#packer_personal').parent().parent().removeClass('hide');
            $('#graphsall').find('#manager_personal').parent().parent().addClass('hide');
        } else {
            $('#graphsall').find('#picker_personal').parent().parent().addClass('hide');
            $('#graphsall').find('#packer_personal').parent().parent().addClass('hide');
            $('#graphsall').find('#manager_personal').parent().parent().removeClass('hide');
        }
          
   });
});

/**
 * Отображение блоков при обновлении сотрудника.
 */
$(document).ready(function(){
    let string = document.location.search;
    let split = string.split('&');

    if(split[4] == 'updatetable_picker') {
        $('#graphsall').find('#picker_personal').parent().parent().removeClass('hide');
        $('#graphsall').find('#packer_personal').parent().parent().addClass('hide');
        $('#graphsall').find('#manager_personal').parent().parent().addClass('hide');
    } else if(split[4] == 'updatetable_packer') {
        $('#graphsall').find('#picker_personal').parent().parent().addClass('hide');
        $('#graphsall').find('#packer_personal').parent().parent().removeClass('hide');
        $('#graphsall').find('#manager_personal').parent().parent().addClass('hide');
    } else if (split[4] == 'updatetable_manager'){
        $('#graphsall').find('#picker_personal').parent().parent().addClass('hide');
        $('#graphsall').find('#packer_personal').parent().parent().addClass('hide');
        $('#graphsall').find('#manager_personal').parent().parent().removeClass('hide');
    }
});

/**
 * Удаление склассов bootstrap.
 */
$(document).ready(function(){
    $('#graphsall').find('#fozzy_staff_schedule_shop').removeClass('fixed-width-xl');
    $('#graphsall').find('#fozzy_staff_schedule_stype').removeClass('fixed-width-xl');
    $('#graphsall').find('#picker_personal').removeClass('fixed-width-xl');
    $('#graphsall').find('#packer_personal').removeClass('fixed-width-xl');
    $('#graphsall').find('#manager_personal').removeClass('fixed-width-xl');
    $('#graphsdriver').find('#fozzy_staff_schedule_driver_shop').removeClass('fixed-width-xl');
    $('#graphsdriver').find('#fozzy_staff_schedule_driver_stype').removeClass('fixed-width-xl');
    $('#graphsdriver').find('#driver_personal').removeClass('fixed-width-xl');
    $('#graphsexport').find('#fozzy_staff_schedule_stype').removeClass('fixed-width-xl');
    $('#graphsexport').find('#fozzy_staff_schedule_shop').removeClass('fixed-width-xl');

});

/**
 * Выбор упаковщиков, зборщиков и старших менеджеров в розрезе по филиалам.
 */
$(document).ready(function(){
    $("body").on('change', '#fozzy_staff_schedule_shop' ,function(){

        var type_shop = $('#fozzy_staff_schedule_shop').val();
        var type_persone = $('#fozzy_staff_schedule_stype').val();

        $.ajax({
            method: "POST",
            url: '/modules/fozzy_staff_schedule/ajax/ajaxForm.php',
            data: {
                'person_shop': type_shop,
                'type_persone': type_persone,
            },
            success: function(response){
                var value = JSON.parse(response);
                $('#'+value.id+' option').remove();
                $.each(value.data, function(index) {
                    var option = document.createElement("option");
                    option.text = value.data[index].fio;
                    option.value = value.data[index].id_person;
                    var select = document.getElementById(value.id);
                    select.appendChild(option);
                })
            }
        })
    });
});

/**
 * Выбор водителей в розрез по филиалам.
 */
$(document).ready(function(){
    $("body").on('change', '#fozzy_staff_schedule_driver_shop' ,function(){
        var type_shop = $('#fozzy_staff_schedule_driver_shop').val();
        var type_persone = $('#fozzy_staff_schedule_driver_stype').val();

        $.ajax({
            method: "POST",
            url: '/modules/fozzy_staff_schedule/ajax/ajaxForm.php',
            data: {
                'person_shop': type_shop,
                'type_persone': type_persone,
            },
            success: function(response){
                var value = JSON.parse(response);

                $('#'+value.id+' option').remove();
                $.each(value.data, function(index) {
                    var option = document.createElement("option");
                    option.text = value.data[index].fio;
                    option.value = value.data[index].id_person;
                    var select = document.getElementById(value.id);
                    select.appendChild(option);
                })
            }
        })
    });
});

/**
 * Подключение плагина 'Select2' для "Сборщиков", "Упаковщиков", "Старших менеджеров".
 */
$(document).ready(function(){
    var $selectStatus = $('select[name^="fozzy_staff_schedule_name_personal"]');
    /*Ввключение select в select2*/
    $selectStatus.select2({closeOnSelect: false});

    $selectStatus.find(':first-child').removeAttr('selected');

    $('.select2-input').css('width','150% !important');

    var changed = false;

    $selectStatus.on('change', function(e) {
        changed = true;
    });

    $selectStatus.on('select2-removed', function(e) {
        if($(this).find('option:selected').length == 0)
        {
            $(this).find(':first-child').prop('selected', true);
        }
        $('#submitFilterButtonorder').focus();
        $('#submitFilterButtonorder').click();
        $('.select2-input').css('width','100% !important');
    });

    $selectStatus.on('select2-close', function(e) {
        if(changed)
        {
            $('#submitFilterButtonorder').focus();
            $('#submitFilterButtonorder').click();
        }
        $('.select2-input').css('width','150% !important');
    });
});

$(document).ready(function(){
    var $selectStatus = $('select[name^="fozzy_staff_schedule_stype"]');
    /*Ввключение select в select2*/
    $selectStatus.select2({closeOnSelect: false});

    $selectStatus.find(':first-child').removeAttr('selected');

    $('.select2-input').css('width','150% !important');

    var changed = false;

    $selectStatus.on('change', function(e) {
        changed = true;
    });

    $selectStatus.on('select2-removed', function(e) {
        if($(this).find('option:selected').length == 0)
        {
            $(this).find(':first-child').prop('selected', true);
        }
        $('#submitFilterButtonorder').focus();
        $('#submitFilterButtonorder').click();
        $('.select2-input').css('width','100% !important');
    });

    $selectStatus.on('select2-close', function(e) {
        if(changed)
        {
            $('#submitFilterButtonorder').focus();
            $('#submitFilterButtonorder').click();
        }
        $('.select2-input').css('width','150% !important');
    });
});

/**
 * Подключение плагина 'Select2' для "Водителей".
 */
$(document).ready(function(){
    var $selectStatus = $('select[name^="fozzy_staff_schedule_driver_name_personal"]');
    /*Ввключение select в select2*/
    $selectStatus.select2({closeOnSelect: false});

    $selectStatus.find(':first-child').removeAttr('selected');

    $('.select2-input').css('width','150% !important');

    var changed = false;

    $selectStatus.on('change', function(e) {
        changed = true;
    });

    $selectStatus.on('select2-removed', function(e) {
        if($(this).find('option:selected').length == 0)
        {
            $(this).find(':first-child').prop('selected', true);
        }
        $('#submitFilterButtonorder').focus();
        $('#submitFilterButtonorder').click();
        $('.select2-input').css('width','100% !important');
    });

    $selectStatus.on('select2-close', function(e) {
        if(changed)
        {
            $('#submitFilterButtonorder').focus();
            $('#submitFilterButtonorder').click();
        }
        $('.select2-input').css('width','150% !important');
    });
});

$(document).ready(function(){
    var $selectStatus = $('select[name^="fozzy_staff_schedule_driver_stype"]');
    /*Ввключение select в select2*/
    $selectStatus.select2({closeOnSelect: false});

    $selectStatus.find(':first-child').removeAttr('selected');

    $('.select2-input').css('width','150% !important');

    var changed = false;

    $selectStatus.on('change', function(e) {
        changed = true;
    });

    $selectStatus.on('select2-removed', function(e) {
        if($(this).find('option:selected').length == 0)
        {
            $(this).find(':first-child').prop('selected', true);
        }
        $('#submitFilterButtonorder').focus();
        $('#submitFilterButtonorder').click();
        $('.select2-input').css('width','100% !important');
    });

    $selectStatus.on('select2-close', function(e) {
        if(changed)
        {
            $('#submitFilterButtonorder').focus();
            $('#submitFilterButtonorder').click();
        }
        $('.select2-input').css('width','150% !important');
    });
});

$(document).ready(function(){
    var $selectStatus = $('select[name^="fozzy_staff_schedule_driver_shop"]');
    /*Ввключение select в select2*/
    $selectStatus.select2({closeOnSelect: false});

    $selectStatus.find(':first-child').removeAttr('selected');

    $('.select2-input').css('width','150% !important');

    var changed = false;

    $selectStatus.on('change', function(e) {
        changed = true;
    });

    $selectStatus.on('select2-removed', function(e) {
        if($(this).find('option:selected').length == 0)
        {
            $(this).find(':first-child').prop('selected', true);
        }
        $('#submitFilterButtonorder').focus();
        $('#submitFilterButtonorder').click();
        $('.select2-input').css('width','100% !important');
    });

    $selectStatus.on('select2-close', function(e) {
        if(changed)
        {
            $('#submitFilterButtonorder').focus();
            $('#submitFilterButtonorder').click();
        }
        $('.select2-input').css('width','150% !important');
    });
});

/**
 * Подключение плагина 'Select2' для экспорта графика сотрудников.
 */
$(document).ready(function(){
    var $selectStatus = $('select[name^="fozzy_staff_schedule_shop"]');
    /*Ввключение select в select2*/
    $selectStatus.select2({closeOnSelect: false});

    $selectStatus.find(':first-child').removeAttr('selected');

    $('.select2-input').css('width','150% !important');

    var changed = false;

    $selectStatus.on('change', function(e) {
        changed = true;
    });

    $selectStatus.on('select2-removed', function(e) {
        if($(this).find('option:selected').length == 0)
        {
            $(this).find(':first-child').prop('selected', true);
        }
        $('#submitFilterButtonorder').focus();
        $('#submitFilterButtonorder').click();
        $('.select2-input').css('width','100% !important');
    });

    $selectStatus.on('select2-close', function(e) {
        if(changed)
        {
            $('#submitFilterButtonorder').focus();
            $('#submitFilterButtonorder').click();
        }
        $('.select2-input').css('width','150% !important');
    });
});

/**
 * Выделение цветом при нажатии на чекбокс графика сотрудника в таблицах.
 */
$(document).ready(function(){
    /*Выделение цветом в таблице "Сборщиков"*/
    $('.table_picker').on('click', 'input:checkbox', function(){
        if ($(this).is(':checked'))
            $(this).parent().parent().children().css("background-color", "#b2cbe4");
        else
            $(this).parent().parent().children().css("background-color", "#ffffff");
    });

    /*Выделение цветом в таблице "Упаковщиков"*/
    $('.table_packer').on('click', 'input:checkbox', function(){
        if ($(this).is(':checked'))
            $(this).parent().parent().children().css("background-color", "#b2cbe4");
        else
            $(this).parent().parent().children().css("background-color", "#ffffff");
    });

    /*Выделение цветом в таблице "Водителей"*/
    $('.table_driver').on('click', 'input:checkbox', function(){
        if ($(this).is(':checked'))
            $(this).parent().parent().children().css("background-color", "#b2cbe4");
        else
            $(this).parent().parent().children().css("background-color", "#ffffff");
    });

    /*Выделение цветом в таблице "Менеджеров"*/
    $('.table_manager').on('click', 'input:checkbox', function(){
        if ($(this).is(':checked'))
            $(this).parent().parent().children().css("background-color", "#b2cbe4");
        else
            $(this).parent().parent().children().css("background-color", "#ffffff");
    });
});

/**
 * Перебрасывание на вкладку водителей.
 */
function fozzy_staff_schedule_init_tabs(){
    $('document').ready( function() {
        $('#navtabs16 a[href="#graphsdriver"]').tab('show');
    });
}