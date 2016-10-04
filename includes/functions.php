<?php
function aione_show_errors($errors = null){
	$output = '';
	$output .= '<div class="aione_errors"><ul>';
	foreach($errors as $error){
		$output .= '<li class="error"><strong>' . __('Error') . '</strong>: ' . $error . '</li>';
	}
	$output .= '</ul></div>';
	return $output;
}