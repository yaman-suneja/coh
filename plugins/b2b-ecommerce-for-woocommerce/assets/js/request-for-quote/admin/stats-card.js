jQuery(document).ready(function($){

	ajax_url = a_vars.url;

	var card_is_hide = false;
    var intervalId = window.setInterval(function(){
	    	if(card_is_hide == true){
	    		clearInterval(intervalId);
	    	}
	    	if( jQuery('a[name="wcb2be_rfq_stats_card"]').length ){
	    		card_is_hide = true;
	    		
	    		jQuery.ajax({
                    url: ajax_url,
                    type: "get",
                    data: {action: "wcb2be_ajax_card_rfq_stats",},
                    success: function (response) {
                       jQuery('a[name="wcb2be_rfq_stats_card"]').after( response );
                       jQuery('a[name="wcb2be_rfq_stats_card"]').remove();
                    }
                });

	    	}
    	}, 
    	2000
    );

});