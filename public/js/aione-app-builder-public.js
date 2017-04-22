(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	 $( document ).ready(function() {
		 
		$("#loginform input[type=text]").attr("required", true); 
		$("#loginform input[type=password]").attr("required", true);
		 
		 
		 
		 
	    var compareLink = $('a.compare_link');
	    var removeLink = $('a.remove_link');

		$(".remove-button").hide();
		var sessionData = $("#hidden_session").text();
		if(sessionData.length>0){
			 var sessionJsondata = JSON.parse(sessionData);
			$.each(sessionJsondata, function( index, value ) {
			  $("#remove-post-"+value).show();
			  $("#compare-post-"+value).hide();
			});
		}			
	    compareLink.on('click', function(e) {
			e.preventDefault();
		   var postID = $(this).attr('id');
	       var details = {
	            'action': 'compareAction',
				'postID': postID
	        };

	        $.ajax({
	            //url: 'http://192.168.0.101/aione/wp-admin/admin-ajax.php',
	            url: ajaxurl,
	            type: 'POST',
	            data: details,  // data format
	            success:function(data) {
	                console.log(data);
					
	                var jsondata = JSON.parse(data);
					
					$("#compare-post-"+postID).hide();
					$("#remove-post-"+postID).show();
					if(jsondata.length>3){
						$(".compare_link").addClass("disabled");
					} else {
						$(".compare_link").removeClass("disabled");
					}
	            },
	            error: function(error) {
	                console.log(error);
	            }
	        });   
	    });
		
		removeLink.on('click', function(e) {
			e.preventDefault();
		   var postID = $(this).attr('id');
	       var details = {
	            'action': 'removeAction',
				'postID': postID
	        };

	        $.ajax({
	            //url: 'http://192.168.0.101/aione/wp-admin/admin-ajax.php',
	            url: ajaxurl,
	            type: 'POST',
	            data: details,  // data format
	            success:function(data) {
	                console.log(data);
					
					$("#compare-post-"+postID).show();
					$("#remove-post-"+postID).hide();
					 var jsondata = JSON.parse(data);
					 if(jsondata.length>3){
						$(".compare_link").addClass("disabled");
					} else {
						$(".compare_link").removeClass("disabled");
					}
	            },
	            error: function(error) {
	                console.log(error);
	            }
	        });   
	    });
		
	});

	 

})( jQuery );
