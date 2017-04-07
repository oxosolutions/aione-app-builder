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
	    var compareLink = $('a.compare_link');
	    var removeLink = $('a.remove_link');
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
	            },
	            error: function(error) {
	                console.log(error);
	            }
	        });   
	    });
		
	});

	 

})( jQuery );
