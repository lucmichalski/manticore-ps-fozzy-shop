{**
 *  2017 ModuleFactory.co
 *
 *  @author    ModuleFactory.co <info@modulefactory.co>
 *  @copyright 2017 ModuleFactory.co
 *  @license   ModuleFactory.co Commercial License
 *}

{if $is_ps_min_16}
<div id="fieldset_0" class="panel defaultForm form-horizontal">
    <div class="panel-heading">
        {l s='Developed By' mod='fsregeneratethumbs'}
    </div>
    <div class="fsrt-ta-center">
        <a href="https://addons.prestashop.com/en/116_modulefactory" target="_blank">
            <img src="{$module_base_url|escape:'html':'UTF-8'}views/img/help_footer_1280x170.jpg" id="fsrt-developed-by">
        </a>
    </div>
</div>
{/if}

{if $is_ps_15}
<fieldset>
    <legend>{l s='Developed By' mod='fsregeneratethumbs'}</legend>
    <a href="https://addons.prestashop.com/en/116_modulefactory" target="_blank">
        <img src="{$module_base_url|escape:'html':'UTF-8'}views/img/help_footer_1280x170.jpg" id="fsrt-developed-by">
    </a>
</fieldset>
{/if}
