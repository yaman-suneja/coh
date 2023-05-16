 <?php

/**

 * The template for displaying the footer.

 *

 * Contains the closing of the #content div and all content after.

 *

 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials

 *

 * @package Astra

 * @since 1.0.0

 */



if ( ! defined( 'ABSPATH' ) ) {

	exit; // Exit if accessed directly.

}

$obj = get_queried_object();
$cat_slug = $obj->slug;
if( $cat_slug == 'cbd-bundles' ){
	echo '<style>
	.yith-wcan-filters{ display: none; }
	.woocommerce-result-count{ display: none; }
	form.woocommerce-ordering{ display: none; }
	</style>';
}


?>

	<script>

	var acc = document.getElementsByClassName("accordion");

    var i;

    

    for (i = 0; i < acc.length; i++) {

      acc[i].addEventListener("click", function() {

        this.classList.toggle("active");

        var panel = this.nextElementSibling;

        if (panel.style.display === "block") {

          panel.style.display = "none";

        } else {

          panel.style.display = "block";

        }

      });

    }

</script>

<script>

	jQuery(document).ready(function(){

	    setInterval(function(){

	        jQuery("html").text(function () {

                return $(this).text().replace("The username you entered is incorrect", "The username you entered is already taken"); 

            });

	    }, 1000)
	})

    

	</script>

	



<?php if(!is_user_logged_in()){?>

    <script>

        jQuery( ".yith-wcan-filter .filter-items li" ).each(function() {

          var label = jQuery(this).text();

          jQuery(this).addClass(label);

        });

        jQuery('.Backbar, .Pro.Trial.Kits, .Wholesale.12-Packs').hide()

    </script>

<?php } ?>

	

<?php astra_content_bottom(); ?>

	</div> <!-- ast-container -->

	</div><!-- #content -->

<?php 

	astra_content_after();

		

	astra_footer_before();

		

	astra_footer();

		

	astra_footer_after(); 

?>

	</div><!-- #page -->



<?php 

	astra_body_bottom();    

	wp_footer(); 

?>

	</body>

</html>

