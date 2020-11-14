{*
/**
 * StorePrestaModules SPM LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 *
 /*
 * 
 * @author    StorePrestaModules SPM
 * @category content_management
 * @package blockfaq
 * @copyright Copyright StorePrestaModules SPM
 * @license   StorePrestaModules SPM
 */
*}

{capture name=path}
{l s='Frequently asked questions' mod='blockfaq'}
{/capture}

{if $blockfaqis16 == 1}
<h1 class="page-heading">{$meta_title|escape:'htmlall':'UTF-8'}</h1>
{/if}

<div class="border-block-faq">

<div class="b-tab">
	<ul>
		<li class="current"><a href="#" {if $blockfaqis16 == 1}class="b-tab-16"{/if}>{l s='Frequently asked questions' mod='blockfaq'}</a></li>
	</ul>					
</div>

<div class="clear"></div>
<div class="b-search-friends">

	<div class="float-right margin-top-5">
	<b class="filter-category-color">{l s='Filter by category' mod='blockfaq'}:&nbsp;</b>
	<select onchange="window.location.href='{$blockfaqfaq_url|escape:'htmlall':'UTF-8'}?category_id='+this.options[this.selectedIndex].value">';
	<option value=0>---</option>
	
	{foreach from=$blockfaqdata_categories.items item=cat name=myLoop}
	<option value={$cat.id|escape:'htmlall':'UTF-8'} {if $blockfaqselected_cat == $cat.id}selected="selected"{/if}>{$cat.title|escape:'quotes':'UTF-8'}</option>
	{/foreach}
		
	</select>
	
	{if $blockfaqselected_cat != 0}
	<a href="{$blockfaqfaq_url|escape:'htmlall':'UTF-8'}"
	   class="text-decoration-underline">{l s='clear filter' mod='blockfaq'}</a>
	{/if}
	
	<br/><br/>
	<form method="get" action="{$blockfaqfaq_url|escape:'htmlall':'UTF-8'}">

	<fieldset>
	<input type="submit" value="go" class="button_mini {if $blockfaqis17 == 1}button-mini-blockfaq{/if} {if $blockfaqis_ps15 == 0}search_go{/if}">
    <input type="text" class="txt {if $blockfaqis17 == 1}search-faq17{/if}  {if $blockfaqis_ps15 == 0}search-input-height-15{/if}" name="search" onfocus="{literal}if(this.value == '{/literal}{l s='Search' mod='blockfaq'}{literal}') {this.value='';};{/literal}" onblur="{literal}if(this.value == '') {this.value='{/literal}{l s='Search' mod='blockfaq'}{literal}';};{/literal}" value="{l s='Search' mod='blockfaq'}" />
	{if $blockfaqis_search == 1}
        <a href="{$blockfaqfaq_url|escape:'htmlall':'UTF-8'}" class="clear-search-shoppers">
            {l s='Clear search' mod='blockfaq'}
        </a>
	{/if}
	</fieldset>
	</form>
	
	
	
	</div>
	
	<div class="clear"></div>
</div>
		
<div class="main-text-box">

{if $blockfaqfaqis_askform == 1}

<div class="button-ask-a-question">
<a class="btn-custom btn-primary-blockfaq" href="javascript:void(0)" onclick="show_form_question(1)" id="button-bottom-add-question"><b>{l s='Ask a question' mod='blockfaq'}</b></a>
</div>
	
<div id="succes-question-faq">
{l s='Thank you, moderator will answer as soon as possible!' mod='blockfaq'}						
</div>
	
