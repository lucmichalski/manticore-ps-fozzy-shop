{*
 * 2016 Terranet
 *
 * NOTICE OF LICENSE
 *
 * @author    Terranet
 * @copyright 2016 Terranet
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*}


<div class="panel topLoaderScroll">
    <h3>
        {l s='Fast generation link:' mod='gshoppingfeed'}
    </h3>

    <h4>
        {l s='Copy this link and insert it in Google Merchant Center' mod='gshoppingfeed'}
    </h4>

    {l s='Rebuild & Download link:' mod='gshoppingfeed'}

    <a target="_blank" href="{$cron_link|escape:'html':'UTF-8'}">
        {$cron_link|escape:'html':'UTF-8'}
    </a>

    <span class="ml-20 btn btn-default" onclick="copyText('{$cron_link|escape:'html':'UTF-8'}')">
        {l s='Copy to clipboard' mod='gshoppingfeed'}
    </span>

    <hr/>
    <h4>
        {l s='For more products quantity' mod='gshoppingfeed'}
    </h4>
    <p> {l s='Rebuild (for cronjob):' mod='gshoppingfeed'}
        <a target="_blank" href="{$cron_link_rebuild|escape:'html':'UTF-8'}">
            {$cron_link_rebuild|escape:'html':'UTF-8'}
        </a>
        <span class="ml-20 btn btn-default" onclick="copyText('{$cron_link_rebuild|escape:'html':'UTF-8'}')">
        {l s='Copy to clipboard' mod='gshoppingfeed'}
    </span>
    </p>
    <p> {l s='Download (for Google Merchant Center):' mod='gshoppingfeed'}
        <a target="_blank" href="{$cron_link_download|escape:'html':'UTF-8'}">
            {$cron_link_download|escape:'html':'UTF-8'}
        </a>
        <span class="ml-20 btn btn-default" onclick="copyText('{$cron_link_download|escape:'html':'UTF-8'}')">
        {l s='Copy to clipboard' mod='gshoppingfeed'}
    </span>
    </p>
</div>
<script type="text/javascript">
    function copyText(str){
      const el = document.createElement('textarea');
      el.value = str;
      document.body.appendChild(el);
      el.select();
      document.execCommand('copy');
      document.body.removeChild(el);
    }
</script>
