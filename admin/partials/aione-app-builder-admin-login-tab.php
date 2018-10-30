<div id='aione_app_builder_page_target'>

	<div class='wrap welcome-panel'>
		
		<h1>Login Page Setting</h1>

		<form action='' method='post'>
			<table class='form-table'>
				<tbody>
					<tr>
						<th scope='row'><label for='aione_app_builder_login_page'>Login Page</label></th>
						<td><?php wp_dropdown_pages(array('name' => 'aione_app_builder_login_page','show_option_none' => 'Select Login Page','id' => 'aione_app_builder_login_page','selected'=> get_option('aione_app_builder_login_page'))); ?>
						<p class="description">Select the page you wish to make WordPress default Login page. <br><strong>Note:</strong> This should be the page that contains the <strong>login form  shortcode</strong></p>
					</td>
				</tr>

				<tr>
					<th scope='row'><label for='admin_login_redirect_page'>Login Redirect Page</label></th>
					<td><?php wp_dropdown_pages(array('name' => 'admin_login_redirect_page','show_option_none' => 'Select Login Redirect Page','id' => 'admin_login_redirect_page','selected'=> get_option('admin_login_redirect_page'))); ?>
					<p class="description">Select the page users will be redirected to after login.</p>
				</td>
			</tr>

			<tr>
				<th scope='row'><label for='enable_two_factor_auth'>Enable Two Factor Authentication</label></th>
				<td>
					Yes: <input type="radio" name="enable_two_factor_auth" value="yes" <?php if(get_option('enable_two_factor_auth') == 'yes') {
						echo "checked";
					} ?>>
				</td>
			</tr>

			<tr>
				<th scope='row'></th>
				<td>
					No: <input type="radio" name="enable_two_factor_auth" <?php if(get_option('enable_two_factor_auth') == 'no' || get_option('enable_two_factor_auth') == '') {
						echo "checked";
					} ?>  value="no">
				</td>
			</tr>

			<tr>
				<th scope='row'><label for='select_one_option'>Choose one option</label></th>
				<td>
					Email: <input type="radio" name="two_factor_auth" <?php if(get_option('two_factor_auth') == 'email') {
						echo "checked";
					} ?> value="email">
				</td>
			</tr>
			
			<tr>
				<th scope='row'><label for='select_one_option'></label></th>
				<td>
					Mobile: <input type="radio" name="two_factor_auth" <?php if(get_option('two_factor_auth') == 'mobile') {
						echo "checked";
					} ?> value="mobile">
				</td>
			</tr>
			
			<tr>
				<th scope='row'><label for='select_one_option'></label></th>
				<td>
					Both: <input type="radio" name="two_factor_auth" <?php if(get_option('two_factor_auth') == 'both') {
						echo "checked";
					} ?> value="both">
				</td>
			</tr>
			
			<tr>
				<th scope='row'><label for='select_one_option'></label></th>
				<td>
					User can select: <input type="radio" <?php if(get_option('two_factor_auth') == 'user_can_select') {
						echo "checked";
					} ?> name="two_factor_auth" value="user_can_select">
				</td>
			</tr>
		</tbody>
	</table>

	<p class='submit'>
		<?php wp_nonce_field( 'validation_key', 'save_set_page' ); ?>
		<input type='hidden' name='action' value='save' />
		<input type='hidden' name='filter' value='login-tab' />
		<input type='submit' name='submit' id='submit' class='button button-primary' value='Save Settings'>
	</p>
</form>
</div>	

</div>