<div id="add-question-form">

	<div class="title-rev" id="idTab666-my">
		<div class="float-left">
		{l s='Ask a question' mod='blockfaq'}
		</div>
		<a href="javascript:void(0)" class="btn-custom btn-primary-blockfaq button-hide-form" onclick="show_form_question(0)">
			<b>{l s='hide form' mod='blockfaq'}</b>
		</a>
		<div class="clear"></div>
	</div>

    <div id="body-add-faq-form">



        <label for="name-question">{l s='Name' mod='blockfaq'}:</label>
                <input type="text" name="name-question" id="name-question" onkeyup="check_inpNameFAQ();" onblur="check_inpNameFAQ();" class="input-question-form" {if $blockfaqcustomer_firstname || $blockfaqcustomer_lastname} value="{$blockfaqcustomer_firstname|escape:'htmlall':'UTF-8'} {$blockfaqcustomer_lastname|escape:'htmlall':'UTF-8'}"   {else}   value=""   {/if} />
                <div class="errorTxtAdd" id="error_name-question"></div>

        <label for="email-question">{l s='Email' mod='blockfaq'}:</label>
                <input type="text" name="email-question" onkeyup="check_inpEmailFAQ();" onblur="check_inpEmailFAQ();"  id="email-question" class="input-question-form" value="{$blockfaqemail|escape:'htmlall':'UTF-8'}" />
                <div class="errorTxtAdd" id="error_email-question"></div>

        <label>{l s='Category' mod='blockfaq'}:</label>
        <div class="clr"></div>
                <select name="category-faq" id="category-faq">
                    <option value=0>---</option>

                    {foreach from=$blockfaqdata_categories.items item=cat name=myLoop}
                        <option value={$cat.id|escape:'htmlall':'UTF-8'}>{$cat.title|escape:'htmlall':'UTF-8'}</option>
                    {/foreach}

                </select>
        <div class="clr"></div>


         <label for="text-question">{l s='Question' mod='blockfaq'}:</label>
                <textarea class="textarea-question-form" onkeyup="check_inpMsgFAQ();" onblur="check_inpMsgFAQ();" id="text-question" name="text-question" cols="42" rows="7"></textarea>
                <div class="errorTxtAdd" id="error_text-question"></div>


        {if $blockfaqfaqis_captcha == 1}
        <label for="inpCaptchaReview">{l s='Captcha' mod='blockfaq'}</label>
            <div class="clr"></div>
                    <img width="100" height="26" class="float-left" id="secureCodReview" src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/blockfaq/captcha.php" alt="Captcha"/>
                    <input type="text" onkeyup="check_inpCaptchaFAQ();" onblur="check_inpCaptchaFAQ();" class="inpCaptchaReview float-left" id="inpCaptchaReview" size="6"/>
                    <div class="clr"></div>

                    <div id="error_inpCaptchaReview" class="errorTxtAdd"></div>

        {/if}
        <div id="footer-add-faq-form-faq">
                <a href="javascript:void(0)" class="btn-custom btn-success-custom button-ask-a-question-form" onclick="add_question()" >
                    <b>{l s='Ask a question' mod='blockfaq'}</b>
                </a>
        </div>

    </div>

    {*
	<table>
				
		<tr>
			<td class="form-left">{l s='Name' mod='blockfaq'}:</td>
			<td class="form-right">
				<input type="text" name="name-question" id="name-question" class="input-question-form" {if $blockfaqcustomer_firstname || $blockfaqcustomer_lastname} value="{$blockfaqcustomer_firstname|escape:'htmlall':'UTF-8'} {$blockfaqcustomer_lastname|escape:'htmlall':'UTF-8'}"   {else}   value=""   {/if} />
			</td>
		</tr>
		<tr>
			<td class="form-left">{l s='Email' mod='blockfaq'}:</td>
			<td class="form-right">
				<input type="text" name="email-question"  id="email-question" class="input-question-form" value="{$blockfaqemail|escape:'htmlall':'UTF-8'}" />
			</td>
		</tr>
		
		<tr>
			<td class="form-left">{l s='Category' mod='blockfaq'}:</td>
			<td class="form-right">
			
					<select name="category-faq" id="category-faq">
					<option value=0>---</option>
					
					{foreach from=$blockfaqdata_categories.items item=cat name=myLoop}
					<option value={$cat.id|escape:'htmlall':'UTF-8'}>{$cat.title|escape:'htmlall':'UTF-8'}</option>
					{/foreach}
						
					<select>
			</td>
		</tr>
		
		
	
	
		
		<tr>
			<td class="form-left">{l s='Question' mod='blockfaq'}:</td>
			<td class="form-right">
				<textarea class="textarea-question-form" id="text-question" name="text-question" cols="42" rows="7"></textarea>
			</td>
		</tr>
		{if $blockfaqfaqis_captcha == 1}
		<tr>
			<td class="form-left">&nbsp;</td>
			<td class="form-right">
			<img width="100" height="26" class="float-left" id="secureCodReview" src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/blockfaq/captcha.php" alt="Captcha"/>
			<input type="text" class="inpCaptchaReview float-left" id="inpCaptchaReview" size="6"/>
				  <div class="clr"></div>
						  
			<div id="error_inpCaptchaReview" class="errorTxtAdd"></div>	
			</td>
		</tr>
		{/if}
		<tr>
			<td class="form-left">&nbsp;</td>
			<td class="form-right">
				<a href="javascript:void(0)" class="greenBtnBig button-ask-a-question-form" onclick="add_question()" >
					<b>{l s='Ask a question' mod='blockfaq'}</b>
				</a>
			</td>
		</tr>
		
	</table>
	*}
	

