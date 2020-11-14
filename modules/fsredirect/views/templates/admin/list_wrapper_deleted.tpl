{**
 *  2016 ModuleFactory.co
 *
 *  @author    ModuleFactory.co <info@modulefactory.co>
 *  @copyright 2016 ModuleFactory.co
 *  @license   ModuleFactory.co Commercial License
 *}

{if $is_ps_15}
<br /><br />
<fieldset>
    <legend>{l s='Deleted Items' mod='fsredirect'}</legend>
    {/if}
    {$generated_list|escape:'html':'UTF-8'|fsrCorrectTheMess}
    {if $is_ps_15}
    <b>{l s='Actions' mod='fsredirect'}:</b><br /><br />
    <a href="{$module_export_url|escape:'html':'UTF-8'|fsrCorrectTheMess}" class="button">
        {l s='Export' mod='fsredirect'}
    </a>
    <a href="{$module_delete_all_url|escape:'html':'UTF-8'|fsrCorrectTheMess}" class="button"
       onclick="if (confirm('{l s='Are you sure you want to delete all deleted items?' mod='fsredirect'}')){literal}{return true;} else {return false;}{/literal}">
        {l s='Delete All Deleted Items' mod='fsredirect'}
    </a>
</fieldset>
<br /><br />
{/if}

