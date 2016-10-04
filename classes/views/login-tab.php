<div id='aione_page_target'>

		<div class='wrap welcome-panel'>
		
			<h1>Login Page Setting</h1>
			
			<form action='' method='post'>
			<table class='form-table'>
				<tbody>
					<tr>
						<th scope='row'><label for='aione_login_page'>Login Page</label></th>
						<td><?php wp_dropdown_pages(array('name' => 'aione_login_page','show_option_none' => 'Select Login Page','id' => 'aione_login_page','selected'=> get_option('aione_login_page'))); ?></td>
					</tr>
					
					
					<tr>
						<th scope='row'><label for='admin_login_redirect_page'>Login Redirect Page</label></th>
						<td><?php wp_dropdown_pages(array('name' => 'admin_login_redirect_page','show_option_none' => 'Select Login Redirect Page','id' => 'admin_login_redirect_page','selected'=> get_option('admin_login_redirect_page'))); ?></td>
					</tr>
					
				</tbody>
			</table>
			
			<p class='submit'>
				<?php wp_nonce_field( 'validation_key', 'save_settings' ); ?>
				<input type='hidden' name='action' value='save' />
				<input type='hidden' name='filter' value='login-tab' />
				<input type='submit' name='submit' id='submit' class='button button-primary' value='Save Settings'>
			</p>
			</form>
		</div>	
		
	</div>