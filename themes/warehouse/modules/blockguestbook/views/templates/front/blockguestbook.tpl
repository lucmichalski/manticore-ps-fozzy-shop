{capture name=path}
{l s='Guestbook' mod='blockguestbook'}
{/capture}



{if $blockguestbookis16 == 0}


<h2>{l s='Guestbook' mod='blockguestbook'}</h2>
{else}
<h1 class="h1 page-title">{$meta_title|escape:'htmlall':'UTF-8'}</h1>
{/if}


<div class="card">
{if $count_all_reviews > 0}

<div class="toolbar-top">
			
	<div class="sortTools margin-bottom-10">
					<h5 class="block-title">{l s='Posts' mod='blockguestbook'}  (<span id="count_items_top"  class="guestbook-count-items">{$count_all_reviews|escape:'htmlall':'UTF-8'}</span>)</h5>
	</div>

</div>

<div id="succes-review">
{l s='Your post has been successfully sent. Thanks for post!' mod='blockguestbook'}						
</div>


<div class="text-align-center margin-bottom-20" id="add_guestbook">
    <input type="button" onclick="show_guestbook_form()" value="{l s='Add Guestbook Post' mod='blockguestbook'}" class="btn btn-primary btn-lg guestbook-add-btn" />
</div>

<div id="add-guestbook-form" class="card">

    <form method="post" enctype="multipart/form-data" id="blockguestbook_form" name="blockguestbook_form">
        <input type="hidden" name="action" value="addreviewguestbook" />


	<div class="title-rev" id="idTab666-my">
		<div class="block-title">
		{l s='Add Post' mod='blockguestbook'}
		</div>

        <div class="clear"></div>
	</div>


        <div id="body-add-blockguestbook-form">

	<label for="name-review">{l s='Name' mod='blockguestbook'}:<span class="guestbook-req">*</span></label>
				<input type="text" name="name-review" id="name-review"
                       {if strlen($blockguestbookname_c)>0}value="{$blockguestbookname_c|escape:'htmlall':'UTF-8'}"{/if}
                       class="guestbook-input" onkeyup="check_inpNameReview();" onblur="check_inpNameReview();" />
                <div class="errorTxtAdd" id="error_name-review"></div>


			<label for="email-review">{l s='Email' mod='blockguestbook'}:<span class="guestbook-req">*</span></label>
				<input type="text" name="email-review"
					   id="email-review" class="guestbook-input"
                       {if strlen($blockguestbookemail_c)>0}value="{$blockguestbookemail_c|escape:'htmlall':'UTF-8'}"{/if}
                       onkeyup="check_inpEmailReview();" onblur="check_inpEmailReview();"
                        />
                <div class="errorTxtAdd" id="error_email-review"></div>

            {if $blockguestbookis_avatarg == 1}
        <label for="avatar-review">{l s='Avatar:' mod='blockguestbook'}</label>
                <input type="file" name="avatar-review"
                       id="avatar-review"
                       class="guestbook-input"
                        />
                <div class="b-guide">
                    {l s='Allow formats' mod='blockguestbook'}: *.jpg; *.jpeg; *.png; *.gif.
                </div>
                <div class="errorTxtAdd" id="error_avatar-review"></div>
            {/if}

        {if $blockguestbookis_webg == 1}
            <label>{l s='Web address:' mod='blockguestbook'}</label>
                    <input type="text" name="web-review"
                           id="web-review"
                           class="guestbook-input"
                            />

        {/if}
        {if $blockguestbookis_companyg == 1}
            <label>{l s='Company' mod='blockguestbook'}:</label>
                    <input type="text" name="company-review"
                           id="company-review"
                           class="guestbook-input"
                            />

        {/if}
        {if $blockguestbookis_addrg == 1}
            <label>{l s='Address' mod='blockguestbook'}:</label>
                    <input type="text" name="address-review"
                           id="address-review"
                           class="guestbook-input"
                            />

        {/if}

        {if $blockguestbookis_countryg == 1}
            <label>{l s='Country' mod='blockguestbook'}:</label>
                    <input type="text" name="country-review"
                           id="country-review"
                           class="guestbook-input"
                            />

        {/if}

        {if $blockguestbookis_cityg == 1}
            <label>{l s='City' mod='blockguestbook'}:</label>
                    <input type="text" name="city-review"
                           id="city-review"
                           class="guestbook-input"
                            />

        {/if}
		<label for="text-review">{l s='Post' mod='blockguestbook'}:<span class="guestbook-req">*</span></label>

				<textarea class="guestbook-textarea"
						  id="text-review"
						  name="text-review" onkeyup="check_inpMsgReview();" onblur="check_inpMsgReview();"></textarea>
                <div class="errorTxtAdd" id="error_text-review"></div>

            {* gdpr *}
            {hook h='displayGDPRConsent' mod='psgdpr' id_module=$id_module}
            {* gdpr *}

		{if $blockguestbookis_captchag == 1}

            <label>{l s='Captcha' mod='blockguestbook'}</label>
            <div class="clr"></div>
			<img width="100" height="26" class="float-left" id="secureCodReview" src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/blockguestbook/captcha.php" alt="Captcha" />
			<input type="text" class="inpCaptchaReview float-left" id="inpCaptchaReview"  name="captcha" size="6" onkeyup="check_inpCaptchaReview();" onblur="check_inpCaptchaReview();" />
				  <div class="clr"></div>
						  
			<div id="error_inpCaptchaReview" class="errorTxtAdd"></div>	

		{/if}
        </div>


		<div id="footer-add-blockguestbook-form-blockguestbook"  class="form-right block-add-guestbook-button">
                <input type="submit" name="submit_blockguestbook" value="{l s='Add Post' mod='blockguestbook'}" class="btn btn-primary btn-lg guestbook-add-btn" />
            </div>

        </form>
