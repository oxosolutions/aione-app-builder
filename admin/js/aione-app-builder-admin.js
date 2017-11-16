(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
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
	 $( document ).ready(function(){

	 	var labelPostType = $('#post-body-content .js-wpcf-slugize-source').val() != ''
	        ? $('#post-body-content .js-wpcf-slugize-source').val()
	        : $('#post-body-content .js-wpcf-slugize-source' ).data('anonymous-post-type');

	    $('.js-wpcf-singular').html( labelPostType );
	    $('#post-body-content').on('keyup input cut paste', '.js-wpcf-slugize-source', function() {
	        $('.js-wpcf-singular').html($(this).val());
	    });

	 	$( document ).on( 'click', '.js-wpcf-choose-icon', function() {
	        var $thiz = $(this);
	        // show a spinner or something via css
	        var dialog = $('<div style="display:none;height:450px;" class="wpcf-dashicons"><span class="spinner"></span>Please Wait, Loading…</div>').appendTo('body');
	        // open the dialog
	        dialog.dialog({
	            // add a close listener to prevent adding multiple divs to the document
	            close: function(event, ui) {
	                // remove div with all data and events
	                dialog.remove();
	            },
	            dialogClass: 'wpcf-choose-icon wpcf-ui-dialog',
	            modal: true,
	            minWidth: 800,
	            maxHeight: .9*$(window).height(),
	            title: 'Choose Icon',
	            position: { my: "center top+50", at: "center top", of: window },

	        });
	        // load remote content
	        dialog.load(
	            ajaxurl, 
	            {
	                action: 'aione_cpt_get_icons_list',
	                _wpnonce: $thiz.data('wpcf-nonce'),
	                slug: $thiz.data('wpcf-value'),
	                "wpcf-post-type": $thiz.data('wpcf-post-type'),
	            },
	            function (responseText, textStatus, XMLHttpRequest) {
	                $(dialog).on('keyup input cut paste', '.js-wpcf-search', function() {
	                    if ( '' == $(this).val() ) {
	                        $('li', dialog).show();
	                    } else {
	                        var re = new RegExp($(this).val(), "i");
	                        $('li', dialog).each(function(){
	                            if ( !$(this).data('wpcf-icon').match(re) ) {
	                                $(this).hide();
	                            } else {
	                                $(this).show();
	                            }
	                        });
	                    }
	                });
	                $(dialog).on('click', 'a', function() {
	                    var $icon = $(this).data('wpcf-icon');
	                    $('#aione-types-icon').val($icon);
	                    $thiz.data('wpcf-value', $icon);
	                    var classes = 'aione-cpt-menu-image dashicons-before dashicons-'+$icon;
	                    $('div.aione-cpt-menu-image').removeClass().addClass(classes);
	                    dialog.dialog( "close" );
	                    return false;
	                });
	            }
	            );
	        //prevent the browser to follow the link
	        return false;
	    });
	 });

})( jQuery );