</div>
<br/><br/>



{literal}
<script type="text/javascript">
    function check_inpNameFAQ()
    {

        var name_review = trim(document.getElementById('name-question').value);

        if (name_review.length == 0)
        {
            field_state_change('name-question','failed', '{/literal}{$blockfaqmsg2|escape:'htmlall':'UTF-8'}{literal}');
            return false;
        }
        field_state_change('name-question','success', '');
        return true;
    }


    function check_inpEmailFAQ()
    {

        var email_review = trim(document.getElementById('email-question').value);

        if (email_review.length == 0)
        {
            field_state_change('email-question','failed', '{/literal}{$blockfaqmsg3|escape:'htmlall':'UTF-8'}{literal}');
            return false;
        }
        field_state_change('email-question','success', '');
        return true;
    }

    function check_inpMsgFAQ()
    {

        var subject_review = trim(document.getElementById('text-question').value);

        if (subject_review.length == 0)
        {
            field_state_change('text-question','failed', '{/literal}{$blockfaqmsg4|escape:'htmlall':'UTF-8'}{literal}');
            return false;
        }
        field_state_change('text-question','success', '');
        return true;
    }


    {/literal}{if $blockfaqfaqis_captcha == 1}{literal}
    function check_inpCaptchaFAQ()
    {

        var inpCaptchaReview = trim(document.getElementById('inpCaptchaReview').value);

        if (inpCaptchaReview.length != 6)
        {
            field_state_change('inpCaptchaReview','failed', '{/literal}{$blockfaqmsg5|escape:'htmlall':'UTF-8'}{literal}');
            return false;
        }
        field_state_change('inpCaptchaReview','success', '');
        return true;
    }
    {/literal}{/if}{literal}

function add_question(){
	var _name_question = $('#name-question').val();
	var _email_question = $('#email-question').val();
	var _text_question = $('#text-question').val();
	{/literal}{if $blockfaqfaqis_captcha == 1}{literal}
		var _captcha = $('#inpCaptchaReview').val();
	{/literal}{/if}{literal}
    var category = $('#category-faq').val();


    var is_name_faq = check_inpNameFAQ();
    var is_email_faq = check_inpEmailFAQ();
    var is_msg_faq =check_inpMsgFAQ();
    {/literal}{if $blockfaqfaqis_captcha == 1}{literal}
    var is_captcha_faq = check_inpCaptchaFAQ();
    {/literal}{/if}{literal}

    if(is_name_faq && is_email_faq && is_msg_faq

    {/literal}{if $blockfaqfaqis_captcha == 1}{literal}
        && is_captcha_faq
    {/literal}{/if}{literal}
    ){


		
	$('#add-question-form').css('opacity',0.5);
	$.post(baseDir + 'modules/blockfaq/ajax.php', 
			{action:'addquestion',
			 name:_name_question,
			 email:_email_question,
			 text_question:_text_question,
			 {/literal}{if $blockfaqfaqis_captcha == 1}{literal}
			 	 captcha:_captcha,
			 {/literal}{/if}{literal}
			 category:category
			 }, 
	function (data) {
		$('#add-question-form').css('opacity',1);
		
		if (data.status == 'success') {

			
			show_form_question(0);

			$('#name-question').val('');
			$('#email-question').val('');
			$('#text-question').val('');
			$('#inpCaptchaReview').val('');
			
			$('#succes-question-faq').show();



			{/literal}{if $blockfaqfaqis_captcha == 1}{literal}
				var count = Math.random();
				document.getElementById('secureCodReview').src = "";
				document.getElementById('secureCodReview').src = "{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{literal}modules/blockfaq/captcha.php?re=" + count;
			{/literal}{/if}{literal}
			
					
		} else {
			$('#add-question-form').css('opacity',1);
			var error_type = data.params.error_type;
			
			if(error_type == 2){
				field_state_change('email-question','failed', '{/literal}{$blockfaqmsg6|escape:'htmlall':'UTF-8'}{literal}');
                return false;
			} 
			{/literal}{if $blockfaqfaqis_captcha == 1}{literal}
				else if(error_type == 3){ 
				    field_state_change('inpCaptchaReview','failed', '{/literal}{$blockfaqmsg7|escape:'htmlall':'UTF-8'}{literal}');
                    $('#inpCaptchaReview').val('');
                    var count = Math.random();
                    document.getElementById('secureCodReview').src = "";
                    document.getElementById('secureCodReview').src = "{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{literal}modules/blockfaq/captcha.php?re=" + count;
                    return false;
				} 
			{/literal}{/if}{literal}

			else {
				alert(data.message);
			}

				{/literal}{if $blockfaqfaqis_captcha == 1}{literal}
                    $('#inpCaptchaReview').val('');
					var count = Math.random();
					document.getElementById('secureCodReview').src = "";
					document.getElementById('secureCodReview').src = "{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{literal}modules/blockfaq/captcha.php?re=" + count;
				{/literal}{/if}{literal}
			
		}
	}, 'json');


    }
}


