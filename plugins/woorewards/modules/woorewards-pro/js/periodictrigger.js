jQuery(function($){$('body').on('change','.lws_adm_durationfield .lws_adm_lifetime_check',function(){var visible=$(this).prop('checked');var root=$(this).closest('.lws_adm_durationfield');var master=root.find('.lws_adm_lifetime_master');if(master.length){var suffix='[period]';var name=master.attr('name');name=name.substr(0,name.length-suffix.length)+'[date]';$('input[name="'+name+'"]').toggle(visible)}})})