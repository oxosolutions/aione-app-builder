<?php
/**
 * Template Name: Aione Compare Post
 */
?>
<?php get_header(); ?>
<div id="primary">
		<div id="custom-template" role="main">
			
			<?php
			
			if(isset($_SESSION['compare_ids']) && !empty($_SESSION['compare_ids'])) {
				$array = $_SESSION['compare_ids'];
				//echo "<pre>";print_r($array);echo "</pre>";
				foreach($array as $v){
					$displayItems = get_field_objects($v);
				}
				//$displayItems = get_field_objects($array);
				$specifications = array();
				foreach( $displayItems as $field ) {
					$specifications[$field['label']] = $field['name'];
				}
				echo "<table>";
				echo "<th>Specification</th>";
				foreach($array as $post_id) {
					$post = get_post($post_id);
					echo "<th>".$post->post_title."</th>";	 
				}
				foreach($specifications as $specifications_key => $specifications_value) {
					echo "<tr>";
					echo "<td>".$specifications_key."</td>";
					foreach($array as $post_id){
						$value = get_field($specifications_value, $post_id);
						echo "<td>".$value."</td>";
					}
					echo "</tr>";	
				}
				echo "</table>";
				//echo "<pre>";print_r($displayItems);echo "</pre>";
				
				echo "<style>
					th,tr,td {border:1px solid;}
				</style>";
			} else {
				echo "No Phone is added to compare list.";
			}
			
			?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_footer(); ?>