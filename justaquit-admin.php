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
<?php class justaquit {
	// Install
	function install(){
		global $wpdb;
		global $justaquit_db_version;
		$justaquit_db_version = "1.0.2";
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
				domain_wp tinyint(1) NOT NULL,
				domain_registered datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				domain_expire datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				UNIQUE KEY ID (ID)
			);";
			$table_name = $wpdb->prefix."databases";
			$sql .= "CREATE TABLE $table_name(
				ID mediumint(9) NOT NULL AUTO_INCREMENT,
				db_name varchar(30) NOT NULL,
				db_user varchar(16) NOT NULL,
				db_password varchar(55) NOT NULL,
				client_id mediumint(9) NOT NULL,
				domain_id mediumint(9) NOT NULL,
				UNIQUE KEY ID (ID)
			)";
			$table_name = $wpdb->prefix."clientdomain";
			$sql .= "CREATE TABLE $table_name (
				ID mediumint(9) NOT NULL AUTO_INCREMENT,
				client_id mediumint(9) NOT NULL,
				domain_id mediumint(9) NOT NULL,
				UNIQUE KEY ID (ID)
			);";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);

			update_option( "justaquit_db_version", $justaquit_db_version );
		}
	}

	// Register Settings
	function settings_register(){
		register_setting( 'justaquit', 'justaquit_settings', array( 'justaquit', 'verify_settings') );
	}

	// Callback functions
	function verify_settings($input){
		if( $input['linodeAPI'] == NULL )
			$input['linodeAPI'] = '';

		if( $input['linodeDomain'] == NULL )
			$input['linodeDomain'] = '';

		if( $input['mainFolder'] == NULL )
			$input['mainFolder'] = '/home/';

		if( $input['mainUser'] == NULL )
			$input['mainUser'] = 'root';

		if( $input['virtualHost'] == NULL )
			$input['virtualHost'] = '/etc/apache2/sites-enabled/';

		return $input;
	}

	// Just for show options available
	function page_main(){
?>
	<div class="wrap">
		<h2>JustAquit Admin Options</h2>
		<ul>
			<li><a href="<?php bloginfo('url') ?>/wp-admin/admin.php?page=aquit_addclient">Add New Client</a></li>
			<li><a href="<?php bloginfo('url') ?>/wp-admin/admin.php?page=aquit_adddomain">Add New Domain</a></li>
			<li><a href="<?php bloginfo('url') ?>/wp-admin/admin.php?page=aquit_settings">Settings</a></li>
		</ul>
	</div>
<?php
	}

	// Add a new client
	function page_addclient(){
		$submit = $_POST['submit'];
		if( $submit ){
			$client_author = $_POST['client_author'];
			$client_name = $_POST['client_name'];
			$client_email = $_POST['client_email'];
			$client_address = $_POST['client_address'];
			$client_phone = $_POST['client_phone'];
			$client_registered = $_POST['client_registered'];

			global $wpdb;
			$table = $wpdb->prefix.'clients';
			$query = "SELECT * FROM $table WHERE client_email = %s";
			$procced = $wpdb->get_var( $wpdb->prepare( $query, $client_email ) );	
			if( $procced == 0 ){
				$query = "INSERT INTO $table ( client_author, client_name, client_email, client_address, client_phone, client_registered ) VALUES ( %d, %s, %s, %s, %s, %s )";
				$wpdb->query( $wpdb->prepare( $query, array( $client_author, $client_name, $client_email, $client_address, $client_phone, $client_registered ) ) );
				
				echo '<div id="message" class="updated fade"><p>User created successfully.</p></div>';
			} else {
				echo '<div id="message" class="error"><p>User already exists. Try again.</p></div>';
			}
		}
?>
	<div class="wrap">
		<h2>Add New Client</h2>
		<form action="<?php echo $_SERVER['PHP_SELF'] ?>?page=aquit_addclient" method="post">
			<input type="hidden" name="client_author" value="<?php echo get_current_user_id() ?>" />
			<input type="hidden" name="client_registered" value="<?php echo current_time('mysql') ?>" />
			<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<label for="client_name">Client Name:</label>
					</th>
					<td>
						<input type="text" name="client_name" required />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="client_email">Client Email:</label>
					</th>
					<td>
						<input type="text" name="client_email" required />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="client_address">Client Address:</label>
					</th>
					<td>
						<input type="text" name="client_address" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="client_address">Client Phone:</label>
					</th>
					<td>
						<input type="text" name="client_phone" />
					</td>
				</tr>
			</tbody>
			</table>
			<p class="submit"><input type="submit" name="submit" class="button-primary" value="Add New User" /></p>
		</form>

		<h2>Current Clients</h2>
		<table class="wp-list-table widefat fixed clients" cellspacing="0">
			<thead>
				<tr>
					<th scope="col" id="id" class="manage-column column-id"><span>ID</span></th>
					<th scope="col" id="name" class="manage-column column-name"><span>Name</span></th>
					<th scope="col" id="email" class="manage-column column-email"><span>E-mail</span></th>
					<th scope="col" id="phone" class="manage-column column-phone"><span>Phone</span></th>
					<th scope="col" id="domains" class="manage-column column-domains"><span>Domains</span></th>
					<th scope="col" id="author" class="manage-author column-author"><span>Author</span></th>
				</tr>
			</thead>
			<tbody id="the-list" class="list:client">
<?php
	global $wpdb;
	$table = $wpdb->prefix.'clients';
	$query = "SELECT * FROM $table";
	$clients = $wpdb->get_results( $query );
	$i=0;
	foreach( $clients as $client ) : $i++;
?>
				<tr <?php if($i%2==0){echo 'class="alternate"';} ?>>
					<th scope="row" class="column-id"><?php echo $client->ID ?></th>
					<td class="name column-name"><strong><?php echo $client->client_name ?></strong></td>
					<td class="email column-email"><a href="<?php echo $client->client_email ?>"><?php echo $client->client_email ?></a></td>
					<td class="phone column-phone"><?php echo $client->client_phone ?></td>
					<td class="domains column-domains">
<?php
		global $wpdb;
		$table = $wpdb->prefix.'clientdomain';
		$query = "SELECT * FROM $table WHERE client_id = %s";
		$domains = $wpdb->get_var( $wpdb->prepare( $query, $client->ID ) );
		if( $domains )
			echo $domains;
		else
			echo '0';
?>
					</td>
					<td class="author column-author">
<?php
		$author = get_userdata( $client->client_author );
		echo $author->display_name;
?>
					</td>
				</tr>
<?php
	endforeach;
?>
			</tbody>
		</table>
	</div>
<?php
	}

	// Add a new domain
	function page_adddomain(){
		function scripts(){
			wp_enqueue_script( 'justaquit', plugin_dir_url(__FILE__) . '/javascript/main.jquery.js', array('jquery') );
		}
		add_action('wp_enqueue_scripts', 'scripts');
?>
	<div class="wrap">
<?php
		$submit = $_POST['submit'];
		if( $submit ){
			$client_id = $_POST['client_id'];
			$domain_author = $_POST['domain_author'];
			$domain_title = $_POST['title'];
			$domain_user = $_POST['domainuser'];
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
	$table = $wpdb->prefix.'clients';
	$query = "SELECT ID, client_name FROM $table";
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
						<label>Domain</label>
					</th>
					<td>
						<input type="text" name="domainName" id="domainName" required />
						<select name="domainID">
<?php
	$settings = get_option('justaquit_settings');
	$domainID = $settings['linodeDomain'];

	require('Services/Linode.php');
	try {
		$linode = new Services_Linode($settings['linodeAPI']);
		$domainName = $linode->domain_list(array('DomainID' => $domainID));
		$domainName = $domainName['DATA'];
		$domainName = $domainName[0]['DOMAIN'];
} catch (Services_Linode_Exception $e) {
echo $e->getMessage();
}
?>
							<option value="<?php echo $domainID ?>" selected><?php echo $domainName ?></option>
							<option value="9999">Custom Domain</option>
						</select>
						<span class="description">User domain or subdomain. Example: DOMAIN.justaquit.net</span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label>Domain Username:</label>
					</th>
					<td>
						<input type="text" name="domainUser" id="domainUser" required />
						<span class="description">Domain username. Example: client</span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label>Presentation Date:</label>
					</th>
					<td>
						<input type="number" max="31" min="0" name="domain_date_day" placeholder="Day" />&nbsp;-&nbsp;
						<input type="number" max="12" min="0" name="domain_time_month" placeholder="Month" />&nbsp;-&nbsp;
						<input type="number" min="2012" name="domain_time_year" value="<?php echo date('Y',current_time('timestamp')); ?>" placeholder="Year" />
					</td>
				</tr>
			</tbody>
			</table>
			<p class="submit"><input type="submit" name="submit" class="button-primary" value="Add New Domain" /></p>
		</form>

		<h2>Current Domains</h2>
		<table class="wp-list-table widefat fixed domains" cellspacing="0">
			<thead>
				<tr>
					<th scope="col" id="id" class="manage-column column-id"><span>ID</span></th>
					<th scope="col" id="domain" class="manage-column column-domain"><span>Name</span></th>
					<th scope="col" id="client" class="manage-column column-client"><span>Client</span></th>
					<th scope="col" id="title" class="manage-column column-title"><span>Title</span></th>
					<th scope="col" id="url" class="manage-column column-url"><span>URL</span></th>
				</tr>
			</thead>
			<tbody id="the-list" class="list:domain">
				<tr>
					<th scope="row" class="column-id"><?php echo $domain->ID ?></th>
					<td class="domain column-domain"><strong><?php echo $domain->user ?></strong></td>
					<td class="client column-client">
						<span>
<?php 
	global $wpdb;
	$table = $wpdb->prefix.'clients';
	$query = "SELECT client_name FROM $table WHERE ID = %s";
	$client = $wpdb->get_var( $wpdb->prepare( $query, $domain->client_id ) );
	echo $client;
?>
						</span></td>
					<td class="title column-title">968754010</td>
					<td class="url column-url">0</td>
				</tr>
			</tbody>
		</table>



	</div>
<?php		
	}

	function page_settings(){
?>
	<div class="wrap">
		<h2>JustAquit Admin Settings</h2>

		<form action="options.php" method="post">
<?php
	settings_fields('justaquit');
	$settings = get_option('justaquit_settings');
?>
			<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<label>Linode API Key:</label>
					</th>
					<td>
						<input type="text" name="justaquit_settings[linodeAPI]" value="<?php echo $settings['linodeAPI'] ?>" />
					</td>
				</tr>
<?php
	if( $settings['linodeAPI'] ) {
?>
				<tr valign="top">
					<th scope="row">
						<label>Master Domain:</label>
					</th>
					<td>
						<select name="justaquit_settings[linodeDomain]">
<?php
require('Services/Linode.php');


try {
	$linode = new Services_Linode('LbMPSPcoqfaTuqxySC5Fv92CjlQmY3nrlovxdo6C2xDwgBDmWmdoZtMeWMf2kIa2');
	$domains = $linode->domain_list();
	$domains = $domains['DATA'];
	foreach( $domains as $domain ){
		if( $settings['linodeDomain'] == $domain['DOMAINID'] )
			echo '<option selected value="'.$domain['DOMAINID'].'">'.$domain['DOMAIN'].'</option>';
		else
			echo '<option value="'.$domain['DOMAINID'].'">'.$domain['DOMAIN'].'</option>';
	}
} catch (Services_Linode_Exception $e) {
    echo $e->getMessage();
}
?>
						</select> 
					</td>
				</tr>
<?php
	}
?>
				<tr vlalign="top">
					<th scope="row">
						<label>Main Folder:</label>
					</th>
					<td>
						<input type="text" name="justaquit_settings[mainFolder]" value="<?php echo $settings['mainFolder'] ?>" />
						<span class="description">Base folder location for the domains.Tailing slash required.</span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label>Main User:</label>
					</th>
					<td>
						<input type="text" name="justaquit_settings[mainUser]" value="<?php echo $settings['mainUser'] ?>" />
						<span class="description">Final linux user owner fro the new domain.</span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label>Virtual Host Folder:</label>
					</th>
					<td>
						<input type="text" name="justaquit_settings[virtualHost]" value="<?php echo $settings['virtualHost'] ?>" />
						<span class="description">Tailing slash require.</span>
					</td>
				</tr>
			</tbody>
			</table>
			<p class="submit"><input type="submit" value="<?php _e('Save Changes') ?>" class="button-primary" /></p>
		</form>
	</div>

<?php
	}
}

function justaquit_init(){
	add_menu_page( 'JustAquit', 'JustAquit', 'administrator', 'aquit', array('justaquit', 'page_main'), '', 59 );
	add_submenu_page( 'aquit', 'Add Client', 'Add Client', 'administrator', 'aquit_addclient', array( 'justaquit', 'page_addclient' ) );
	add_submenu_page( 'aquit', 'Add Domain', 'Add Domain', 'administrator', 'aquit_adddomain', array( 'justaquit', 'page_adddomain' ) );
	add_submenu_page( 'aquit', 'Settings', 'Settings', 'administrator', 'aquit_settings', array( 'justaquit', 'page_settings' ) );
	add_action('admin_init', array('justaquit', 'settings_register'));
}
add_action('admin_menu', 'justaquit_init');
register_activation_hook( __FILE__, array('justaquit', 'install') );
?>