jQuery(function($){function getSizedUrl(attr,size){var url=attr.url;if(typeof attr.sizes!='undefined'&&attr.sizes[size]){url=attr.sizes[size].url}
return url}
function makeHtmlFromMedia(attr,type,imagesize,classsize){var html='';if(type=='image'){html="<img src='"+getSizedUrl(attr,imagesize)+"'";html+=" alt='"+(typeof attr.alt!='undefined'?attr.alt:'')+"'"
html+=" title='"+(typeof attr.title!='undefined'?attr.title:'')+"'"
html+=" class='attachment-"+classsize+" size-"+classsize+"'>"}
return html}
$(".lws_adminpanel_btn_add_media").on('click',function(event){event.preventDefault();var clicked_button=$(event.target);if(clicked_button.data('gk_frame')!=undefined){clicked_button.data('gk_frame').open();return}
var gk_frame=wp.media({title:clicked_button.data('title'),multiple:!1,library:{type:clicked_button.data('type')},button:{text:clicked_button.data('pick')}});gk_frame.on('select',function(){var selection=clicked_button.data('gk_frame').state().get('selection');if(!selection)
return;var attachment=selection.first();var owner=clicked_button.closest('.lws_media_master');var addBtn=owner.find(".lws_adminpanel_btn_add_media");owner.find('.lws-adm-media').show().html(makeHtmlFromMedia(attachment.attributes,clicked_button.data('type'),clicked_button.data('image-size'),clicked_button.data('class-size')));owner.find('.lws_adminpanel_input_media_url').val(getSizedUrl(attachment.attributes,clicked_button.data('image-size')));owner.find('.lws_adminpanel_input_media_id').val(attachment.attributes.id).trigger('change');addBtn.removeClass("lws-media-add").addClass("lws-media-edit").val(addBtn.data('edit'));owner.find(".lws_adminpanel_btn_del_media").show()});clicked_button.data('gk_frame',gk_frame);clicked_button.data('gk_frame').open()});$(".lws_adminpanel_btn_del_media").on('click',function(event){event.preventDefault();var owner=$(this).closest('.lws_media_master');var addBtn=owner.find(".lws_adminpanel_btn_add_media");owner.find('.lws-adm-media').html("").hide();addBtn.removeClass("lws-media-edit").addClass("lws-media-add").val(addBtn.data('add'));owner.find(".lws_adminpanel_btn_del_media").hide();owner.find('.lws_adminpanel_input_media_url').val('');owner.find('.lws_adminpanel_input_media_id').val('').trigger('change');$(this).hide()});$('.lws_adminpanel_input_media_url').on('change',function(){var imgUrl=$(this).val();var owner=$(this).closest('.lws_media_master');if(imgUrl!=undefined&&imgUrl!=''){var btn=owner.find('.lws_adminpanel_btn_add_media');owner.find('.lws-adm-media').html(makeHtmlFromMedia({url:imgUrl},btn.data('type'),btn.data('image-size'),btn.data('class-size')));owner.find('.lws-adm-media').show();btn.removeClass("lws-media-add").addClass("lws-media-edit").val(btn.data('edit'));owner.find(".lws_adminpanel_btn_del_media").show()}else{owner.find('.lws_adminpanel_btn_del_media').trigger('click')}})})