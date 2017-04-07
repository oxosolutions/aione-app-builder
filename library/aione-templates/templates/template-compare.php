<?php
/**
 * Template Name: Aione Compare Post
 */
?>
<?php get_header(); ?>
<div id="primary">
		<div id="custom-template" role="main">
			
			<?php
			
			if(isset($_SESSION['compare_ids'])) {
				echo "copare phone list here";
			} else {
				echo "add phone to compare list";
			}
			
			?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_footer(); ?>