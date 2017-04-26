# Toolset Common

A collection of php, javascript and CSS libraries, utilities and models to be used with Toolsets plugins.

## Install:

Fetch the toolset-common library using composer:

		composer install --no-autoloader

		
## How To Use Toolset Admin Notices

#### Simple Admin Notice
We call Simple Admin Notice the temporary success / warning / error messages in WordPress. Like "Post saved.", 
"Field XY is required" and so on... all these messages are temporary.

##### Success Notice

	Toolset_Admin_Notices_Manager::add_notice( 'types-post-saved', 'Your post was successfully saved!' );
	
The first parameter is the id of the notice, and the second the content of the notice. It is a short version of:

	$n = new Toolset_Admin_Notice_Success( 'types-post-saved' );
	$n->set_content( 'Your post was successfully saved!' );
	Toolset_Admin_Notices_Manager::add_notice( $n );
	
You see we first initialise the success notice with the id `types-post-saved`. Than adding our content to the notice 
and finally apply it to our `Toolset_Admin_Notices_Manager`.

Every notice has an id, this is used for making sure not displaying twice the same and for dismissable notices.

##### Warning / Error notice
You can use the previous code also for adding Error and Warning messages.

	$n = new Toolset_Admin_Notice_Error( 'error-on-save' );
	...
	$n = new Toolset_Admin_Notice_Warning( 'warning-message' );
	...


#### Toolset Admin Notice
If you want to show a more advanced or permanent message, like running our installer, doing a database update or 
something like that, you should create a toolset styled admin notice. Here an example of use:
```
	$notice = new Toolset_Admin_Notice_Required_Action( 'toolset-run-installer' );
	Toolset_Admin_Notices_Manager::add_notice( $notice );
	$notice->set_content( __DIR__ .'/templates/admin/notice/content/run-installer.phtml' );
 
 
	// ON THE LAST STEP OF THE INSTALLER:
	if( $installation_done ) {
		Toolset_Admin_Notices_Manager::dismiss_notice_by_id( 'toolset-run-installer', true );
	}
```

This message uses a template for the content:
```
	<h3>
		<?php _e( 'Do you want to prepare this site for quick editing with Toolset?', 'wpcf' ); ?>
	</h3>
    
	<p class="toolset-list-title">
		<?php _e( 'We will:', 'wpcf' ); ?>
	</p>
	
	<ul class="toolset-list">
		<li>
			<?php printf( __( 'Automatically install the Toolset plugins that are needed for %s', 'wpcf' ), 'Toolset Starter' ); ?>
		</li>
		<li>
			<?php printf( __( 'Set up layouts, template, archives and other site elements for %s', 'wpcf'), 'Toolset Starter' ); ?>
		</li>
	</ul>
    
	<?php
	echo Toolset_Admin_Notices_Manager::tpl_button_primary(
        __( 'Run Installer', 'wpcf' ),
        admin_url( 'index.php?page=toolset-site-installer' )
    );
```

By default `Toolset_Admin_Notice_Required_Action` does not show a dismiss button. The message will be dismissed once the
the required action is done. In this example it would be on the last step of the installer by calling:
```
Toolset_Admin_Notices_Manager::dismiss_notice_by_id( 'toolset-run-installer', true );
```
*Note: the second parameter is for $globally. If it would be false, only the current user won't see the message anymore.
With true no one will see the message about "Run Installer" anymore.*

#### Make a notice permanent dismissible
To create a notice, which should be dismissible by the user you can add the following:
```
$notice->set_is_dismissible_permanent( true );
```
This would add the usual (x) to the top right of the message. The dismiss is saved as usermeta.

#### Make a notice permanent dissmissible per installation
Same as above but once a user clicks on the (x) nobody will see the notice anymore.
```
$notice->set_is_dismissible_globally( true );
```

#### Conditions

Now my installer message is in place. But even subscribers can see the message. I want to limit it to admins:
```
if( current_user_can( 'manage_options' ) ) {
	// my previous notice init code
}
```
ok, but better:
```
// my previous notice init code
$notice->add_condition( new Toolset_Condition_User_Role_Admin() );
```
Now only administrators will see the message.

You can add as many conditions as you want.
```
// my previous notice init code
$notice->add_condition( new Toolset_Condition_User_Role_Admin() );
$notice->add_condition( new Toolset_Condition_Plugin_Layouts_Active() );
```
#### Create a new Condition
Feel free to add new conditions. Your condition must implement the `Toolset_Condition_Interface`. 
```
interface Toolset_Condition_Interface {
	/**
	 * @return bool
	 */
	public function is_met();
}
```
You see very simple, as example the `Toolset_Condition_Plugin_Layouts_Active` condition:
```
class Toolset_Condition_Plugin_Layouts_Active implements Toolset_Condition_Interface {
 
	public function is_met() {
		if( defined( 'WPDDL_DEVELOPMENT' ) || defined( 'WPDDL_PRODUCTION' ) ) {
			return true;
		}

		return false;
	}
 
}
```
