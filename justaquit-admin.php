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
	// Install
	function install(){
		global $wpdb;
		global $justaquit_db_version;
		$justaquit_db_version = "0.92";
		$installed_ver = get_option( "justaquit_db_version" );
		if( $installed_ver != $justaquit_db_version ) {
			$table_name = $wpdb->prefix."clients";
			$sql = "CREATE TABLE $table_name (
				ID mediumint(9) NOT NULL AUTO_INCREMENT,
				client_author mediumint(9) DEFAULT 0 NOT NULL,
				client_name varchar(250) NOT NULL,
				client_email varchar(100) NOT NULL,
				client_address varchar(150) NOT NULL,
				client_phone varchar(20) NOT NULL,
				client_registered datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				UNIQUE KEY ID (ID)
			);";
			$table_name = $wpdb->prefix."domains";
			$sql .= "CREATE TABLE $table_name (
				ID mediumint(9) NOT NULL AUTO_INCREMENT,
				client_id mediumint(9) NOT NULL,
				database_id mediumint(9) NOT NULL,
				domain_author mediumint(9) DEFAULT 0 NOT NULL,
				domain_title text NOT NULL,
				domain_user varchar(50) NOT NULL,
				domain_url varchar(55) NOT NULL,
				domain_registered datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				domain_expire datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				UNIQUE KEY ID (ID)
			);";
			$table_name = $wpdb->prefix."clientdomain";
			$sql .= "CREATE TABLE $table_name (
				ID mediumint(9) NOT NULL AUTO_INCREMENT,
				client_id mediumint(9) NOT NULL,
				domain_id mediumint(9) NOT NULL,
				UNIQUE KEY ID (ID)
			);";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);

			add_option("justaquit_db_version", $justaquit_db_version);
		}
	}

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
			<li><a href="<?php bloginfo('url') ?>/wp-admin/admin.php?page=aquit_adddomain">Add New Domain</a></li>
		</ul>
	</div>
<?php
	}

	// Add a new client
	function page_addclient(){
?>
	<div class="wrap">
		<h2>Add New Client</h2>
	</div>
<?php
	}

	// Add a new domain
	function page_adddomain(){
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
			$query = 'chown aqtclients:aqtclients /home/aqtclients/'.$domain;
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
		<h2>Add New Domain</h2>
		<form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>?page=aquit_addclient">
			<input type="hidden" name="domain_author" value="<?php echo get_current_user_id() ?>" />
			<input type="hidden" name="domain_registered" value="<?php echo current_time('mysql') ?>" />
			<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						Client:
					</th>
					<td>
						<select name="client_id">
<?php
	global $wpdb;
	$query = "SELECT ID, client_name FROM $wpdb->clients ORDER BY ID";
	$clients = $wpdb->get_results($query);
	foreach( $clients as $client ){
?>
						<option value="<?php echo $client->ID ?>"><?php echo $client->client_name ?></option>
<?php
	}
?> 
						</select>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="domain">Domain</label>
					</th>
					<td>
						<input type="text" name="domain" required />
						<span class="description">User domain or subdomain. Example: DOMAIN.justaquit.net</span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="username">Domain Username:</label>
					</th>
					<td>
						<input type="text" name="username" required />
						<span class="description">Domain username. Example: client</span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label>Presentation Date:</label>
					</th>
					<td>
						<input type="number" max="31" min="0" name="domain_date_day" />&nbsp;-&nbsp;
						<input type="number" max="12" min="0" name="domain_time_month" />&nbsp;-&nbsp;
						<input type="number" min="2012" name="domain_time_year" />
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
	add_submenu_page( 'aquit', 'Add Domain', 'Add Domain', 'administrator', 'aquit_adddomain', array( 'justaquit', 'page_adddomain' ) );
	add_action('admin_init', array('justaquit', 'settings_register'));
}
add_action('admin_menu', 'justaquit_init');
register_activation_hook( __FILE__, array('justaquit', 'install') );
?>