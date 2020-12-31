jQuery(document).ready(function($){ 
    $('.aionepwa-colorpicker').wpColorPicker();	// Color picker
	$('.aionepwa-icon-upload').click(function(e) {	// Application Icon upload
		e.preventDefault();
		var aionepwa_meda_uploader = wp.media({
			title: 'Application Icon',
			button: {
				text: 'Select Icon'
			},
			multiple: false  // Set this to true to allow multiple files to be selected
		})
		.on('select', function() {
			var attachment = aionepwa_meda_uploader.state().get('selection').first().toJSON();
			$('.aionepwa-icon').val(attachment.url);
		})
		.open();
	});
	$('.aionepwa-splash-icon-upload').click(function(e) {	// Splash Screen Icon upload
		e.preventDefault();
		var aionepwa_meda_uploader = wp.media({
			title: 'Splash Screen Icon',
			button: {
				text: 'Select Icon'
			},
			multiple: false  // Set this to true to allow multiple files to be selected
		})
		.on('select', function() {
			var attachment = aionepwa_meda_uploader.state().get('selection').first().toJSON();
			$('.aionepwa-splash-icon').val(attachment.url);
		})
		.open();
	});
	$('.aionepwa-app-short-name').on('input', function(e) {	// Warn when app_short_name exceeds 12 characters.
		if ( $('.aionepwa-app-short-name').val().length > 15 ) {
			$('.aionepwa-app-short-name').css({'color': '#dc3232'});
			$('#aionepwa-app-short-name-limit').css({'color': '#dc3232'});
		} else {
			$('.aionepwa-app-short-name').css({'color': 'inherit'});
			$('#aionepwa-app-short-name-limit').css({'color': 'inherit'});
		}
	});
});