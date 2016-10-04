<div id='aione_page_target'>

		<div class='wrap welcome-panel'>
		
			<h1>Access Control Setting</h1>
			
	<?php
	if(isset($_GET['role'])){
		$useroxo = $_GET['role'];
	} else {
		$useroxo = "administrator";
	}
	?>
	 <form action='' method='post'>
			<input type='submit' name='submit' id='submit' class='button button-primary top-button-options' value='Save Settings'>
             <table class='form-table'>
				<tbody>
	     		<tr>
					<td> <select name="usrole" id="usrole"> <?php wp_dropdown_roles( $useroxo ); ?> </select>
				 </tr> 			
				<tr>
				  <td>
	<?php
	/* *********************************************** */
	 global $wpdb, $current_site, $page;
    global $wp_meta_boxes;

    $all_available_widgets = get_option('oxo_all_active_dashboard_widgets', array());

    $available_widgets = array(
        'dashboard_browser_nag' => __('Browser Nag', 'ub'),
        'dashboard_right_now' => __('Right Now', 'ub'),
        'dashboard_recent_comments' => __('Recent Comments', 'ub'),
        'dashboard_incoming_links' => __('Incoming Links', 'ub'),
        'dashboard_plugins' => __('Plugins', 'ub'),
        'dashboard_quick_press' => __('QuickPress', 'ub'),
        'dashboard_recent_drafts' => __('Recent Drafts', 'ub'),
        'dashboard_primary' => __('Primary Feed', 'ub'),
        'dashboard_secondary' => __('Secondary Feed', 'ub')
    );

    if (count($all_available_widgets) >= 1) {
        $available_widgets = $all_available_widgets;
    }

    $aione_oxo_admin_options_o = get_option($useroxo.'_oxo_admin_options');
	$active = $aione_oxo_admin_options_o['offwidget'];
    ?>

    <div class="postbox">
        <h3 class="hndle" style='cursor:auto;'><span><?php _e('Remove WordPress Dashboard Widgets '); ?></span></h3>
        <div class="inside">
            <p class='description'><?php _e('Select which widgets you want to remove from dashboard of selected user.'); ?>
            <ul class='availablewidgets'>
                <?php
                foreach ($available_widgets as $key => $title) {
                    ?>
                    <li><div class='input-field'><input class='custom-radio' type='checkbox' id="<?php echo $key; ?>" name='active[]' value='<?php echo $key; ?>' <?php if (in_array($key, $active)) echo "checked='checked'"; ?> />
					<label class="switch" for="<?php echo $key; ?>"><span class="handle"></span></label></div><div class='name-field'>&nbsp;<?php echo oxo_remove_tags($title); ?></div><div class="clear"></div></li>
                    <?php
                }  
                ?>
            </ul>
        </div>
    </div>
<?php /* *********************************************** */ ?>
				</td>
				</tr>

                    <tr> 
					<td>
        <?php
        global $menu;
		global $submenu;
			$aione_oxo_admin_options_i = get_option($useroxo.'_oxo_admin_options');
			$oxomenui = $aione_oxo_admin_options_i['oxomenu'];
			$oxosubmenui = $aione_oxo_admin_options_i['oxosubmenu'];

			$i=1;
			$output = "";
			$output .= "<ul id='oxo_admin_options'>";
	
		foreach( $menu as $key => $top ) { 	
			$url = $top[2];
			if($top[0]==""){ continue; }
					$output .= "<li>";
					$output .= "<div class='input-field'>";
					$oldname = $top['0'];
				if(strpos($oldname, "<")>=3){
						$newname = substr($oldname, 0, strpos($oldname, "<"));
				} else { $newname = $oldname; }  
						$vals = str_replace(' ', '', $newname);
						$is_selected = is_selected_menu($oxomenui, $top['2']);
						$output .= "<input class='custom-radio' id='custom-radio-".$i."' type='checkbox' name='menu[".$vals."]' value='".$top['2']."' ";
				        if($is_selected){ $output .='checked="checked"'; }
						$output .= ' /><label class="switch" for="custom-radio-'.$i.'"><span class="handle"></span></label>';
						$output .= "</div><div class='name-field'>";
						$output .= " ".$newname;
						$output .= "</div>";
						$submenumain[] = $submenu[$url];
			
				foreach($submenumain as $keys=>$tops){
					if(!empty($tops)){
						$output .= "<a class='arrow-down'>V</a>";
						$output .= "<div style='clear:both;'></div><ul id='oxo_admin_options_sub'>";
						foreach($tops as $keyss=>$topss){
							$oldnames = $topss['0'];
									if(strpos($oldnames, "<")>=3){
										$newnames = substr($oldnames, 0, strpos($oldnames, "<"));
									} else {
											$newnames = $oldnames;
										}
											$valss = str_replace(' ', '', $newnames);
											$is_selected = is_selected_menu($oxosubmenui, $top['2'].", ".$topss['2']);
											$strrpt  = str_repeat("123ABCDEFGHabcde",10); $strsuff = str_shuffle($strrpt); $j = substr($strsuff,0,3);
											$output .= "<li>";
											$output .= "<div class='input-field'>";
											$output .= "<input class='custom-radio' id='custom-radio-s-".$j."' type='checkbox' name='submenu[".$valss."]' value='".$top['2'].", " .$topss['2']."'";
									        if($is_selected){ $output .= 'checked="checked"'; }
											$output .= ' /><label class="switch" for="custom-radio-s-'.$j.'"><span class="handle"></span></label>';
											$output .= "</div><div class='name-field'>";
											$output .= " ".$newnames;
											$output .= "</div><div style='clear:both;'></div></li>";
											$url = ""; unset($submenumain); $submenumain = array(); 
						} 
				      $output .=  "</ul>";	
		            }/* end if(!empty($tops)) */
		        }  
		   $i++;
		   		$output .= "<div style='clear:both;'></div>";
	       $output .=  "</li>"; 
        }
           $output .=  "</ul>";
	       echo $output;
	
	
function is_selected_menu($item_array, $current_item){
	$is_selected = false;
	if(!empty ($item_array)){
		foreach($item_array as $item_array_item){
			foreach($item_array_item as $item_array_item_value){
				if($item_array_item_value == $current_item){
					$is_selected = true;
					break;
				}
			}
		} 
	}
	return $is_selected;
}


function oxo_remove_tags($string) {
    $string = preg_replace('/<[^>]*>/', ' ', $string);
    $string = str_replace("\r", '', $string);
    $string = str_replace("\n", ' ', $string);
    $string = str_replace("\t", ' ', $string);
    $string = trim(preg_replace('/ {2,}/', ' ', $string));
    return $string;
}

?> 
              </td>
			    <tr>
				</tbody>
			</table>
			
			<p class='submit'>
				<?php wp_nonce_field( 'validation_key', 'save_settings' ); ?>
				<input type='hidden' name='action' value='save' />
				<input type='hidden' name='filter' value='access-control-tab' />
				<input type='submit' name='submit' id='submit' class='button button-primary' value='Save Settings'>
			</p>
			</form>
		</div>	
		
	</div>
	<script>
	;(function($){
	$("#usrole").change(function(){ 
		var vals = $("#usrole").val();
		var pathname = window.location.pathname;
        var url      = window.location.href;
		window.location.assign(url+"&role="+vals);
	});
		$("#oxo_admin_options > li").click(function(){ 
			$(this).addClass("active").siblings("li").removeClass("active");
		});

	})(jQuery);
	</script>