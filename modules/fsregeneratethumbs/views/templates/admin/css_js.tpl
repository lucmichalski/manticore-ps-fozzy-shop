{**
 *  2017 ModuleFactory.co
 *
 *  @author    ModuleFactory.co <info@modulefactory.co>
 *  @copyright 2017 ModuleFactory.co
 *  @license   ModuleFactory.co Commercial License
 *}

{fsrtMinifyCss}
<style type="text/css">
</style>
{/fsrtMinifyCss}

<script type="text/javascript">
    var FSRT = FSRT || { };
    FSRT.isPs15 = {$is_ps_15|escape:'html':'UTF-8'};
    FSRT.isPsMin16 = {$is_ps_min_16|escape:'html':'UTF-8'};
    FSRT.isPs16 = {$is_ps_16|escape:'html':'UTF-8'};
    FSRT.isPsMin17 = {$is_ps_min_17|escape:'html':'UTF-8'};
    FSRT.imageFormatsByType = {$image_formats_by_type|escape:'html':'UTF-8'|fsrtCorrectTheMess};
    FSRT.generateQueueUrl = '{$generate_queue_url|escape:'html':'UTF-8'|fsrtCorrectTheMess}';
    FSRT.generateThumbnailUrl = '{$generate_thumbnail_url|escape:'html':'UTF-8'|fsrtCorrectTheMess}';

    FSRT.translateItemDoneSingular = '{l s='thumbnail regenerated' mod='fsregeneratethumbs'}';
    FSRT.translateItemDonePlural = '{l s='thumbnails regenerated' mod='fsregeneratethumbs'}';
    FSRT.translateAlertText = '{l s='Image thumbnails regeneration completed.' mod='fsregeneratethumbs'}';
    FSRT.translateHasError = '{l s='But some error occurred.' mod='fsregeneratethumbs'}';
    FSRT.translateDownloadLog = '{l s='Please download and check the log file.' mod='fsregeneratethumbs'}';
    FSRT.translateNoImage = '{l s='No image to regenerate' mod='fsregeneratethumbs'}';
    FSRT.translateAlertTitle = '{l s='DONE!' mod='fsregeneratethumbs'}';
    FSRT.translateOk = '{l s='OK' mod='fsregeneratethumbs'}';
    FSRT.translateErrorTitle = '{l s='Error' mod='fsregeneratethumbs'}';
    FSRT.translateErrorText = '{l s='An error occurred during the regeneration process!' mod='fsregeneratethumbs'}';
    FSRT.translateErrorResume = '{l s='Please try to resume.' mod='fsregeneratethumbs'}';
    FSRT.translateCancel = '{l s='Skip Corrupted Image' mod='fsregeneratethumbs'}';
</script>