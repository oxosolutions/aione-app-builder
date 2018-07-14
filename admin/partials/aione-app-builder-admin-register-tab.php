<div id='aione_app_builder_page_target'>

		<div class='wrap welcome-panel'>
		
			<h1>Register Page Settings</h1>
			
			<form action='' method='post'>
			<table class='form-table'>
				<tbody>
					
					<tr>
						<th scope='row'><label for='aione_app_builder_register_page'>Register Page</label></th>
						<td><?php wp_dropdown_pages(array('name' => 'aione_app_builder_register_page','show_option_none' => 'Select Register Page','id' => 'aione_app_builder_register_page','selected'=> get_option('aione_app_builder_register_page'))); ?>
							<p class="description">Select the page you wish to make WordPress default Registration page. <br><strong>Note:</strong> This should be the page that contains the <strong>registration form  shortcode</strong></p>
						</td>
					</tr>
					
				</tbody>
			</table>
			<h2>Registration Fields Settings</h2>
			
			<table class='form-table'>
				<tbody>
					<tr>
						<th scope='row'><label for='aione_app_builder_login_page'>Registration Custom Fields Groups( ACF )</label></th>
						<td><?php wp_dropdown_pages(array('name' => 'aione_app_builder_registration_custom_field_groups','show_option_none' => 'Select Custom Fields Groups','id' => 'aione_app_builder_registration_custom_field_groups','post_type' => 'acf', 'selected'=> get_option('aione_app_builder_registration_custom_field_groups'))); ?>
							<p class="description">Select the Advanced Custom Field group you wish to display under registration form.</p>
						</td>
					</tr>
				</tbody>
			</table>
	
			<p class='submit'>
				<?php wp_nonce_field( 'validation_key', 'save_set_page' ); ?>
				<input type='hidden' name='action' value='save' />
				<input type='hidden' name='filter' value='register-tab' />
				<input type='submit' name='submit' id='submit' class='button button-primary ' value='Save Settings'>
			</p>
			</form>
		</div>	
		
	</div>
