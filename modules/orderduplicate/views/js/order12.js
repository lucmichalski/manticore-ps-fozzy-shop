/**
 * OrderDuplicate
 *
 * @category  Module
 * @author    silbersaiten <info@silbersaiten.de>
 * @support   silbersaiten <support@silbersaiten.de>
 * @copyright 2015 silbersaiten
 * @version   1.0.0
 * @link      http://www.silbersaiten.de
 * @license   See joined file licence.txt
 */
var orderduplicator = {
    init: function() {
        orderduplicator.createListDropdown();
        
        $(document).on('click', '.order_clone', function(e){
            e.preventDefault();
            
            orderduplicator.processDuplicateButtonClick($(this));
            
            return false;
        });
        
        $(document).on('click', '.order_delete', function(e){
            e.preventDefault();
            
            orderduplicator.processDeleteButtonClick($(this));
            
            return false;
        })
    },
    
    createListDropdown: function() {
        var parent = $('table.table.order');
    
        if (parent.length) {
            var items = parent.find('tbody tr');
            
            if (items.length) {
                items.each(function(){
                    var last_cell = $(this).find('td:last'),
                    id_order = parseInt($(this).find('td:first input[type=checkbox]').attr('value'))
                    if (!id_order) {
                        id_order = parseInt($(this).find('td:first').text())
                    }
                    
                    if (last_cell.length) {
                        var button_container = last_cell.find('.btn-group'),
                            buttons = [
                            orderduplicator.createCloneButton(id_order),
                            //orderduplicator.createDeleteButton(id_order)
                            ];
                        
                        for (var i = 0; i < buttons.length; i++) {
                            if (last_cell.find('.btn-group-action').length) {
                                button_container.find('ul.dropdown-menu').append(buttons[i]);
                            } else {
                                button_container.wrap($(document.createElement('div')).addClass('btn-group-action'));
                                
                                button_container.append(
                                    $(document.createElement('button')).addClass('btn btn-default dropdown-toggle').attr('data-toggle', 'dropdown')
                                    .append($(document.createElement('i')).addClass('icon-caret-down'))
                                ).append($(document.createElement('ul')).addClass('dropdown-menu').append(buttons[i]))
                            }
                        }
                    }
                });
            }
        }
    },
    
    createCloneButton: function(id_order) {
        return $(document.createElement('li')).append($(document.createElement('a')).attr({'href': '#', 'rel': id_order}).addClass('order_clone').html('<i class="icon-copy"></i> ' + orderduplicator.tr('Duplicate')));
    },
    
    createDeleteButton: function(id_order) {
        return $(document.createElement('li')).append($(document.createElement('a')).attr({'href': '#', 'rel': id_order}).addClass('order_delete').html('<i class="icon-trash"></i> ' + orderduplicator.tr('Delete')));
    },
    
    processDuplicateButtonClick: function(btn) {
        var id_order = btn.attr('rel');
        
        $.post(duplicator_path, {'action': 'duplicateGetInfo', 'id_order': id_order}, function(data) {
            orderduplicator.setFancybox(data);
        }, 'html');
    },
    
    processDeleteButtonClick: function(btn) {
        var id_order = btn.attr('rel');
        
        if (confirm(orderduplicator.tr('Are you sure you want to remove this order? This action can\'t be undone.'))) {
            $.post(duplicator_path, {'action': 'deleteOrder', 'id_order': id_order}, function(data) {
                if (typeof(data.success) != 'undefined') {
                    location.reload(true);
                }
            }, 'json');
        }
    },
    
    setFancybox: function(fancybox_content) {
        $.fancybox({
            content: fancybox_content,
            afterShow: function(){
               // orderduplicator.updateAddressList($('input[name=id_customer]').val(), $('input[name=id_address_delivery]').val(), $('input[name=id_address_invoice]').val());
                
                $('#customer_select')
                    .autocomplete(duplicator_path, {
                        minChars: 3,
                        max: 10,
                        selectFirst: false,
                        scroll: false,
                        dataType: "json",
                        formatItem: function(data, i, max, value, term) {
                            return data.firstname + ' ' + data.lastname;
                        },
                        parse: function(data) {
                        var mytab = new Array();
                        for (var i = 0; i < data.length; i++) {
                            mytab[mytab.length] = { data: data[i], value: data[i].firstname + ' ' + data[i].lastname };
                        }
                        return mytab;
                        },
                    }).result(function(event, data, formatted) {
                        $('#customer_select').val(data.firstname + ' ' + data.lastname);
                        $('input[name=id_customer]').val(parseInt(data.id_customer));
                        
                        orderduplicator.updateAddressList(data.id_customer);
                    });
                    
                $('#customer_select').setOptions({
                    extraParams: {
                        action : 'getCustomerList'
                    }
                });
                
                $('#cloneOrder').click(function(e) {
                    e.preventDefault();
                    
                    $.post(
                    duplicator_path,
                    {
                        action: 'cloneOrder',
                        id_order: $('input[name=id_order]').val(),
                        id_order_state: $('select[name=order_state]').val(),
                        id_payment_method: $('select[name=payment_method]').val(),
                        id_customer: $('input[name=id_customer]').val(),
                        id_address_delivery: $('input[name=id_address_delivery]').val(),
                        id_address_invoice: $('input[name=id_address_invoice]').val(),
                        id_order_type: $('select[name=order_type]').val(),
                        ids_products: $('select[name=products]').val()
                    },
                    function(data) {
                        if (typeof(data.success) != 'undefined') {
                            window.location = data.link;
                        }
                        else if (typeof(data.error) != 'undefined') {
                            alert(data.error);
                        }
                    },
                    'json'
                    )
                    
                    return false;
                }); 
            }
        });
    },
    
    updateAddressList: function(id_customer, id_address_delivery_selected, id_address_invoice_selected) {
        id_address_delivery_selected = id_address_delivery_selected || false;
        id_address_invoice_selected = id_address_invoice_selected || false;
        
        $('#addresses_container').slideUp('fast', function() {
            $(this).empty();
            
            var container = $(this);
            
            $.post(duplicator_path, {
                action: 'getAddressList',
                id_customer: id_customer,
                id_address_delivery_selected: id_address_delivery_selected,
                id_address_invoice_selected: id_address_invoice_selected
            }, function(data) {
                container.html(data);
                
                var id_address_invoice = container.find('ul#address_list_invoice li.selected').attr('rel'),
                    id_address_delivery = container.find('ul#address_list_delivery li.selected').attr('rel');

                $('ul#address_list_invoice li.address_item').click(function(){
                    if ( ! $(this).is('.selected')) {
                        $('ul#address_list_invoice li.address_item').removeClass('selected');

                        $(this).addClass('selected');

                        $('input[name=id_address_invoice]').val($(this).attr('rel'));
                    }
                });

                $('ul#address_list_delivery li.address_item').click(function(){
                    if (!$(this).is('.selected')) {
                        $('ul#address_list_delivery li.address_item').removeClass('selected');

                        $(this).addClass('selected');

                        $('input[name=id_address_delivery]').val($(this).attr('rel'));
                    }
                });
                    
                $('input[name=id_address_delivery]').val(id_address_delivery);
                $('input[name=id_address_invoice]').val(id_address_invoice);
            }, 'html');
            
            $(this).slideDown('fast');
        });
    },
    
    tr: function(str) {
        if (typeof(orderduplicateTranslation) == 'object' && str in orderduplicateTranslation) {
            return orderduplicateTranslation[str];
        }
        
        return str;
    }
};

$(function(){
    orderduplicator.init();
});
