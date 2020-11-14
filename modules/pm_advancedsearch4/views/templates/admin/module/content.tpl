{$css_js_assets|as4_nofilter}

<div id="pm_backoffice_wrapper" class="pm_bo_ps_{$ps_major_version|escape:'htmlall':'UTF-8'} pm_bo_ps_{$ps_version_full|escape:'htmlall':'UTF-8'}">
    {module->_displayTitle text="{$module_display_name}"}

    {if !$permissions_errors|sizeof}
        {if $module_is_up_to_date}
            {if $context_is_shop}
                {if $block_layered_is_active}
                    {module->_showWarning text="
                        {l s='"%s" is not compatible with Advanced Search 4.' sprintf=$block_layered_display_name mod='pm_advancedsearch4'}<br />
                        {l s='You must disable it to prevent unexpected behaviour on your shop.' mod='pm_advancedsearch4'}
                    "}
                {/if}
                {$rating_invite|as4_nofilter}
                {$parent_content|as4_nofilter}

                {if $cache_alert}
                    {module->_showWarning text="
                        {l s='Cache is currently disabled. We really recommend you to enable it when you are in production mode.' mod='pm_advancedsearch4'}<br /><br />
                        <a class=\"button\" href=\"#moduleCache_on\">
                            {l s='Enable it' mod='pm_advancedsearch4'}
                        </a>
                    "}
                {/if}

                {as4_button text={l s='Add a new search engine' mod='pm_advancedsearch4'} href="{$base_config_url|as4_nofilter}&pm_load_function=displaySearchForm&class=AdvancedSearchClass&pm_js_callback=closeDialogIframe" class='open_on_dialog_iframe' rel='980_530_1' icon_class='ui-icon ui-icon-plusthick'}
                <br />
                <br />
                <div id="wrapAsTab">
                    <ul id="asTab">
                    {foreach from=$search_engines item=search_engine}
                        <li id="TabSearchAdminPanel{$search_engine.id_search|intval}">
                            <a href="{$base_config_url|as4_nofilter}&pm_load_function=displaySearchAdminPanel&id_search={$search_engine.id_search|intval}">
                                <span>{$search_engine.id_search|intval} - {$search_engine.internal_name|escape:'htmlall':'UTF-8'}</span>
                            </a>
                        </li>
                    {/foreach}
                    </ul>
                </div>
                <br />

                <div id="msgNoResults" style="{if $search_engines|is_array && $search_engines|sizeof}display:none{/if}">
                    {module->_showWarning text="{l s='You do not have added a search engine yet. Please click on the click “Add a new search engine“ in order to start.' mod='pm_advancedsearch4'}"}
                    <br />
                </div>

                <div id="wrapConfigTab">
                    <ul id="configTab">
                        <li>
                            <a href="#config-1">
                                <span><img src="{$module_path|as4_nofilter}views/img/cog.gif" />  {l s='General settings' mod='pm_advancedsearch4'}</span>
                            </a>
                        </li>
                        <li>
                            <a href="#config-2">
                                <span><img src="{$module_path|as4_nofilter}views/img/document-code.png" /> {l s='Advanced styles (CSS)' mod='pm_advancedsearch4'}</span>
                            </a>
                        </li>
                        <li>
                            <a href="#config-3">
                                <span><img src="{$module_path|as4_nofilter}views/img/clock.png" /> {l s='Scheduled Task' mod='pm_advancedsearch4'}</span>
                            </a>
                        </li>
                        <li>
                            <a href="#config-4">
                                <span><img src="{$module_path|as4_nofilter}views/img/drill.png" /> {l s='Maintenance' mod='pm_advancedsearch4'}</span>
                            </a>
                        </li>
                    </ul>

                    <div id="config-1">
                        {$configuration_tab|as4_nofilter}
                    </div>
                    <div id="config-2">
                        {$advanced_styles_tab|as4_nofilter}
                    </div>
                    <div id="config-3">
                        {$cron_tab|as4_nofilter}
                    </div>
                    <div id="config-4">
                        {$maintenance_tab|as4_nofilter}
                    </div>
                </div>

                <script type="text/javascript">
                    var editTranlate = {{l s='Configure' mod='pm_advancedsearch4'}|json_encode};
                    var deleteTranlate = {{l s='Delete' mod='pm_advancedsearch4'}|json_encode};
                    var alertDeleteCriterionGroup = {{l s='Do you really want to delete this criterion group from your search engine ?' mod='pm_advancedsearch4'}|json_encode};
                    var reindexationInprogressMsg = {{l s='Another reindexing is in progress. Please wait.' mod='pm_advancedsearch4'}|json_encode};
                    var reindexingCriteriaMsg = {{l s='Reindexing group' mod='pm_advancedsearch4'}|json_encode};
                    var reindexingCriteriaOfMsg = "/";
                    {literal}
                    $(document).ready(function() {
                        // Initialise the second table specifying a dragClass and an onDrop function that will display an alert
                        $("#wrapConfigTab").tabs({cache:false});
                        $("#wrapAsTab").tabs({cache:false}).find(".ui-tabs-nav").sortable({
                            axis: "x",
                            update: function(event, ui) {
                                var order = $(this).sortable("toArray");
                                saveOrder(order.join(","), "orderSearchEngine");
                            },
                            stop: function() {
                                $("#wrapAsTab").tabs("refresh");
                            }
                        });
                    });
                    {/literal}
                </script>

            {else}
                {module->_showWarning text="{l s='You must select a specific shop in order to continue, you can\'t create/edit a search engine from the “all shop“ or “group shop“ context.' mod='pm_advancedsearch4'}"}
            {/if}
        {else}
            {if $from_version_4_9}
                {* 4.10 releases updates *}
                {module->_showWarning text="
                    {l s='You have an old version of the module, this new version has a new approach for multishop features.' mod='pm_advancedsearch4'}<br /><br />
                    {l s='Because of a lot of constraints, you will not be able to associate one search engine to multiple shops anymore.' mod='pm_advancedsearch4'}<br /><br />
                    {l s='If it is your case at this time, we will only associate your search engine to one shop (from all the shops it was currently associated).' mod='pm_advancedsearch4'}<br /><br />
                    {l s='We really recommand you to re-index all the search engines after this update. Thank you for your comprehension.' mod='pm_advancedsearch4'}
                "}
            {elseif $from_version_4_10}
                {* 4.11 releases updates *}
                {module->_showWarning text="
                    {l s='We really recommand you to re-index all the search engines after this update.' mod='pm_advancedsearch4'}<br /><br />
                    {l s='Also, please backup every database table related to this module before updating (<your prefix>_pm_advancedsearch*).' mod='pm_advancedsearch4'}
                "}
            {/if}

            {module->_showWarning text="
                {l s='We have detected that you installed a new version of the module on your shop' mod='pm_advancedsearch4'}<br /><br />
                <center>
                    <a href=\"{$base_config_url|as4_nofilter}&makeUpdate=1\" class=\"button\">
                        {l s='Please click here in order to finish the installation process' mod='pm_advancedsearch4'}
                    </a>
                </center>
            "}
        {/if}
    {else}
        {module->_showWarning text="
            {l s='Before being able to configure the module, make sure to set write permissions to files and folders listed below:' mod='pm_advancedsearch4'}<br /><br />
            {$permissions_errors|implode:'<br />'|as4_nofilter}
        "}
    {/if}

    {module->_displaySupport}
</div>