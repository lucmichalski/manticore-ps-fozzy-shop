{**
 *  2017 ModuleFactory.co
 *
 *  @author    ModuleFactory.co <info@modulefactory.co>
 *  @copyright 2017 ModuleFactory.co
 *  @license   ModuleFactory.co Commercial License
 *}

<div id="fsrt_form" class="panel">
    <fieldset>
        <legend>{l s='Regenerate Image Thumbnails' mod='fsregeneratethumbs'}</legend>
        {assign var="fsregeneratethumbs_field_name" value="fsrt_type"}
        <label>{l s='Image Type:' mod='fsregeneratethumbs'}</label>
        <div class="margin-form">
            <select class="col-md-3" id="{$fsregeneratethumbs_field_name|escape:'htmlall':'UTF-8'}" name="{$fsregeneratethumbs_field_name|escape:'htmlall':'UTF-8'}">
                <option value="all">{l s='All Type' mod='fsregeneratethumbs'}</option>
                {foreach from=$image_types item=image_type_name key=image_type_value}
                    <option value="{$image_type_value|escape:'htmlall':'UTF-8'}">{$image_type_name|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
            </select>
            <div class="clear"></div>
        </div>
        {assign var="fsregeneratethumbs_field_name" value="fsrt_format"}
        <label>{l s='Image Format:' mod='fsregeneratethumbs'}</label>
        <div class="margin-form">
            <select class="col-md-3" id="{$fsregeneratethumbs_field_name|escape:'htmlall':'UTF-8'}" name="{$fsregeneratethumbs_field_name|escape:'htmlall':'UTF-8'}">
                <option value="all">{l s='All Format' mod='fsregeneratethumbs'}</option>
                {foreach from=$image_formats item=image_format}
                    <option value="{$image_format.name|escape:'htmlall':'UTF-8'}">{$image_format.name|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
            </select>
            <div class="clear"></div>
        </div>
        <label></label>
        <div class="margin-form">
            <a href="javascript:;" onclick="FSRT.generateQueue();" class="button btn btn-default button15" id="fsrt_button_regenerate">{l s='Regenerate' mod='fsregeneratethumbs'}</a>
            <a href="javascript:;" onclick="FSRT.processResume();" class="button btn btn-default button15 hide" id="fsrt_button_resume">{l s='Resume' mod='fsregeneratethumbs'}</a>
            <a href="javascript:;" onclick="FSRT.processPause();" class="button btn btn-default button15 hide" id="fsrt_button_pause">{l s='Pause' mod='fsregeneratethumbs'}</a>

            <a href="{$download_log_url|escape:'htmlall':'UTF-8'|fsrtCorrectTheMess}" class="button btn btn-default button15">{l s='Download Error Log' mod='fsregeneratethumbs'}</a>
        </div>
    </fieldset>
</div>
<br /><br />
<div id="fsrt_queue_content" class="clearfix"></div>