/**
 *  2016 ModuleFactory.co
 *
 *  @author    ModuleFactory.co <info@modulefactory.co>
 *  @copyright 2016 ModuleFactory.co
 *  @license   ModuleFactory.co Commercial License
 */

var FSR = FSR || {};

FSR.convertToRedirect = function(url)
{
    $('input#old_url').val(url);
    $.scrollTo(0, {duration:300});
}
