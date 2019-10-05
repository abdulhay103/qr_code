; (function(jQuery){
	jQuery(document).ready(function(){
		 var current_value = jQuery("#qrcv_toggle").val();
		jQuery('#toggle1').minitoggle({
			on:current_value==1 ? true : false
		});

		if (current_value==1) {
			jQuery('#toggle1 .toggle-handle') .attr('style', 'transform: translate3d(42px,0px,0px)');
		}

		jQuery('#toggle1').on("toggle", function(e){
            if (e.isActive)
                jQuery("#qrcv_toggle").val(1);
            else
                jQuery("#qrcv_toggle").val(0);
        });
	});
})(jQuery); 