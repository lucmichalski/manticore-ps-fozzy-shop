<script>
var type_auth = '{$type_auth}';
var cart_qties = {$cart_qties|intval};
var id_cart = {$id_cart|intval};
var renderSeq="{$renderSeq}";
var renderCustomerSeq="{$renderCustomerSeq}";
var hide_header={$hide_header|intval};
var hide_column_right={$hide_column_right|intval};
var fill_error = "{l s='Need fill' mod='ecm_checkout' js=1}";
var check_error = "{l s='Need checking' mod='ecm_checkout' js=1}";
var select_error = "{l s='Need select' mod='ecm_checkout' js=1}";
var pass_not_match = "{l s='Passwords not match' mod='ecm_checkout' js=1}";
var phone_not_match = "{l s='Phones not match' mod='ecm_checkout' js=1}";
var password_generate = "{Configuration::get('ecm_checkout_password_generate')}";
var phone_mask = "{Configuration::get('ecm_checkout_phone_mask')}";
var verify_password = "{Configuration::get('ecm_checkout_password2')}";
var render_action = ['quantity_up','quantity_down','quantity_delete','set_quantity','set_payment','set_carrier','delete_Discount','add_Discount','save_country','change_address', 'init_cart'];
</script>