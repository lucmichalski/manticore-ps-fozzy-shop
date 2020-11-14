<div><img src="{$urls.base_url}modules/ecm_liqpay/img/loader.gif"></div>
{l s='You will be redirected to the Liqpay website in a few seconds.' mod='ecm_liqpay'}
<form id="liqpay_redirect" method="POST" action="https://www.liqpay.ua/api/checkout" accept-charset="utf-8">
    <input type="hidden" name="signature"   value="{$signature}" />
    <input type="hidden" name="data"        value="{$data}" />
</form>
<script>document.getElementById("liqpay_redirect").submit();</script>

