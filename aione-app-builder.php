<?php
/*
Plugin Name: Aione App Builder
Plugin URI: http://oxosolutions.com/products/wordpress-plugins/aione-app-builder
Description: Aione App Builder
Version: 1.0.1.25
Author: OXO Solutions
Author URI: http://oxosolutions.com/
GitHub Plugin URI: https://github.com/oxosolutions/aione-app-builder
GitHub Branch: master
*/


if (file_exists(dirname( __FILE__ ) .'/classes/aione-app-builder-main.php')){
    require_once( dirname( __FILE__ ) .'/classes/aione-app-builder-main.php' );
}


    function plugin_options_page() {
		$tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->general_settings_key;
		?>
		<div class="wrap">
			<?php $this->plugin_options_tabs(); ?>
			<form method="post" action="options.php">
				<?php wp_nonce_field( 'update-options' ); ?>
				<?php settings_fields( $tab ); ?>
				<?php do_settings_sections( $tab ); ?>	
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
/*	
if (file_exists(dirname( __FILE__ ) .'/classes/class-aione-app-builder-main-update.php')){
	require_once( dirname( __FILE__ ) .'/classes/class-aione-app-builder-main-update.php' );
}
*/