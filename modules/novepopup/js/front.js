/**
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2014 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*
* Don't forget to prefix your containers with your own identifier
* to avoid any conflicts with others containers.
*/


$(document).ready(function(){
           
  $(document).on('click', '#novepopup .cross, #novepopup-overlay', function(e){
           	$('#novepopup-overlay').hide();
           	$('#novepopup').fadeOut('fast');
           	if($("#novepopup-checkbox").is(':checked'))
           	 novepopsetcook();
           });
       });

  $(document).on('click', ' #novepopup  .nove-btn-newsletter', function(e){
            
             novepopsetcook();
             $('#novepopup-overlay').hide();
            $('#novepopup').fadeOut('fast');
       });
       
  $(document).on('click', ' #novepopup a ', function(e){
           alert('ok'); 
            var link = $(this).attr("href");
            novepopsetcook1(link);
            $('#novepopup-overlay').hide();
            $('#novepopup').fadeOut('fast');
       });



		
      function novepopsetcook() {
            var name = novepopup_name;
            var value = '1';
            var expire = new Date();
            expire.setDate(expire.getDate()+novepopup_time);
            document.cookie = name + "=" + value +";path=/;" + ((expire==null)?"" : ("; expires=" + expire.toGMTString()))
        }

     function novepopsetcook1(link) {
            var name = novepopup_domain;
            var value = 'link';
            var expire = new Date();
            expire.setDate(expire.getDate()+novepopup_time);
            document.cookie = name + "=" + encodeURI(value) +";path=/;" + ((expire==null)?"" : ("; expires=" + expire.toGMTString()))
        }