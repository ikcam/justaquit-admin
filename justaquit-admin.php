<?php
/*
Plugin Name: JustAquit Admin
Plugin URI: http://justaquit.net
Description: Controls JustAquit Clients
Version: v0.0.1
Author: Irving Kcam
Author URI: http://ikcam.com
License: GPL2
*/
?>

<?php 
class justaquit {
	// Register Settings
	function settings_register(){
	}

	// Just for show options available
	function page_main(){
?>
	<div class="wrap">
		<h2>JustAquit Admin Options</h2>
		<ul>
			<li><a href="<?php bloginfo('url') ?>/wp-admin/admin.php?page=aquit_addclient">Add New Client</a></li>
			<li><a href="#">View Current Clients</a></li>
		</ul>
	</div>
<?php
	}

	// Add a new client
	function page_addclient(){
		$continue = $_POST['submit'];
		if( $continue ){
			$domain = $_POST['domain'];
		}
?>
	<div class="wrap">
		<h2>Add New Client</h2>
		<form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
			<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<label for="domain">Domain</label>
					</th>
					<td>
						<input type="text" name="domain" />
						<span class="description">User domain or subdomain. Example: CLIENT.justaquit.net</span>
					</td>
				</tr>
			</tbody>
			</table>
			<p class="submit"><input type="submit" name="submit" class="button-primary" value="Add New Client" /></p>
		</form>
	</div>
<?php		
	}
}

function justaquit_init(){
	add_menu_page( 'JustAquit', 'JustAquit', 'administrator', 'aquit', array('justaquit', 'page_main'), '', 59 );
	add_submenu_page( 'aquit', 'Add Client', 'Add Client', 'administrator', 'aquit_addclient', array( 'justaquit', 'page_addclient' ) );
	add_action('admin_init', array('justaquit', 'settings_register'));
}
add_action('admin_menu', 'justaquit_init');
?>