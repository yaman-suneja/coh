jQuery(function($){$(".woocommerce").on('click',".lws_woorewards_add_coupon",function(event){if($(event.currentTarget).data('reload')==undefined){$('#coupon_code').val($(event.currentTarget).data('coupon'));$(event.currentTarget).closest('.item').hide();$("input[name='apply_coupon'],button[name='apply_coupon']").trigger('click')}else{var params={};var uri=window.location.toString();if(window.location.search.length>1){var couples=window.location.search.substr(1).split("&");for(var index=0;index<couples.length;++index){couple=couples[index].split("=");var key=unescape(couple[0]);if(!key.startsWith('wrac_'))
params[key]=couple.length>1?unescape(couple[1]):''}}
uri=(uri.substring(0,uri.indexOf("?"))+'?');if(Object.keys(params).length>0)
uri+=($.param(params)+'&');document.location=(uri+$(event.currentTarget).data('reload'))}
return!1});$(".woocommerce").on('click',".woocommerce-remove-coupon",function(){$.each($(this).closest('.cart-discount').attr('class').split(' '),function(index,value){if(value.startsWith('coupon-')){$('.wr-available-coupons').find('.item.'+value).show();return!1}})})})