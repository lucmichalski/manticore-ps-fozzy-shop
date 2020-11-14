{**
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * We are experts and professionals in PrestaShop
 *
 * @author Elcommerce <support@elcommece.com.ua>
 * @copyright 2010-2018 Elcommerce
 * @license Comercial
 * @category PrestaShop
 * @category Module
*}

<div class="panel-heading"><i class="icon-cogs"></i> {l s='Customs' mod='ecm_checkout'}</div>
<form id="custom_set" action="{$currentIndex}&token={$token}" method="post" enctype="multipart/form-data">
	<fieldset class="space">
		<div class="row">
			<div class="form-group">
				<label class="control-label col-lg-2">Custom CSS Code:</label>
				<div class="col-lg-10">
					<textarea name="custom_css" id="custom_css" cols="80" rows="20" class="mytextarea textarea-autosize editor" 
					spellcheck="false"
					style="overflow: hidden; overflow-wrap: break-word; resize: none; height: 31px;">{$custom_css}</textarea>
					{*<pre><code class="syntax-highight html"></code></pre>*}
					<p class="help-block">
					{l s='Add your CSS custom code' mod='ecm_checkout'}
					</p>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-2">Custom JS Code:</label>
				<div class="col-lg-10">
					<textarea name="custom_js" id="custom_js" cols="80" rows="20" class="textarea-autosize editor" 
					spellcheck="false"
					style="overflow: hidden; overflow-wrap: break-word; resize: none; height: 31px;">{$custom_js}</textarea>
					<p class="help-block">
					{l s='Add your JS custom code' mod='ecm_checkout'}
					</p>
				</div>
			</div>
		</div>
		<div class="panel-footer">
			<button type="submit" name="submit_customs" class="btn btn-default pull-right">
			<i class="process-icon-save"></i> {l s='Save customs settings' mod='ecm_checkout'}
			</button>
		</div>
	</fieldset>
</form>









{*
<style>
	mytextarea, code{
		font-family: Consolas,Liberation Mono,Courier,monospace;
		font-size: 14px;
		margin: 0;
	}
	
	mytextarea{
		background: transparent !important;
		z-index: 2;
		height: auto;
		resize: none;
		color: #fff;
    text-shadow: 0px 0px 0px rgba(0, 0, 0, 0);
    text-fill-color: transparent;
		-webkit-text-fill-color: transparent;
		
		&::-webkit-input-placeholder{
			color: rgba(255, 255, 255, 1);
		}
		
		&:focus{
			outline: 0;
			border: 0;
			-webkit-box-shadow: none;
			-moz-box-shadow: none;
			box-shadow: none;
		}
	}

	code{
		z-index: 1;
	}
}

pre {
	white-space: pre-wrap;  
	white-space: -moz-pre-wrap;
	white-space: -pre-wrap;  
	white-space: -o-pre-wrap;
	word-wrap: break-word;
	code{
		
		.hljs {
			color: #a9b7c6;
			display: block;
			overflow-x: auto;
			padding: 0.5em
		}
		.hljs-number,
		.hljs-literal,
		.hljs-symbol,
		.hljs-bullet {
				color: #6897BB
		}
		.hljs-keyword,
		.hljs-selector-tag,
		.hljs-deletion {
				color: #cc7832
		}
		.hljs-variable,
		.hljs-template-variable,
		.hljs-link {
				color: #629755
		}
		.hljs-comment,
		.hljs-quote {
				color: #808080
		}
		.hljs-meta {
				color: #bbb529
		}
		.hljs-string,
		.hljs-attribute,
		.hljs-addition {
				color: #6A8759
		}
		.hljs-section,
		.hljs-title,
		.hljs-type {
				color: #ffc66d
		}
		.hljs-name,
		.hljs-selector-id,
		.hljs-selector-class {
				color: #e8bf6a
		}
		.hljs-emphasis {
				font-style: italic
		}
		.hljs-strong {
				font-weight: bold
		}
	}
}
</style>
<script>
var tabCharacter = "  ";
var tabOffset = 2;

$(document).on('click', '#indent', function(e){
	e.preventDefault();
	var self = $(this);
	
	self.toggleClass('active');
	
	if(self.hasClass('active'))
	{
		tabCharacter = "\t";
		tabOffset = 1;
	}
	else
	{
		tabCharacter = "  ";
		tabOffset = 2;
	}
})

$(document).on('click', '#fullscreen', function(e){
	e.preventDefault();
	var self = $(this);
	
	self.toggleClass('active');
	self.parents('.editor-holder').toggleClass('fullscreen');
});

/*------------------------------------------
	Render existing code
------------------------------------------*/
$(document).on('ready', function(){
	hightlightSyntax();
	
	emmet.require('.mytextarea').setup({
    pretty_break: true,
    use_tab: true
	});
});




/*------------------------------------------
	Capture text updates
------------------------------------------*/
$(document).on('ready load keyup keydown change', '.editor', function(){
	correctTextareaHight(this);
	hightlightSyntax();
});


/*------------------------------------------
	Resize textarea based on content  
------------------------------------------*/
function correctTextareaHight(element)
{
  var self = $(element),
      outerHeight = self.outerHeight(),
      innerHeight = self.prop('scrollHeight'),
      borderTop = parseFloat(self.css("borderTopWidth")),
      borderBottom = parseFloat(self.css("borderBottomWidth")),
      combinedScrollHeight = innerHeight + borderTop + borderBottom;
  
  if(outerHeight < combinedScrollHeight )
  {
    self.height(combinedScrollHeight);
  }
}
// function correctTextareaHight(element){
// 	while($(element).outerHeight() < element.scrollHeight + parseFloat($(element).css("borderTopWidth")) + parseFloat($(element).css("borderBottomWidth"))) {
// 		$(element).height($(element).height()+1);
// 	};
// }


/*------------------------------------------
	Run syntax hightlighter  
------------------------------------------*/
function hightlightSyntax(){
	var me  = $('.editor');
	var content = me.val();
	var codeHolder = $('code');
	var escaped = escapeHtml(content);
	
	codeHolder.html(escaped);
	
	$('.syntax-highight').each(function(i, block) {
		hljs.highlightBlock(block);
	});
}


/*------------------------------------------
	String html characters
------------------------------------------*/
function escapeHtml(unsafe) {
	return unsafe
			 .replace(/&/g, "&amp;")
			 .replace(/</g, "&lt;")
			 .replace(/>/g, "&gt;")
			 .replace(/"/g, "&quot;")
			 .replace(/'/g, "&#039;");
}


/*------------------------------------------
	Enable tabs in textarea
------------------------------------------*/
$(document).delegate('.allow-tabs', 'keydown', function(e) {
	var keyCode = e.keyCode || e.which;

	if (keyCode == 9) {
		e.preventDefault();
		var start = $(this).get(0).selectionStart;
		var end = $(this).get(0).selectionEnd;

		// set textarea value to: text before caret + tab + text after caret
		$(this).val($(this).val().substring(0, start)
								+ tabCharacter
								+ $(this).val().substring(end));

		// put caret at right position again
		$(this).get(0).selectionStart =
		$(this).get(0).selectionEnd = start + tabOffset;
	}
});

</script>

*}