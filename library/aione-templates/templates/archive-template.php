<?php
/**
 * Template Name: Aione Archive
 */
 ?>
<?php get_header(); ?>

	<div id="primary">
		<div id="custom-template" role="main">
			
			<?php
			
				$wp_post = $_SERVER['REQUEST_URI'];
				$permalink =  get_permalink();
				$home_url = home_url();
				$url = trim(str_replace($home_url,"",$permalink), "/");
				$post_type = strstr($url, '/', true); 
				
				$option_name = "aione_app_builder_template_setting";
				if ( get_option( $option_name ) !== false ) {
					$tem_settings = get_option( $option_name ); 
					$tem_settings = unserialize($tem_settings);
					foreach($tem_settings as $key => $value){
						
						if ($post_type == $key){
							$template_id = $value['template_archive'];
							$template_post = get_post($template_id );
						}
					}
				}
				 $args = array(
					'posts_per_page'   => -1,
					'post_type'        => $post_type,
					'post_status'      => 'publish'
				);
				$posts_array = get_posts( $args );
				if($posts_array){
					foreach($posts_array as $post_object){
						$data = do_shortcode( $template_post->post_content );
						$css = get_post_meta($template_post->ID, 'pyre_custom_css',true);
						$data .= "<style>";
						$data .= $css;
						$data .= "</style>";
						//echo $data;
						//echo "<hr>";
					}
				} 
				$loop = new WP_Query( $args );
				if( $loop->have_posts() ):
							
					while( $loop->have_posts() ): $loop->the_post(); global $post;
					$data = do_shortcode( $template_post->post_content );
						$css = get_post_meta($template_post->ID, 'pyre_custom_css',true);
						$data .= "<style>";
						$data .= $css;
						$data .= "</style>";
						echo $data;
						echo "<hr>";
					endwhile;
					
				endif;
				
			?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_footer(); ?>