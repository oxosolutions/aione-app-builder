<div id='aione_app_builder_page_target'>

	<div class='wrap welcome-panel'>
		
		<h1>Login Page Setting</h1>

		<form action='' method='post'>
			<table class='form-table'>
				<tbody>

					<tr>
						<th scope='row'><label for='aione_app_builder_login_page_captcha'>Show Captcha on Login Page</label></th>
						<td>
							<fieldset>
								<label><input type="radio" name="enable_login_page_captcha" value="yes" <?php if(get_option('enable_login_page_captcha') == 'yes') {
										echo "checked";
									} ?> > <span class="format-i18n">Yes</span></label>
								<br/>	
								<label><input type="radio" name="enable_login_page_captcha" value="no" <?php if(get_option('enable_login_page_captcha') == 'no') {
										echo "checked";
									} ?> > <span class="format-i18n">No</span></label>	
							</fieldset>
						</td>
					</tr>

					
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
						<fieldset>
							<label><input type="radio" name="enable_two_factor_auth" value="yes" <?php if(get_option('enable_two_factor_auth') == 'yes') {
									echo "checked";
								} ?> > <span class="format-i18n">Yes</span></label>
							<br/>	
							<label><input type="radio" name="enable_two_factor_auth" value="no" <?php if(get_option('enable_two_factor_auth') == 'no') {
									echo "checked";
								} ?> > <span class="format-i18n">No</span></label>	
						</fieldset>
					</td>
				</tr>

				<tr>
					<th scope='row'><label for='aione_app_builder_login_page_tfa_role'>Select Role to apply TFA</label></th>
					<td><?php 
					//wp_dropdown_roles(); 
					global $wp_roles;
	    			$roles = $wp_roles->get_names();
	    			$login_page_tfa_role = get_option('login_page_tfa_role',array());
	    			?>
	    			<div class="wp-tab-panel"> <ul>
	    			<?php	
	    			foreach ($roles as $key => $value) {
	    				if(in_array($key, $login_page_tfa_role)){
	    					$checked = "checked";
	    				} else{
	    					$checked = "";
	    				}
	    			?>
	    				<li><label>
	    					<input type="checkbox" name="login_page_tfa_role[]" value="<?php echo $key;?>" <?php echo $checked;?>>
	    					<?php echo $value;?>
	    				</label></li>
	    			<?php		        
				    }
					?>
					</ul></div>
							<p class="description">Select the role you wish to apply Two Factor Auth. <br></p>
						</td>
				</tr>

				<tr>
					<th scope='row'><label for='two_factor_auth'>Choose one option</label></th>
					<td>
						<fieldset>
							<label><input type="radio" name="two_factor_auth" value="email" <?php if(get_option('two_factor_auth') == 'email') {
									echo "checked";
								} ?> > <span class="format-i18n">Email</span></label>
							<br/>	
							<label><input type="radio" name="two_factor_auth" value="mobile" <?php if(get_option('two_factor_auth') == 'mobile') {
									echo "checked";
								} ?> > <span class="format-i18n">Mobile</span></label>
								<br/>
							<label><input type="radio" name="two_factor_auth" value="both" <?php if(get_option('two_factor_auth') == 'both') {
									echo "checked";
								} ?> > <span class="format-i18n">Both</span></label>	
							<br/>
							<label><input type="radio" name="two_factor_auth" value="user_can_select" <?php if(get_option('two_factor_auth') == 'user_can_select') {
									echo "checked";
								} ?> > <span class="format-i18n">User Can Select</span></label>	
						</fieldset>
					</td>
				</tr>

				<tr>
					<th scope='row'><label for='login_page_otp_mobile_field'>Mobile Number Meta Key</label></th>
					<td>
						<input class="regular-text ltr" type="text" name="login_page_otp_mobile_field" value="<?php echo get_option('login_page_otp_mobile_field');?>">
					</td>
				</tr>

				<tr>
					<th scope='row'><label for='Select SMS Account'>Select SMS Account: </label></th>
					<td>
						<select name="sms_service_provider">
							<option value="">Select SMS Service provider</option>
							<option value="twillio"<?php if(get_option('sms_service_provider') == 'twillio'){ echo "selected";
							} ?> >Twillio</option>
							<option value="msgclub"<?php if(get_option('sms_service_provider') == 'msgclub'){ echo "selected";
							} ?> >MsgClub</option>
						</select>
						<p class="description">Select SMS Account from where user wants to send SMS.</p>
					</td>
				</tr>

				<tr>
					<th scope='row'>Twillio Account SID:</th>
					<td>
						<input class="regular-text ltr" type="text" name="twillio_sms_service_provider_key"
							value="<?php echo get_option('twillio_sms_service_provider_key'); ?> "
						>
					</td>
				</tr>

				<tr>
					<th scope='row'>Twillio Auth Token : </th>
					<td>				
						<input class="regular-text ltr" type="text" name="twillio_sms_service_provider_secret" value="<?php echo get_option('twillio_sms_service_provider_secret'); ?> "> 
					</td>
				</tr>

				<tr>
					<th scope='row'>Twillio Sender Phone Number : </th>
					<td>											
						<input class="regular-text ltr" type="text" name="twillio_sms_service_provider_phone_number" value="<?php echo get_option('twillio_sms_service_provider_phone_number'); ?> "> 
					</td>
				</tr>

				<tr>
					<th scope='row'>MsgClub Auth Key : 	</th>
					<td>										
						<input class="regular-text ltr" type="text" name="msgclub_sms_service_provider_key" value="<?php echo get_option('msgclub_sms_service_provider_key'); ?> "> 
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