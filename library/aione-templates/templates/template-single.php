<?php
/**
 * Template Name: Aione Single Post
 */
 ?>

<?php get_header(); ?>

	<div id="primary">
		<div id="custom-template" role="main">
			
			<?php
			
				
				$option_name = "aione_app_builder_template_setting";
				if ( get_option( $option_name ) !== false ) {
					$tem_settings = get_option( $option_name ); 
					$tem_settings = unserialize($tem_settings);
					foreach($tem_settings as $key => $value){
						
						if ($post->post_type == $key){
							$template_id = $value['template_single'];
							$template_post = get_post($template_id );
						}
					}
				}
				//echo "<pre>";print_r($templates);echo "</pre>";
				$data = do_shortcode( $template_post->post_content );
				$css = get_post_meta($template_post->ID, 'pyre_custom_css',true);
				$data .= "<style>";
				$data .= $css;
				$data .= "</style>";
				echo $data;
			?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_footer(); ?>