</div>

<div id="list_reviews" class="productsBox1">
{foreach from=$reviews_items item=review name=myLoop}
	<table cellspacing="0" cellpadding="0" border="0" width="100%" class="productsTable compareTableNew {if $blockguestbookis16==1}float-left-table16{/if}">
		<tbody>
			<tr class="line1">
                {if $blockguestbookis_avatarg == 1}
                <td class="post_avatar">
                    <img
                            {if strlen($review.avatar)>0}
                                src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}{$blockguestbookpic|escape:'htmlall':'UTF-8'}{$review.avatar|escape:'htmlall':'UTF-8'}"
                            {else}
                                src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/blockguestbook/views/img/avatar_m.gif"
                            {/if}
                            alt="{$review.name|escape:'htmlall':'UTF-8'}"
                            />

                </td>
                {/if}
			<td class="info card">
				<span class="commentbody_center">
				{$review.message nofilter}

                    {if $review.is_show == 1}
                        <div class="admin-reply-on-guestbook">
                            <div class="owner-date-reply">{l s='Administrator' mod='blockguestbook'}: </div>
                            {$review.response nofilter}
                        </div>
                    {/if}
                </span>
               <div class="clear"></div>
				<span class="foot_center margin-top-10">{l s='Posted by' mod='blockguestbook'} {$review.name|escape:'htmlall':'UTF-8' nofilter}</b>

                    {if $blockguestbookis_countryg == 1}
                        {if strlen($review.country)>0}
                            , <span class="fs-12">{$review.country|escape:'htmlall':'UTF-8' nofilter}</span>
                        {/if}
                    {/if}
                    {if $blockguestbookis_cityg == 1}
                        {if strlen($review.city)>0}
                            , <span class="fs-12">{$review.city|escape:'htmlall':'UTF-8' nofilter}</span>
                        {/if}
                    {/if}
                </span>
                    <span class="foot_center">{$review.date_add|date_format:"%d-%m-%Y"|escape:'htmlall':'UTF-8'}</span>
                    <span class="foot_center">
				{if $blockguestbookis_companyg == 1}
                    <b>{$review.company|escape:'htmlall':'UTF-8' nofilter}</b>
                {/if}

                        {if $blockguestbookis_addrg == 1}
                            <b>{$review.address|escape:'htmlall':'UTF-8' nofilter}</b>
                        {/if}

                        {if $blockguestbookis_webg == 1}
                            {if strlen($review.web)>0}
                                <a title="http://{$review.web|escape:'htmlall':'UTF-8' nofilter}" rel="nofollow"
                                   href="http://{$review.web|escape:'htmlall':'UTF-8' nofilter}">http://{$review.web|escape:'htmlall':'UTF-8' nofilter}</a>
                            {/if}
                        {/if}
				</span>

				</span>
			</td>
			</tr>
		</tbody>
	</table>
{/foreach}
{if $blockguestbookis16==1}<div class="clear"></div>{/if}
</div>


