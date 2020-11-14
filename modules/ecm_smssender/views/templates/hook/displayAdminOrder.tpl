<input id="module_url" name="module_url" type="hidden" value ="{$base_url}modules/ecm_smssender/"/>
<a onclick="showform({$id_order},0)" class="button button-small btn btn-default" title="{l s='Send SMS with payment data' mod='ecm_smssender'}">
    <span>
        <i class="icon-money">
        </i>{l s=' Send SMS' mod='ecm_smssender'}
    </span>
</a>
<a onclick="showform({$id_order},1)" class="button button-small btn btn-default" title="{l s='Send SMS with TTN data' mod='ecm_smssender'}">
    <span>
        <i class="icon-barcode">
        </i>{l s=' Send SMS' mod='ecm_smssender'}
    </span>
</a>
</br></br>
<script>
var url = '{$module_path}'+"/ajax/sender.php";
    function showform(id_order,header) {
        if (!!$.prototype.fancybox){
            $.fancybox({
                    title  : null,
                    padding: 25,
                    type   : 'ajax',
                    href   : url+"?mode=showform&id_order="+id_order+"&header="+header,
                    ajax   :{
                    },
                    helpers: {
                        overlay: {
                            locked: false
                        }
                    },
                    afterClose: function () {
                        parent.location.reload();
                    }
                });
        }
    }
    function sendsms(id_order,header) {
        $( "#ecm_send_sms" ).prop( "disabled", true );
        phone = $('input[name=phone]:checked').val()
        if (phone === undefined){
            $("#error_message").html('<p><font color="#FF0000">Выберите номер телефона для отправки СМС</font></p>');
            $( "#ecm_send_sms" ).prop( "disabled", false );
            return false;
        }
        var data = {
            mode:"sendsms",
            id_order:id_order,
            phone:phone,
            header:header
        };
        console.log(data)
        $.ajax({
                data:data,
                url:url,
                success: function(msg) {
                    console.log(msg)
                    if(msg == 1) {
                        $("#error_message").html('<p><font color="green">СМС успешно отправлено</font></p>');
                        setTimeout(function(){ showSuccessMessage("СМС успешно отправлено"); parent.jQuery.fancybox.close();}, 3000);

                    }  else {
                        $(function () {
                                $("#error_message").html('<p><font color="#FF0000">'+msg+'</font></p>');
                                return false;
                            });

                        showErrorMessage("Неудача(");

                    }

                },
            });

    }
</script>