function show_form_question(par){
	if(par == 1){
		 $('#button-bottom-add-question').hide(200);
	     $('#add-question-form').show(200);
	     $('#succes-question-faq').hide();
	} else {
		$('#button-bottom-add-question').show(200);
	     $('#add-question-form').hide(200);
	}
}

function trim(str) {
	   str = str.replace(/(^ *)|( *$)/,"");
	   return str;
	   }


    function field_state_change(field, state, err_text)
    {

        var field_label = $('label[for="'+field+'"]');
        var field_div_error = $('#'+field);

        if (state == 'success')
        {
            field_label.removeClass('error-label');
            field_div_error.removeClass('error-current-input');
        }
        else
        {
            field_label.addClass('error-label');
            field_div_error.addClass('error-current-input');
        }
        document.getElementById('error_'+field).innerHTML = err_text;

    }
</script>
{/literal}


{/if}

{if $blockfaqis_search == 1}
<h3 class="search-result-item">{l s='Results for' mod='blockfaq'} "{$blockfaqsearch|escape:'quotes':'UTF-8'}"</h3>
<br/>
{/if}

{if $blockfaqselected_cat!=0}
{foreach from=$blockfaqdata_categories.items item=cat name=myLoop}
	{if $blockfaqselected_cat == $cat.id}
	<h3 class="filter-by-cat-result-item">{l s='Questions in' mod='blockfaq'} <span class="category-faq-color">{$cat.title|escape:'quotes':'UTF-8'}</span></h3>
	{/if}
{/foreach}
{/if}		

<div class="clear"></div>


{if count($blockfaqitems) > 0}


{foreach from=$blockfaqitems item=faq_cat name=myLoop1}

    {if isset($faq_cat[0].category_title_parent)}
        <h3 class="title-faq-category">{$faq_cat[0].category_title_parent nofilter}</h3>
    {/if}



    {foreach from=$faq_cat item=faq name=myLoop}

	<p class="faqItem" id="faq_{$faq.id|escape:'htmlall':'UTF-8'}">
		<span class="font-weight-bold"> > </span><strong>{$faq.title nofilter}</strong>
	</p>
	<div id="faq_{$faq.id|escape:'htmlall':'UTF-8'}_block" style="display: none;"
		class="faqAnsw">
	<p>
      {l s='Posted' mod='blockfaq'}: {$faq.time_add|date_format|escape:'quotes':'UTF-8' nofilter}
      
      {if $faq.is_by_customer && strlen($faq.customer_name)>0}
      {l s='by' mod='blockfaq'} <b>{$faq.customer_name|escape:'quotes':'UTF-8' nofilter}</b>
      {/if}
      
      {if count($faq.categories)>0}
       {l s='in' mod='blockfaq'}  
      {foreach from=$faq.categories item=category name=catname}
      <a href="{$blockfaqfaq_url|escape:'htmlall':'UTF-8'}?category_id={$category.category_id|escape:'htmlall':'UTF-8'}"
	  		title="{$category.title nofilter}"
      		>{$category.title nofilter}</a>{if $smarty.foreach.catname.last}{else}, {/if}
      {/foreach}
      {/if}
      
    </p>
    	
	{$faq.content nofilter}
		
	</div>
    {/foreach}
{/foreach}


{else}
<p class="faqAnsw text-align-center">{l s='Questions not found' mod='blockfaq'}</p>
{/if}
	
	</div>

</div>


{literal}
<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function(event) {
jQuery(document).ready(function(){
$('.main-text-box .faqItem').click(function () { 
    $(this).next()[!$(this).next().is(':visible') ? 'show' : 'hide'](400);
  });


if(window.location.href.indexOf('#') != -1){
var vars = [], hash = '';
var hashes = window.location.href.slice(window.location.href.indexOf('#') + 1);

for(var i = 0; i < hashes.length; i++)
{
	hash += hashes[i];
	//alert(hashes[i]);
}
$('#'+hash+'_block').show(200);
}
});
    });
</script>
{/literal}

