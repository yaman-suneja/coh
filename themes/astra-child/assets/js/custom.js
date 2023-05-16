    $ = jQuery;
$(document).ready(function (e) {

    //$('.um-password #um-submit-btn').click(function () {

        $('.um-password #username_b').attr('placeholder', 'Enter your username');

        setInterval(function(){

            var email = $('.um-password #username_b').val();

            if (IsEmail(email) == false) {

                jQuery('.um-password #um-submit-btn').removeAttr('disabled');

                jQuery('.um-password .email-error-msg').remove();

                //console.log('not email');

            }else{

                //console.log('email');

                jQuery('.um-password #um-submit-btn').attr('disabled', 'disabled');

                jQuery('.um-password .email-error-msg').remove();

                jQuery('<p class="email-error-msg" style="color:red;">Please enter a Username instead of email ID</p>').insertAfter('.um-password #username_b');

            }

            jQuery('.sezzle-shopify-info-button').first().css('display', 'none');

        }, 500)

        jQuery('#reg_username, #reg_email, #username, #password, #b2bking_registration_roles_dropdown').attr('required', 'required');

        $("#b2bking_registration_roles_dropdown").prepend("<option value='' selected='selected'>Select Role</option>");
        
        var org_text = jQuery('ul.woocommerce-error li').first().text();
        
        if(org_text.includes('Error: ERROR: Your answer was incorrect - please try again.')){
            jQuery('ul.woocommerce-error li').first().text('Please fill the captcha')
        }

        jQuery('section.up-sells.upsells').addClass('related');

        jQuery('.search-panel-class').insertAfter('li.search-custom-icon');
        
        jQuery('li.search-custom-icon').click(function(){
            jQuery('.search-panel-class').slideToggle('slow'); 
        });

        jQuery('table.variations select option:eq(1)').prop('selected', true);
	
// 	        $('.main-navigation li.menu-item a.menu-link').on("click", function() {
// 				if($(this).text().toLowerCase() == 'loyalty and rewards'){
// 					document.location.reload(true);
// 				}
// 			});

    });



function IsEmail(email) {

    var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

    if (!regex.test(email)) {

        return false;

    }else {

        return true;

    }

}