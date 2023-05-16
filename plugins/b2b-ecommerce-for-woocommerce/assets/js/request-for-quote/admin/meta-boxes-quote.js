jQuery( function ( $ ) {

    /**
     * Quote Data Panel
     */
    var cwcrfq_meta_boxes_quote = {
            states: null,
            init: function() {}

    };
    var cwcrfq_meta_boxes_quote_items = {
        init: function() {
            $( '#wcrfq-quote-item' )
            .on( 'click', 'button.submit-quote-action', this.submit_quote );
        },
        submit_quote: function() {
            var wpnonce = $("#_wpnonce").val();
            
            var data = {
                action:   'codup_rfq_submit_quote',
		        quote_id: codup_rfq_admin_meta_boxes_quote.post_id,
				wpnonce: wpnonce,	
            } ;
            
            $.ajax({
                url:  codup_rfq_admin_meta_boxes_quote.ajax_url,
                data: data,
                type: 'POST',
                success: function( response ) {

                },
                complete: function( response ) {
                    window.location.reload(false); 
                }
            });
        }
    };
    cwcrfq_meta_boxes_quote_items.init();
    
    $( "#rfq_admin_comment_submit_button" ).click(function() {

        var wpnonce = $("#admin-comment-meta-action").val();

        var rfq_admin_comment_quote_id = $( "#rfq_admin_comment_quote_id" ).val();
        var rfq_admin_comment_textarea = $( "#rfq_admin_comment_textarea" ).val();
        var rfq_admin_comment_author   = $( "#rfq_admin_comment_author" ).val();

        if(rfq_admin_comment_textarea != ''){
            $.ajax({
                url: codup_rfq_admin_meta_boxes_quote.ajax_url,
                type:"POST",
                data: {
                    action: 'save_admin_comment',
                    'rfq_admin_comment_quote_id': rfq_admin_comment_quote_id,
                    'rfq_admin_comment_textarea': rfq_admin_comment_textarea,
                    'rfq_admin_comment_author'  : rfq_admin_comment_author,
                    wpnonce: wpnonce,
                },
                success: function(data) {
                    $('#rfq_admin_comment_box').append(
                        
                        "<tr style='height: 65px;width: 100%;'>"+
                            "<td style='vertical-align: top;padding: 8px 10px;width:25%;word-wrap: break-word;'>"+
                                "<div style='font-size: 13px;line-height: 1.5em;color:#555'>"+
                                    "<strong>"+ 
                                        rfq_admin_comment_author + 
                                    "</strong>"+
                                "</div>"+
                            "</td>"+
                            "<td style='vertical-align: top;padding: 8px 10px;width:75%;word-wrap: break-word;'>"+
                                "<div style='font-size: 13px;line-height: 1.5em;'>"+
                                    rfq_admin_comment_textarea +
                                "</div>"+
                            "</td>"+
                        "</tr>"
                    );
                },
                error: function(e){
                    alert('Error Sending Comment!!');
                }
            });
            $( "#rfq_admin_comment_textarea" ).val('');
        }
    });
    
    $( "#admin_save_qoute_button" ).click(function() {

        var rfq_admin_qoute_id = $( "#admin_quote_id" ).val();

        var admin_quote_total_quantity = []
        var admin_quote_total_price = []
        var nonce = $('rfq_metabox_item_settings').val();
        var products_detail = [];
        arrayFromPhp.map(order_details => {

            var total_quantity = Number($(`#edit_qty_field_value_${order_details}`).val())
            var total_price = Number($(`#edit_price_field_value_${order_details}`).val())
            var actual_price = Number($(`#actual_price_${order_details}`)[0].innerText)

            var obj = {
                [order_details] : {
                    "qty": total_quantity, "price": total_price , "actual_price":actual_price
                }
            }
            
            products_detail = [...products_detail, obj]

        })

        $.ajax({
            url: codup_rfq_admin_meta_boxes_quote.ajax_url,
            type:"POST",
            data: {
                action: 'save_item_meta_box_values',
                'products_detail': products_detail,
                'rfq_admin_qoute_id': rfq_admin_qoute_id,
                'rfq_nonce' : nonce,
            },
            success: function(data) {
                location.reload();
            },
            error: function(e){
                alert('Error Occured!!');
            }
        });
    });

    $( ".delete-order-item" ).click(function() {
        
        var product_id = $('.delete-order-item').attr('id');
        var rfq_admin_qoute_id = $( "#admin_quote_id" ).val();
        var nonce = $('rfq_metabox_item_settings').val();

        $.ajax({
            url: codup_rfq_admin_meta_boxes_quote.ajax_url,
            type:"POST",
            data: {
                action: 'delete_line_item',
                'product_id': product_id,
                'rfq_admin_qoute_id': rfq_admin_qoute_id,
                'rfq_nonce' : nonce,
            },
            success: function(data) {
                location.reload();
            },
            error: function(e){
                alert('Error Occured!!');
            }
        });

    });
    function scrollBottom(transVal){
        var height = 0;
        jQuery('#rfq_customer_comments_box tr').each(function(i, value){
            height += parseInt(jQuery(this).height());
        });
        height += '';
        jQuery('#rfq_customer_comments_box').animate({scrollTop: height},transVal); 
    }
    scrollBottom(0);
    jQuery('#rfq_admin_comment_submit_button').click(function(){
        scrollBottom(3000);
    })

    $(".input-quantity").on("change",function(){

        var this_id = $(this).attr("id");

        var product_id = this_id.split("edit_qty_field_value_")[1];
        
        var qty = $(this).val();
        var cost = $("#actual_price_"+product_id).html();
        var price =  qty * cost; 
        
        $(".item_subtotal_"+product_id).html(price);

    })
})