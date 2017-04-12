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
				if(isset($_GET['aione_search_filter']) && $_GET['aione_search_filter'] == "aione_search_filter"){
					//$meta_array = array('relation' => 'OR',);
					$meta_array = array();
					
					$raw_array = $_GET;
					$args = array(
						'posts_per_page'   => 10,
						'post_type'        => $post_type,
						'post_status'      => 'publish',
						'paged' => ( get_query_var('paged') ? get_query_var('paged') : 1),
						
					);
					if($_GET['by_cat']){
						$args['tax_query'] = array(
												array(
													'taxonomy' => 'category',
													'field'    => 'slug',
													'terms'    => $_GET['by_cat'],
													
												),
											);
					}
					if($_GET['keyword']){
						$args['s'] = $_GET['keyword'];
					}
					unset($raw_array['aione_search_filter']);
					unset($raw_array['aione-search-filter-submit']);
					unset($raw_array['by_cat']);
					unset($raw_array['keyword']);
					foreach($raw_array as $raw_array_key => $raw_array_val){
						if(!empty($raw_array_val)){
							$dump_array = array(
								'key'     => $raw_array_key,
								'value' => $raw_array_val,
								'compare' => '=',
							);
							array_push($meta_array,$dump_array);
						}
						
					}
					//echo "<pre>";print_r($meta_array);
					$args['meta_query'] = $meta_array;
					/*$args = array(
						'posts_per_page'   => 10,
						'post_type'        => $post_type,
						'post_status'      => 'publish',
						'meta_query'   =>$meta_array,
						'paged' => ( get_query_var('paged') ? get_query_var('paged') : 1),
					);*/
					
				} else {
					$args = array(
						'posts_per_page'   => 10,
						'post_type'        => $post_type,
						'post_status'      => 'publish',
						//'category_name' => 'uncategorized',
						'paged' => ( get_query_var('paged') ? get_query_var('paged') : 1),
					);
				}
				
				
				$loop = new WP_Query( $args );
				//echo "<pre>";print_r($args);
				$css = get_post_meta($template_post->ID, 'pyre_custom_css',true);
				$sidebar = get_post_meta($template_post->ID, 'sbg_selected_sidebar_replacement',true);
				if(empty($sidebar)){
					if( $loop->have_posts() ):
							
						while( $loop->have_posts() ): $loop->the_post(); global $post;
						$data = do_shortcode( $template_post->post_content );
							//$css = get_post_meta($template_post->ID, 'pyre_custom_css',true);
							//$sidebar = get_post_meta($template_post->ID, 'sbg_selected_sidebar_replacement',true);
							$data .= "<style>";
							$data .= $css;
							$data .= "</style>";
							echo $data;
							echo "<hr>";
						endwhile;
						
						global $wp_query;

						$big = 999999999; // need an unlikely integer
						$translated = __( 'Page', 'mytextdomain' ); // Supply translatable string
						echo '<div class="pagenav">';
						echo paginate_links( array(
							'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
							'format' => '?paged=%#%',
							'current' => max( 1, get_query_var('paged') ),
							'total' => $wp_query->max_num_pages,
								'before_page_number' => '<span class="screen-reader-text">'.$translated.' </span>'
						) );
						echo '</div>';

						?>
						<?php
					endif;
				} else {
					echo "<div style='float:left;width:60%;'>";
					if( $loop->have_posts() ):
								
						while( $loop->have_posts() ): $loop->the_post(); global $post;
						$data = do_shortcode( $template_post->post_content );
							//$css = get_post_meta($template_post->ID, 'pyre_custom_css',true);
							//$sidebar = get_post_meta($template_post->ID, 'sbg_selected_sidebar_replacement',true);
							$data .= "<style>";
							$data .= $css;
							$data .= "</style>";
							echo $data;
							
							echo "<hr>";
						endwhile;
						
						global $wp_query;

						$big = 999999999; // need an unlikely integer
						$translated = __( 'Page', 'mytextdomain' ); // Supply translatable string
						echo '<div class="pagenav">';
						echo paginate_links( array(
							'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
							'format' => '?paged=%#%',
							'current' => max( 1, get_query_var('paged') ),
							'total' => $wp_query->max_num_pages,
								'before_page_number' => '<span class="screen-reader-text">'.$translated.' </span>'
						) );
						echo '</div>';

						?>
						<?php
					endif;
					echo "</div>";
					?>
					<div id="sidebar" <?php Aione()->layout->add_class( 'sidebar_1_class' ); ?> >
					<?php
					generated_dynamic_sidebar();
					//echo "<div style='float:right;width:40%;'>".generated_dynamic_sidebar()."</div>";
					echo "<div style='clear:both;'></div>";
				}
			?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_footer(); ?>