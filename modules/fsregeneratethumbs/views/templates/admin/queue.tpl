{**
 *  2017 ModuleFactory.co
 *
 *  @author    ModuleFactory.co <info@modulefactory.co>
 *  @copyright 2017 ModuleFactory.co
 *  @license   ModuleFactory.co Commercial License
 *}

<div id="fsrt_queue" class="panel">
    <div class="panel-heading">
        <span>{l s='Process Queue' mod='fsregeneratethumbs'}</span>
    </div>
    <div class="fsrt_queue_header">
        <div class="col-md-3 col-xs-6">
            {l s='Image Type' mod='fsregeneratethumbs'}
        </div>
        <div class="col-md-3 col-xs-6">
            {l s='Image Format' mod='fsregeneratethumbs'}
        </div>
        <div class="col-md-3 col-xs-6">
            {l s='Progress' mod='fsregeneratethumbs'}
        </div>
        <div class="clearfix"></div>
    </div>
    <div id="fsrt_queue_list">
        {foreach from=$image_formats_by_type item=image_type key=image_type_key}
            {foreach from=$image_type item=image_format}
                <div data-imagetype="{$image_type_key|escape:'htmlall':'UTF-8'}" data-imageformat="{$image_format.name|escape:'htmlall':'UTF-8'}" id="{$image_type_key|escape:'htmlall':'UTF-8'}_{$image_format.name|escape:'htmlall':'UTF-8'}" class="fsrt_queue_item clearfix">
                    <div class="col-md-3 col-xs-6 fsrt_col">
                        {$image_types[$image_type_key]|escape:'htmlall':'UTF-8'}
                    </div>
                    <div class="col-md-3 col-xs-6 fsrt_col">
                        {$image_format.name|escape:'htmlall':'UTF-8'}
                    </div>
                    <div class="col-md-4 col-xs-12 fsrt_col fsrt_progress_col">
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;" id="{$image_type_key|escape:'htmlall':'UTF-8'}_{$image_format.name|escape:'htmlall':'UTF-8'}_progress_bar">
                                {l s='Queued' mod='fsregeneratethumbs'}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-xs-12 fsrt_col fsrt-ta-center" id="{$image_type_key|escape:'htmlall':'UTF-8'}_{$image_format.name|escape:'htmlall':'UTF-8'}_progress_numeric">
                        ~
                    </div>
                </div>
            {/foreach}
        {/foreach}
    </div>
</div>