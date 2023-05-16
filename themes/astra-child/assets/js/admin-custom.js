jQuery(document).ready(function(){
   setInterval(function(){
       jQuery('.um-admin-infobox a').attr('target', '_blank');
       jQuery('.um-admin-infobox a').click(function(){
           var link_to_open = jQuery(this).attr('href')
           window.open(link_to_open);
       });
   }, 1000);
   
});