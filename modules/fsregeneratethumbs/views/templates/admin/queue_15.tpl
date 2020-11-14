{**
 *  2017 ModuleFactory.co
 *
 *  @author    ModuleFactory.co <info@modulefactory.co>
 *  @copyright 2017 ModuleFactory.co
 *  @license   ModuleFactory.co Commercial License
 *}

<div id="fsrt_queue" class="panel">
    <fieldset>
        <legend>{l s='Process Queue' mod='fsregeneratethumbs'}</legend>
        <table id="fsrt_queue_list" cellspacing="0" cellpadding="0" style="width: 100%; margin-bottom:10px;" class="table">
            <thead>
                <tr class="nodrag nodrop">
                    <th><span class="title_box">{l s='Image Type' mod='fsregeneratethumbs'}</span></th>
                    <th><span class="title_box">{l s='Image Format' mod='fsregeneratethumbs'}</span></th>
                    <th><span class="title_box">{l s='Progress' mod='fsregeneratethumbs'}</span></th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$image_formats_by_type item=image_type key=image_type_key}
                {foreach from=$image_type item=image_format}
                <tr data-imagetype="{$image_type_key|escape:'htmlall':'UTF-8'}" data-imageformat="{$image_format.name|escape:'htmlall':'UTF-8'}" id="{$image_type_key|escape:'htmlall':'UTF-8'}_{$image_format.name|escape:'htmlall':'UTF-8'}" class="fsrt_queue_item">
                    <td style="padding-right:20px;">{$image_types[$image_type_key]|escape:'htmlall':'UTF-8'}</td>
                    <td style="padding-right:20px;">{$image_format.name|escape:'htmlall':'UTF-8'}</td>
                    <td style="width:100%">
                        <div class="progress fsmg_progress">
                            <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;" id="{$image_type_key|escape:'htmlall':'UTF-8'}_{$image_format.name|escape:'htmlall':'UTF-8'}_progress_bar">
                                {l s='Queued' mod='fsregeneratethumbs'}
                            </div>
                        </div>
                    </td>
                </tr>
                {/foreach}
            {/foreach}
            </tbody>
        </table>
    </fieldset>
</div>