jQuery(function($){$('body').on('click','.lws-woorewards-social-button',function(event){$.ajax({url:lws_ajax.url,data:{action:'lws_woorewards_social_sharing',nonce:$(event.target).data('n'),s:$(event.target).data('s'),p:$(event.target).data('p')}}).fail(function(jqXHR,textStatus){console.log(textStatus)});if($(event.target).data('popup')=='on'){window.open($(event.target).attr('href'),'wr-social-share','directories=no, location=no, width=640, height=480, menubar=no, status=no, scrollbars=no, menubar=no');return!1}})})