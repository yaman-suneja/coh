jQuery(document).ready( function($) {

    $( "#signup_form_role" ).select2()
    $( "#signup_form_state" ).select2()
    $( "#signup_form_country" ).select2()

    $(document).on("change", "#signup_form_country", function() {

        let state = sign_up_form.states;
        let country_code = $(this).val();
        $( "#signup_form_state" ).closest("p.input-column").show();
        $( "#signup_form_state" ).html($('<option value="">Select State</option>'));
        if( "" == country_code ) {
            return;
        }
        
        if( "object" == typeof state[country_code] && 0 !== parseInt(state[country_code].length) ) {
            var options = [];
            options = [ ...options, $('<option value="">Select State</option>')];
            $.each( state[country_code], function(index, value) {
                options = [ ...options, $('<option value="'+index+'">'+value+'</option>')];
            })
            $( "#signup_form_state" ).html(options)
        }
        else {
            $( "#signup_form_state" ).attr("required", false);
            $( "#signup_form_state" ).closest("p.input-column").hide();
        }    
    });

})