<div id='aione_page_target'>

		<div class='wrap welcome-panel'>
		
			<h1>Register Page Settings</h1>
			
			<form action='' method='post'>
			<table class='form-table'>
				<tbody>
					
					<tr>
						<th scope='row'><label for='aione_register_page'>Register Page</label></th>
						<td><?php wp_dropdown_pages(array('name' => 'aione_register_page','show_option_none' => 'Select Register Page','id' => 'aione_register_page','selected'=> get_option('aione_register_page'))); ?></td>
					</tr>
					
				</tbody>
			</table>
			<h2>Registration Fields Settings</h2>
			
			<table class='form-table'>
				<tbody>
					<tr>
						<th scope='row'><label for='aione_login_page'>Registration Custom Fields Groups( ACF )</label></th>
						<td><?php wp_dropdown_pages(array('name' => 'aione_registration_custom_field_groups','show_option_none' => 'Select Custom Fields Groups','id' => 'aione_registration_custom_field_groups','post_type' => 'acf', 'selected'=> get_option('aione_registration_custom_field_groups'))); ?></td>
					</tr>
				</tbody>
			</table>
	
			<p class='submit'>
				<?php wp_nonce_field( 'validation_key', 'save_settings' ); ?>
				<input type='hidden' name='action' value='save' />
				<input type='hidden' name='filter' value='register-tab' />
				<input type='submit' name='submit' id='submit' class='button button-primary ' value='Save Settings'>
			</p>
			</form>
		</div>	
		
	</div>
