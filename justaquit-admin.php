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
?>
	<div class="wrap">
<?php
		$submit = $_POST['submit'];
		if( $submit ){
			$domain = $_POST['domain'];
			$domain = esc_attr($domain);

			// Create folder
			$query = 'mkdir /home/aqtclients/'.$domain;
			$result = shell_exec($query);

			// Change folder permissions
			$query = 'chown aqtclients:aqtclients /home/aqt/'.$domain;
			$result = shell_exec($query);
?>
		<h2>Result</h2>
		<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row">
					URL:
				</th>
				<td>
					<a href="<?php echo $domain ?>" target="_blank"><?php echo $domain ?></a>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					FTP User:
				</th>
				<td>
					aqtclients
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					FTP Password:
				</th>
				<td>
					******
				</td>
			</tr>
		</tbody>
		</table>
<?php
		}
?>			
		<h2>Add New Client</h2>
		<form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>?page=aquit_addclient">
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