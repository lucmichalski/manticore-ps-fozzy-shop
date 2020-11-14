/**
*  2007-2017 PrestaShop
*
*  @author    Amazzing
*  @copyright Amazzing
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

var ajax_action_path = window.location.href.split('#')[0]+'&ajax=1',
	blockAjax = false,
	bulk_click = [],
	refresh_patterns = 0,
	savePatternTimer,
	saveAutoFillTimer;

$(document).ready(function(){
	$('ul.nav.nav-pills').prepend('<li class="li-docs"></li>');
	$('#module-documentation').prependTo('.li-docs').removeClass('hidden');
	$('.resource-types').change();
}).on('click', 'a[href="#"]', function(e){
	e.preventDefault();
}).on('change', '.list-trigger, .npp', function(){
	var resourceType = $('select[name="resource_type"]').val(),
		orderBy = $('select[name="order_by"]').val();
	if (resourceType  == 'cms' && (orderBy == 'name' || orderBy == 'main.date_add')) {
			$('select[name="order_by"]').val('main.id');
	}
	// manufacturers and suppliers don't have database values for link_rewrite
	if ((resourceType == 'manufacturer' || resourceType == 'supplier') && orderBy == 'link_rewrite') {
			$('select[name="order_by"]').val('name');
	}
	if ($(this).hasClass('resource-types')) {
		var val = $(this).val(),
			available_varables = $(this).find('option:selected').data('variables');
		$('.available-variables').find('span').html(available_varables);
		$('.special-option').addClass('hidden');
		$('.special-option.'+val+'-option').removeClass('hidden');
		$('.has-exclusions').removeClass('hidden');
		$('.has-exclusions.not-for-'+val).addClass('hidden');
		$('.order-way.active').removeClass('active');
	}
	if ($(this).hasClass('way')) {
		$(this).toggleClass('active').siblings().toggleClass('active');
	}
	if ($(this).hasClass('resource-types') || $(this).hasClass('languages')) {
		refresh_patterns = 1;
	}
	if ($(this).hasClass('additional-filter')) {
		$(this).toggleClass('not-empty', $(this).val() != '-');
		var $additionalFilters = $(this).closest('.additional-filters');
		$additionalFilters.toggleClass('filters-activated', $additionalFilters.find('.not-empty').length > 0);
	}
	callResourseList(1);
}).on('click', '.clear-filters', function(){
	blockAjax = true;
	$(this).closest('.additional-filters').find('.additional-filter').val('-').change();
	blockAjax = false;
	callResourseList(1);
}).on('change', '.meta-checkbox', function(){
	var checked = $(this).prop('checked'),
		response = function(r) {
			callResourseList(1);
		};
	$(this).parent().next().toggleClass('hidden', !checked);
	savePatterns(response);
}).on('click', '.go-to-page', function(){
	callResourseList($(this).data('page'));
}).on('click', '.list-order-way', function(){
	var by = $(this).data('by'),
		way = $(this).data('way');
	$('input[name="order_way"]').each(function(){
		var checked = $(this).val() == way;
		$(this).prop('checked', checked);
		if (checked) {
			$(this).parent().addClass('active').siblings().removeClass('active');
		}
	});
	$('select[name="order_by"]').val(by).change();
}).on('click', '.filter-items', function(){
	callResourseList(1);
}).on('focusin', '.pattern, .length', function(e) {
	$(this).data('initial-value', $(this).val());
}).on('focusout keyup', '.pattern, .length', function(e){
	var $el = $(this),
		blocked = $el.val() != $el.data('initial-value');
	$('bulk-generate.blocked, .generate-meta').toggleClass('blocked', blocked);
	clearTimeout(savePatternTimer);
	savePatternTimer = setTimeout(function() {
		if ($el.val() != $el.data('initial-value')) {
			var response = function(r) {
				if ('savedTxt' in r) {
					$.growl.notice({title: '', message: utf8_decode(r.savedTxt)});
					$('bulk-generate.blocked, .generate-meta').removeClass('blocked');
				}
			};
			$el.data('initial-value', $el.val());
			savePatterns(response);
		}
	}, 700);
}).on('click', '.reset-filters', function(){
	$('tr.filter').find('input[type="text"]').val('');
	callResourseList(1);
}).on('click', '.chk-action', function(){
	var $checkboxes = $('input[name="bulk-items[]"]');
	if ($(this).hasClass('all')){
		$checkboxes.prop('checked', true);
	} else if ($(this).hasClass('none')){
		$checkboxes.prop('checked', false);
	} else if ($(this).hasClass('invert')){
		$checkboxes.each(function(){
			$(this).prop('checked', !$(this).prop('checked'));
		});
	}
}).on('click', '.bulk-generate', function(){
	bulk_click = [];
	$(this).closest('.resource-list').find('input[name="bulk-items[]"]:checked').each(function(){
		$(this).closest('tr').find('.generate-meta').each(function(){
			bulk_click.push($(this));
		});
	});
	if (bulk_click.length){
		bulk_click[0].click();
		$('html, body').animate({
			scrollTop: bulk_click[0].closest('tr').offset().top	- 150
		}, 700);
	}
}).on('keypress', 'input.filter', function(e){
	if (e.which == 13 || e.keyCode  == 13){
		$('.filter-items').click();
	}
}).on('click', '.generate-meta', function(){
	if (!$(this).hasClass('blocked')) {
		generateMeta($(this));
	}
}).on('keypress', '.editable-value', function(e){
	if (e.which == 13 || e.keyCode  == 13) {
		e.preventDefault();
		$(this).blur();
	}
}).on('focusout', '.editable-value', function(e){
	$(this).closest('td').addClass('loading');
	generateMeta($(this));
}).on('keyup', '.editable-value', function(e){
	fillCounter($(this));
}).on('click', '.value-holder', function(){
	$(this).find('.editable-value').removeClass('just-saved')
	.closest('td').addClass('edit').removeClass('just-saved')
	.parent().find('.generate-meta').addClass('hidden');
	$(this).find('.editable-value').each(function(){
		fillCounter($(this));
	});
}).on('click', '.img-nav', function(e){
	swapImg($(this));
}).on('click', '.close-dynamic-images', function(){
	$(this).closest('.dynamic-images').remove();
}).on('change', '.auto-fill-resource', function(){
	clearTimeout(saveAutoFillTimer);
	saveAutoFillTimer = setTimeout(function() {
		var	params = '&action=SaveAutoFillParams&'+$('.auto-fill-meta-tags-form').serialize();
			response = function(r){
				if (r.saved) {
					$.growl.notice({ title: '', message: savedTxt});
				}
			};
		ajaxRequest(params, response);
	}, 700);
});

function savePatterns(response) {
	var	params = '&action=SavePatterns&'+$('.meta-form').serialize(),
		response = typeof response == 'undefined' ? function(r){} : response;
	ajaxRequest(params, response);
}

function generateMeta($el) {
	var $tr = $el.closest('tr'),
		params = 'action=GenerateMeta&'+$('.meta-form').serialize()+'&id='+$tr.data('id');
	if ($el.hasClass('editable-value')) {
		params += '&forced_meta='+$el.data('meta')+'&forced_value='+encodeURIComponent($.trim($el.text()));
		if ($el.hasClass('img-legend')) {
			params += '&forced_meta=legend&forced_legend_id='+$el.data('id');
		}
	}
	var	response = function(r){
		$el.find('i.icon-spin').removeClass('icon-spin');
		$tr.find('.loading, .just-saved').removeClass('loading just-saved');
		if ('errors' in r) {
			$('.resource-list').prepend(utf8_decode(r.errors));
			$('html, body').animate({
				scrollTop: $('.resource-list').offset().top	- 150
			}, 700);
		} else {
			$tr.find('.editable-value').each(function(){
				var metaType = $(this).data('meta');
				if (metaType in r.updated_fields) {
					var newVal = utf8_decode(r.updated_fields[metaType]);
					$(this).html(newVal).addClass('just-saved').closest('td').removeClass('edit');
				}
			});

			if (!$tr.find('.edit').length) {
				$tr.find('.generate-meta').removeClass('hidden');
			}
			bulk_click.shift();
			if (bulk_click.length){
				bulk_click[0].click();
			}
		}
	};
	$('.resource-list .thrown-errors').remove();
	$el.find('i').addClass('icon-spin');
	ajaxRequest(params, response);
}

function callResourseList(p){
	var params = 'action=CallResourseList&'+$('.meta-form').serialize()+'&p='+p+'&refresh_patterns='+refresh_patterns;
	if ($('.npp').length) {
		params += '&npp='+$('.npp').val();
	}
	$('tr.filter').find('input[type="text"]').each(function(){
		if ($(this).val()){
			params += '&'+$(this).data('filter-by')+'='+$(this).val();
		}
	});

	var response = function(r) {
		if ('errors' in r) {
			$('.resource-list').prepend(utf8_decode(r.errors));
		} else {
			$('.resource-list').html(utf8_decode(r.list));
			prepareCarousels();
			if (refresh_patterns) {
				$('.pattern').each(function(){
					var name = $(this).attr('name').replace('patterns[', '').replace('][value]', '');
					if (name in r.patterns) {
						var pattern = r.patterns[name],
							active = 'active' in pattern;
						$(this).val(utf8_decode(pattern['value']));
						$(this).closest('.meta-details').find('.length').val(pattern['length']);
						blockAjax = true;
						$(this).closest('.meta-details').toggleClass('hidden', !active);
						$(this).closest('.meta-type').find('.meta-checkbox').prop('checked', active);
						blockAjax = false;
					}
				});
			}
		}
		refresh_patterns = 0;
	};
	$('.resource-list').children('table').css('opacity', 0.5);
	ajaxRequest(params, response);
}

function prepareCarousels() {
	$('.img-list').each(function(){
		$(this).find('.img-item').first().addClass('visible');
	});
	setTimeout(function(){
		$('.dynamic-src').each(function(i){
			updateSrc($(this));
		});
	}, 300);
}

function fillCounter($el) {
	var meta_type = $el.hasClass('img-legend') ? 'img_legend' : $el.data('meta'),
		max = $('input[name="patterns['+meta_type+'][length]"]').val() || '--',
		current = $.trim($el.text()).length,
		$counter = $el.prev();
	$counter.find('.current').html(current).siblings().html(max);
	if (current > max) {
		$counter.addClass('alert-danger');
	} else if ($counter.hasClass('alert-danger')){
		$counter.removeClass('alert-danger');
	}
}

function swapImg($btn) {
	var $currentItem = $btn.closest('td').find('.img-item.visible');
	if ($btn.data('direction') == 'next') {
		$newItem = $currentItem.next().length ? $currentItem.next() : $currentItem.prevAll().last();
	} else {
		$newItem = $currentItem.prev().length ? $currentItem.prev() : $currentItem.nextAll().last();
	}
	$newItem.addClass('visible').siblings().removeClass('visible');

}

function updateSrc($el) {
	$el.attr('src', $el.data('src')).removeClass('dynamic-src');
}

function ajaxRequest(params, response){
	if (blockAjax) {
		return;
	}
	$.ajax({
		type: 'POST',
		url: ajax_action_path,
		data: params,
		dataType : 'json',
		success: function(r) {
			console.dir(r);
			response(r);
		},
		error: function(r) {
			console.warn($(r.responseText).text() || r.responseText);
		}
	});
}

function utf8_decode (utfstr) {
	var res = '';
	for (var i = 0; i < utfstr.length;) {
		var c = utfstr.charCodeAt(i);
		if (c < 128) {
			res += String.fromCharCode(c);
			i++;
		} else if((c > 191) && (c < 224)) {
			var c1 = utfstr.charCodeAt(i+1);
			res += String.fromCharCode(((c & 31) << 6) | (c1 & 63));
			i += 2;
		} else {
			var c1 = utfstr.charCodeAt(i+1);
			var c2 = utfstr.charCodeAt(i+2);
			res += String.fromCharCode(((c & 15) << 12) | ((c1 & 63) << 6) | (c2 & 63));
			i += 3;
		}
	}
	return res;
}
/* since 1.6.2 */