<div class="toolbar-bottom">
			
	<div class="sortTools {if $blockguestbookis16 == 1}sortTools16{/if}" id="show">

				<div class="page-list clearfix text-center" id="page_nav">
					{$paging|escape:'quotes':'UTF-8' nofilter}
				</div>

			</div>

		</div>
{else}
	<div class="guestbook-no-items">
	{l s='There are not posts yet' mod='blockguestbook'}
	</div>
{/if}

</div>

<br/>



{literal}
<script type="text/javascript">

    setTimeout(function() {
        $('#footer-add-blockguestbook-form-blockguestbook').find('input[name="submit_blockguestbook"]').removeAttr('disabled');
    }, 1000);


    function field_gdpr_change_blockguestbook(){
        // gdpr
        var gdpr_blockguestbook = $('#psgdpr_consent_checkbox_{/literal}{$id_module|escape:'htmlall':'UTF-8'}{literal}');

        var is_gdpr_blockguestbook = 1;

        if(gdpr_blockguestbook.length>0){

            if(gdpr_blockguestbook.prop('checked') == true) {
                $('.gdpr_module_{/literal}{$id_module|escape:'htmlall':'UTF-8'}{literal} .psgdpr_consent_message').removeClass('error-label');
            } else {
                $('.gdpr_module_{/literal}{$id_module|escape:'htmlall':'UTF-8'}{literal} .psgdpr_consent_message').addClass('error-label');
                is_gdpr_blockguestbook = 0;
            }

            $('#psgdpr_consent_checkbox_{/literal}{$id_module|escape:'htmlall':'UTF-8'}{literal}').on('click', function(){
                if(gdpr_blockguestbook.prop('checked') == true) {
                    $('.gdpr_module_{/literal}{$id_module|escape:'htmlall':'UTF-8'}{literal} .psgdpr_consent_message').removeClass('error-label');
                } else {
                    $('.gdpr_module_{/literal}{$id_module|escape:'htmlall':'UTF-8'}{literal} .psgdpr_consent_message').addClass('error-label');
                }
            });

        }

        //gdpr

        return is_gdpr_blockguestbook;
    }


    function check_inpNameReview()
    {

        var name_review = trim(document.getElementById('name-review').value);

        if (name_review.length == 0)
        {
            field_state_change_blockguestbook('name-review','failed', '{/literal}{$blockguestbookmsg2|escape:'htmlall':'UTF-8'}{literal}');
            return false;
        }
        field_state_change_blockguestbook('name-review','success', '');
        return true;
    }


    function check_inpEmailReview()
    {

        var email_review = trim(document.getElementById('email-review').value);

        if (email_review.length == 0)
        {
            field_state_change_blockguestbook('email-review','failed', '{/literal}{$blockguestbookmsg3|escape:'htmlall':'UTF-8'}{literal}');
            return false;
        }
        field_state_change_blockguestbook('email-review','success', '');
        return true;
    }

    function check_inpMsgReview()
    {

        var subject_review = trim(document.getElementById('text-review').value);

        if (subject_review.length == 0)
        {
            field_state_change_blockguestbook('text-review','failed', '{/literal}{$blockguestbookmsg4|escape:'htmlall':'UTF-8'}{literal}');
            return false;
        }
        field_state_change_blockguestbook('text-review','success', '');
        return true;
    }


    {/literal}{if $blockguestbookis_captchag == 1}{literal}
    function check_inpCaptchaReview()
    {

        var inpCaptchaReview = trim(document.getElementById('inpCaptchaReview').value);

        if (inpCaptchaReview.length != 6)
        {
            field_state_change_blockguestbook('inpCaptchaReview','failed', '{/literal}{$blockguestbookmsg5|escape:'htmlall':'UTF-8'}{literal}');
            return false;
        }
        field_state_change_blockguestbook('inpCaptchaReview','success', '');
        return true;
    }
    {/literal}{/if}{literal}


    document.addEventListener("DOMContentLoaded", function(event) {
    $(document).ready(function (e) {
        $("#blockguestbook_form").on('submit',(function(e) {


            {/literal}{if $blockguestbookis_avatarg == 1}{literal}
            field_state_change_blockguestbook('avatar-review','success', '');
            {/literal}{/if}{literal}



            var is_name_review = check_inpNameReview();
            var is_email_review = check_inpEmailReview();
            var is_msg_review =check_inpMsgReview();
            {/literal}{if $blockguestbookis_captchag == 1}{literal}
            var is_captcha_review = check_inpCaptchaReview();
            {/literal}{/if}{literal}

            // gdpr
            var is_gdpr_blockguestbook = field_gdpr_change_blockguestbook();

            if(is_name_review && is_email_review && is_msg_review

                    && is_gdpr_blockguestbook //gdpr

                    {/literal}{if $blockguestbookis_captchag == 1}{literal}
                    && is_captcha_review
                    {/literal}{/if}{literal}
            ){

                $('#add-guestbook-form').css('opacity','0.5');


                e.preventDefault();
                $.ajax({
                    url: baseDir + 'modules/blockguestbook/ajax.php',
                    type: "POST",
                    data:  new FormData(this),
                    contentType: false,
                    cache: false,
                    processData:false,
                    dataType: 'json',
                    success: function(data)
                    {

                        if (data.status == 'success') {

                            $('#name-review').val('');
                            $('#email-review').val('');
                            $('#web-review').val('');

                            $('#country-review').val('');
                            $('#city-review').val('');

                            $('#company-review').val('');
                            $('#address-review').val('');
                            $('#text-review').val('');
                            $('#inpCaptchaReview').val('');

                            $('#add-guestbook-form').hide();

                            $('#succes-review').show();



                            {/literal}{if $blockguestbookis_captchag == 1}{literal}
                            var count = Math.random();
                            document.getElementById('secureCodReview').src = "";
                            document.getElementById('secureCodReview').src = "{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/blockguestbook/{literal}captcha.php?re=" + count;
                            {/literal}{/if}{literal}


                            $('#add-guestbook-form').css('opacity','1');


                        } else {

                            $('#add-guestbook-form').css('opacity','1');

                            var error_type = data.params.error_type;

                            if(error_type == 1){
                                field_state_change_blockguestbook('name-review','failed', '{/literal}{$blockguestbookmsg2|escape:'htmlall':'UTF-8'}{literal}');
                                return false;
                            } else if(error_type == 2){
                                field_state_change_blockguestbook('email-review','failed', '{/literal}{$blockguestbookmsg6|escape:'htmlall':'UTF-8'}{literal}');
                                return false;
                            } else if(error_type == 3){
                                field_state_change_blockguestbook('text-review','failed', '{/literal}{$blockguestbookmsg4|escape:'htmlall':'UTF-8'}{literal}');
                                return false;
                            } else if(error_type == 8){
                                field_state_change_blockguestbook('avatar-review','failed', '{/literal}{$blockguestbookmsg8|escape:'htmlall':'UTF-8'}{literal}');
                                return false;
                            } else if(error_type == 9){
                                field_state_change_blockguestbook('avatar-review','failed', '{/literal}{$blockguestbookmsg9|escape:'htmlall':'UTF-8'}{literal}');
                                return false;
                            }
                            {/literal}{if $blockguestbookis_captchag == 1}{literal}
                            else if(error_type == 4){
                                field_state_change_blockguestbook('inpCaptchaReview','failed', '{/literal}{$blockguestbookmsg7|escape:'htmlall':'UTF-8'}{literal}');
                                var count = Math.random();
                                document.getElementById('secureCodReview').src = "";
                                document.getElementById('secureCodReview').src = "{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/blockguestbook/{literal}captcha.php?re=" + count;
                                return false;
                            }
                            {/literal}{/if}{literal}
                            else {
                                alert(data.message);
                                return false;
                            }

                            {/literal}{if $blockguestbookis_captchag == 1}{literal}
                            var count = Math.random();
                            document.getElementById('secureCodReview').src = "";
                            document.getElementById('secureCodReview').src = "{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/blockguestbook/{literal}captcha.php?re=" + count;
                            {/literal}{/if}{literal}



                        }

                    }

                });

            } else {
                return false;
            }

        }));


    });
    });



</script>
{/literal}