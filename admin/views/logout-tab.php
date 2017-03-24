<div id='aione_app_builder_page_target'>

		<div class='wrap welcome-panel'>
		
			<h1>Logout Page Settings</h1>
			
			<form action='' method='post'>
			<table class='form-table'>
				<tbody>
					
					<tr>
						<th scope='row'><label for='logout_redirect_page'>Logout Redirect Page</label></th>
						<td><?php wp_dropdown_pages(array('name' => 'logout_redirect_page','show_option_none' => 'Select Logout Redirect Page','id' => 'logout_redirect_page','selected'=> get_option('logout_redirect_page'))); ?></td>
					</tr>
				</tbody>
			</table>
			
			<p class='submit'>
				<?php wp_nonce_field( 'validation_key', 'save_set_page' ); ?>
				<input type='hidden' name='action' value='save' />
				<input type='hidden' name='filter' value='logout-tab' />
				<input type='submit' name='submit' id='submit' class='button button-primary ' value='Save Settings'>
			</p>
			</form>
		</div>	
		
	</div>
