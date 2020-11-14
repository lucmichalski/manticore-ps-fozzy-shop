{**
 *  2017 ModuleFactory.co
 *
 *  @author    ModuleFactory.co <info@modulefactory.co>
 *  @copyright 2017 ModuleFactory.co
 *  @license   ModuleFactory.co Commercial License
 *}

<div id="fsrt_form" class="panel">
    <div class="panel-heading">
        <span>{l s='Regenerate Image Thumbnails' mod='fsregeneratethumbs'}</span>
    </div>
    <div class="form-wrapper clearfix">
        <div class="form-group clearfix">
            {assign var="fsregeneratethumbs_field_name" value="fsrt_type"}
            <label class="control-label col-lg-2" for="{$fsregeneratethumbs_field_name|escape:'htmlall':'UTF-8'}">
                {l s='Image Type:' mod='fsregeneratethumbs'}
            </label>
            <div class="col-lg-10">
                <div class="form-group">
                    <select class="col-md-3" id="{$fsregeneratethumbs_field_name|escape:'htmlall':'UTF-8'}" name="{$fsregeneratethumbs_field_name|escape:'htmlall':'UTF-8'}">
                        <option value="all">{l s='All Type' mod='fsregeneratethumbs'}</option>
                        {foreach from=$image_types item=image_type_name key=image_type_value}
                        <option value="{$image_type_value|escape:'htmlall':'UTF-8'}">{$image_type_name|escape:'htmlall':'UTF-8'}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
        </div>
        <div class="form-group clearfix">
            {assign var="fsregeneratethumbs_field_name" value="fsrt_format"}
            <label class="control-label col-lg-2" for="{$fsregeneratethumbs_field_name|escape:'htmlall':'UTF-8'}">
                {l s='Image Format:' mod='fsregeneratethumbs'}
            </label>
            <div class="col-lg-10">
                <div class="form-group">
                    <select class="col-md-3" id="{$fsregeneratethumbs_field_name|escape:'htmlall':'UTF-8'}" name="{$fsregeneratethumbs_field_name|escape:'htmlall':'UTF-8'}">
                        <option value="all">{l s='All Format' mod='fsregeneratethumbs'}</option>
                        {foreach from=$image_formats item=image_format}
                        <option value="{$image_format.name|escape:'htmlall':'UTF-8'}">{$image_format.name|escape:'htmlall':'UTF-8'}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="panel-footer">
        <a href="javascript:;" onclick="FSRT.generateQueue();" class="btn btn-default" id="fsrt_button_regenerate">
            <i class="process-icon-update"></i>{l s='Regenerate' mod='fsregeneratethumbs'}
        </a>
        <a href="javascript:;" onclick="FSRT.processResume();" class="btn btn-default hide" id="fsrt_button_resume">
            <i class="process-icon-play"></i>{l s='Resume' mod='fsregeneratethumbs'}
        </a>
        <a href="javascript:;" onclick="FSRT.processPause();" class="btn btn-default hide" id="fsrt_button_pause">
            <i class="process-icon-pause"></i>{l s='Pause' mod='fsregeneratethumbs'}
        </a>

        <a href="{$download_log_url|escape:'htmlall':'UTF-8'|fsrtCorrectTheMess}" target="_blank" class="btn btn-default pull-right">
            <i class="process-icon-download"></i>{l s='Error Log' mod='fsregeneratethumbs'}
        </a>
    </div>
</div>
<div id="fsrt_queue_content" class="clearfix"></div>