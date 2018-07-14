<div id='aione_app_builder_page_target'>

		<div class='wrap welcome-panel'>
		
			<h1>Lost Password Page Settings</h1>
			
			<form action='' method='post'>
			<table class='form-table'>
				<tbody>
					
					<tr>
						<th scope='row'><label for='aione_app_builder_forgot_password_page'>Forgot Password Page</label></th>
						<td><?php wp_dropdown_pages(array('name' => 'aione_app_builder_forgot_password_page','show_option_none' => 'Select Forgot Password Page','id' => 'aione_app_builder_forgot_password_page','selected'=> get_option('aione_app_builder_forgot_password_page'))); ?>
							<p class="description">Select the page you wish to make WordPress default "Lost Password page". <br><strong>Note:</strong> This should be the page that contains the <strong>password reset  shortcode</strong></p>
						</td>
					</tr>
						
				</tbody>
			</table>
	
			<p class='submit'>
				<?php wp_nonce_field( 'validation_key', 'save_set_page' ); ?>
				<input type='hidden' name='action' value='save' />
				<input type='hidden' name='filter' value='lost-password-tab' />
				<input type='submit' name='submit' id='submit' class='button button-primary ' value='Save Settings'>
			</p>
			</form>
		</div>	
		
	</div>
