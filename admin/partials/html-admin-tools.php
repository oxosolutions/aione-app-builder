<?php 

/**
*  html-admin-tools
*
*  View to output admin tools for both archive and single
*
*  @param	string $screen_id The screen ID used to display metaboxes
*  @param	string $active The active Tool
*  @return	n/a
*/

$class = $active ? 'single' : 'grid';
echo "========================class";
?>
<div class="wrap" id="aione-admin-tools">
	
	<h1><?php _e('Tools', 'aione-app-builder'); ?> </h1>
	
	<div class="aione-meta-box-wrap -<?php echo $class; ?>">
		<?php do_meta_boxes( $screen_id, 'normal', '' ); ?>	
	</div>
	
</div>