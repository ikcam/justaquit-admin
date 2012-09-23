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
public class JAdmin {
	// Function install
	public function install(){
		global $wpdb;

		$table = $wpdb->prefix.	"clients";
		$sql = "CREATE TABLE $table (
			ID mediumint(9) NOT NULL AUTO_INCREMENT,
			first_name varchar(100) NOT NULL,
			last_name varchar(100) NOT NULL,
			email varchar(100) NOT NULL,
			address varchar(150),
			phone varchar(20),
			registration_date mediumint(9) NOT NULL,
			author mediumint(9) DEFAULT 0 NOT NULL,
			editor mediumint(9) DEFAULT 0 NOT NULL,
			UNIQUE KEY ID (ID)
		);";

		$table = $wpdb->prefix."domains";
		$sql .= "CREATE TABLE $table (
			ID mediumint(9) NOT NULL AUTO_INCREMENT,
			title text NOT NULL,
			url varchar(55) NOT NULL,
			priority mediumint(9) DEFAULT 2 NOT NULL,
			client_id mediumint(9) NOT NULL,
			author mediumint(9) NOT NULL,
			linode_did mediumint(9) NOT NULL,
			linode_rid mediumint(9) NOT NULL,
			wordpress tinyint(1) NOT NULL,
			creation_date mediumint(9) NOT NULL,
			UNIQUE KEY ID (ID)
		);";

		$table = $wpdb->prefix."databases";
		$sql .= "CREATE TABLE $table(
			name varchar(30) NOT NULL,
			user varchar(16) NOT NULL,
			password varchar(55) NOT NULL,
			domain_id mediumint(9) NOT NULL,
			UNIQUE KEY ID (ID)
		);";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

	// Register settings
	public function settings_register(){
		register_setting( 'jadmin', 'jadmin_settings', $this->settings_callback() );
	}

	// Callback functions
	private function settings_callback( $input ){
		if( $input['linode_key'] == NULL )
			$input['linode_key'] = '';

		if( $input['linode_did'] == NULL )
			$input['linode_did'] = '';

		if( $input['server_ip'] == NULL )
			$input['server_ip'] = '0.0.0.0';

		if( $input['server_folder'] == NULL )
			$input['server_folder'] = '/home/';

		if( $input['server_user'] == NULL )
			$input['server_user'] = 'root';

		if( $input['server_apache'] == NULL )
			$input['server_apache'] = '/etc/apache2/sites-enabled/';

		if( $input['database_prefix'] == NULL )
			$input['database_prefix'] = 'client_';

		return $input;
	}

	// Just for show options available
	function page_main(){
		function style(){
?>
<style type="text/css">
	ul.option_list > li{
		border:1px solid #CCC;
		border-radius:5px;
		font-size:1.5em;
		display:inline-block;
		margin-right:1em;
		padding:3em 5em;
	}
	ul.option_list > li > a{
		text-decoration:none;
	}
</style>
<?php	
		}
		add_action('admin_footer', 'style');
?>
	<div class="wrap">
		<h2>JustAquit Admin Options</h2>
		<ul class="option_list">
			<li><a href="<?php bloginfo('url') ?>/wp-admin/admin.php?page=aquit_addclient">Manage Clients</a></li>
			<li><a href="<?php bloginfo('url') ?>/wp-admin/admin.php?page=aquit_adddomain">Manage Domains</a></li>
			<li><a href="<?php bloginfo('url') ?>/wp-admin/admin.php?page=aquit_databases">List Databases</a></li>
			<li><a href="<?php bloginfo('url') ?>/wp-admin/admin.php?page=aquit_migrate">Migrate Domain</a></li>
			<li><a href="<?php bloginfo('url') ?>/wp-admin/admin.php?page=aquit_settings">Settings</a></li>
		</ul>
	</div>
<?php
	}

	// Add a new client
	function page_addclient(){
		if( $_POST['submit'] ){
			$client = array(
					'author'     => $_POST['client_author'],
					'name'       => $_POST['client_name'],
					'email'      => $_POST['client_email'],
					'address'    => $_POST['client_address'],
					'phone'      => $_POST['client_phone'],
					'registered' => $_POST['client_registered']
				);

			global $wpdb;
			$table = $wpdb->prefix.'clients';
			$query = "SELECT * FROM $table WHERE client_email = %s";
			$check = $wpdb->get_var( $wpdb->prepare( $query, $client['email'] ) );	
			if( $check == NULL ){
				$query = "INSERT INTO $table ( client_author, client_name, client_email, client_address, client_phone, client_registered ) VALUES ( %d, %s, %s, %s, %s, %s )";
				$wpdb->query( $wpdb->prepare( $query, $client ) );
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
						<input type="email" name="client_email" required />
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
						<th scope="row" class="column-id"><span><?php echo $client->ID ?></span></th>
						<td class="name column-name"><strong><?php echo $client->client_name ?></strong></td>
						<td class="email column-email"><a href="<?php echo $client->client_email ?>"><?php echo $client->client_email ?></a></td>
						<td class="phone column-phone"><span><?php echo $client->client_phone ?></span></td>
						<td class="domains column-domains">
	<?php
			global $wpdb;
			$table = $wpdb->prefix.'clientdomain';
			$query = "SELECT * FROM $table WHERE client_id = %s";
			$domains = $wpdb->get_results( $wpdb->prepare($query, $client->ID) );
			$i=0;
			foreach($domains as $domain){
				$i++;
			}
			echo $i;
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

	// Manage Domain
	function page_adddomain(){
		global $wpdb;
		$settings = get_option('justaquit_settings');
		function scripts(){
			$settings = get_option('justaquit_settings');
?>
			<script type="text/javascript">
				jQuery(function($){
					$('#domain_name').keyup(function(){
						var value = $(this).attr('value');
						value = value.replace('.', '');
						value = value.replace('-', '');
						value = value.replace('_', '');
						value = value.replace(' ', '');
						var user = value.substr(0,<?php echo $settings['dbUserSize'] ?>);
						var name = value.substr(0, 20);
						$('#db_user').attr( 'value', user );
						$('#db_name').attr('value', name);
					});
					$('#domain_name').keyup(function(){
						var value = $(this).attr('value');
						value = value.replace(' ', '');
						$(this).attr('value', value);
					})
				});
			</script>
<?php
		}
		add_action('admin_footer', 'scripts');
?>
	<div class="wrap">
<?php
		if( $_POST['submit'] ){
			$client_id = $_POST['client_id'];	
			if( $_POST['domain_wp'] == true ){
				$domain_wp = 1;
				$db_name = $settings['tablePrefix'].$_POST['db_name'];
				$db_user = $settings['userPrefix'].$_POST['db_user'];
				$db_password = $_POST['db_password'];

				$table = $wpdb->prefix.'databases';
				
				//Verify if database exists
				$i = 1;
				do{
					$query = "SELECT * FROM $table WHERE db_name = %s";
					$check = $wpdb->get_var( $wpdb->prepare($query, $db_name) );
					if( $check!= NULL ){
						if($i==1)
							$db_name = $db_name.$i; // Rename DB
						else {
							$db_name = substr( $db_name, 0, strlen($db_name)-1 ).$i;
						}
					}
					$i++;
				} while ( $check != NULL );

				// Verify if user exists
				$i = 1;
				do{
					$query = "SELECT * FROM $table WHERE db_user = %s";
					$check = $wpdb->get_var( $wpdb->prepare($query, $db_user) );
					if( $check != NULL )
						$db_user = substr($db_user, 0, 15).$i;
					$i++;
				} while ( $check != NULL );

				// Add info to our database
				$query = "INSERT INTO $table ( db_name, db_user, db_password, client_id ) VALUES ( %s, %s, %s, %d ) ";
				$wpdb->query( $wpdb->prepare($query, $db_name, $db_user, $db_password, $client_id) );

				// Datababase ID
				$query = "SELECT * FROM $table WHERE db_name = %s AND db_user = %s";
				$result = $wpdb->get_results( $wpdb->prepare($query, $db_name, $db_user) );
				$database_id = $result[0]->ID;

				// Create Database
				$query = "CREATE DATABASE $db_name;";
				$wpdb->query( $query );
				// Create User
				$query = "CREATE USER %s@'localhost' IDENTIFIED BY  %s;";
				$wpdb->query( $wpdb->prepare($query, $db_user, $db_password) );
				// Grant usage
				$query = "GRANT USAGE ON * . * TO  %s@'localhost' IDENTIFIED BY  %s;";
				$wpdb->query( $wpdb->prepare($query, $db_user, $db_password) );
				// Grant access
				$query = "GRANT ALL PRIVILEGES ON  `$db_name` . * TO  %s@'localhost';";
				$wpdb->query( $wpdb->prepare($query, $db_user) );
			} else {
				$domain_wp = 0;
			}
			
			$linode_did = $_POST['linode_did'];
			$domain_name = $_POST['domain_name'];
			$admin_email = get_bloginfo('admin_email');

			if($settings['linodeAPI'] != ''){
				if( $linode_did == 9999 ){
					$table = $wpdb->prefix.'domains';
					$query = "SELECT ID FROM $table WHERE domain_url = %s";
					$check = $wpdb->get_var( $wpdb->prepare($query, $domain_name) );
					if( $check == NULL ){
						// If currently doesn't exist create it
						require('Services/Linode.php');
						try {
							$linode = new Services_Linode($settings['linodeAPI']);
							$linode = $linode->domain_create( array( 'Domain' => $domain_name, 'Type' => 'master', 'SOA_Email' => $admin_email ) );
							$linode_did = $linode['DATA'][0]['DomainID'];
							$linode_rid = 0;
						} catch (Services_Linode_Exception $e) {
							echo $e->getMessage();
						}
						$proceed = 1;
						$domain_url = $domain_name;
						echo '<li>Domain <code>'.$domain_url.'</code> added to Linode sucessfully.</li>';
					} else {
						$proceed = 0;
						$domain_url = $domain_name;
						echo '<li>Domain <code>'.$domain_url.'</code> already exists on Linode. Try another name.</li>';
					}
				} else {
					require('Services/Linode.php');
					try {
						$linode = new Services_Linode($settings['linodeAPI']);
						$linode = $linode->domain_list( array( 'DomainID' => $linode_did ) );
						$domain_tld = $linode['DATA'][0]['DOMAIN'];
						$domain_url = $domain_name.'.'.$domain_tld;
					} catch (Services_Linode_Exception $e) {
						echo $e->getMessage();
					}
					// Verify if subdomain already exists
					$i=1;
					do {
						$table = $wpdb->prefix.'domains';
						$query = "SELECT * FROM $table WHERE domain_url = %s";
						$check = $wpdb->get_var( $wpdb->prepare($query, $domain_url) );
						if( $check != NULL )
							$domain_url = $domain_name.$i.'.'.$domain_tld;
						$i++;
					} while( $check != NULL );
					// Create subdomain at Linode
					try {
						$linode = new Services_Linode($settings['linodeAPI']);
						$linode = $linode->domain_resource_create( array( 'DomainID' => $linode_did, 'Type' => 'a', 'Name' => $domain_url, 'Target' => $settings['mainIP'] ) );
						$linode_rid = $linode['DATA'][0]['ResourceID'];
					} catch (Services_Linode_Exception $e) {
						echo $e->getMessage();
					}
					$proceed = 1;
					echo '<li>Subdomain <code>'.$domain_url.'</code> has been created.</li>'; 
				}
				if( $proceed = 1 ){
					// Add info to out database
					$domain = array(
							'client_id'     => $client_id,
							'database_id'   => $database_id,
							'author'        => $_POST['domain_author'],
							'title'         => $_POST['domain_title'],
							'url'           => $domain_url,
							'registered'    => $_POST['domain_registered'],
							'wp'            => $domain_wp
						);

					$table = $wpdb->prefix.'domains';
					$query = "INSERT INTO $table ( client_id, database_id, domain_author, domain_title, domain_url, domain_registered	, domain_wp  ) VALUES (%d, %d, %d, %s, %s, %s, %d )";
					$wpdb->query( $wpdb->prepare($query, $domain) );

					$query = "SELECT * FROM $table WHERE domain_url = %s";
					$result = $wpdb->get_results( $wpdb->prepare($query, $domain_url) );
					$domain_id = $result[0]->ID;

					$table = $wpdb->prefix.'databases';
					$query = "UPDATE $table SET domain_id = %s WHERE ID = %s";
					$wpdb->query( $wpdb->prepare($query, $domain_id, $database_id) );

					$table = $wpdb->prefix.'clientdomain';
					$query = "INSERT INTO $table ( client_id, domain_id ) VALUES ( %d, %d )";
					$wpdb->query( $wpdb->prepare($query, $client_id, $domain_id) );

					// Create folder
					$dir = $settings['mainFolder'].$domain_url.'/';
					$exec = 'mkdir '.$dir;
					shell_exec($exec);

					echo '<li>Folder created in this location: <code>'.$dir.'</code></li>';

					// Create Virtual Host
					$filename = plugin_dir_path(__FILE__).'virtualhost.txt';
					$file = fopen($filename, "r");
					$content = fread( $file, filesize($filename) );
					fclose($file);
					$content = preg_replace( "/admin_email/", get_bloginfo('admin_email'), $content );
					$content = preg_replace( "/domain_url/", $domain['url'], $content );
					$home_dir = substr($dir, 0, strlen($dir)-1);
					$content = preg_replace( "/home_dir/", $home_dir, $content );
					$content = preg_replace( "/settings_user/", $settings['mainUser'], $content );
					$content = preg_replace( "/settings_home/", $settings['mainFolder'].'log', $content );
					$filename = $settings['virtualHost'].$domain['url'];
					$file = fopen($filename, "a+");
					fwrite( $file, $content );
					fclose($file);
					// Enable Virtual Host
					$exec = '/usr/sbin/a2ensite '.$domain['url'];
					shell_exec($exec);					

					// Echo Message
					echo '<li>Domain added to Apache Virtual Host</li>';

					if( $domain_wp == 1 ){
						$exec = 'cd '.$dir.' && svn co http://core.svn.wordpress.org/tags/3.4.1 .';
						shell_exec($exec);

						// Edit wp-config.php
						$filename = $dir.'wp-config-sample.php';
						$file = fopen($filename, "r");
						$content = fread( $file, filesize($filename) );
						fclose($file);
						$content = preg_replace("/database_name_here/", $db_name, $content);
						$content = preg_replace("/username_here/", $db_user, $content);
						$content = preg_replace("/password_here/", $db_password, $content);
						$filename = $dir.'wp-config.php';
						$file = fopen($filename, "a+");
						fwrite( $file, $content );
						fclose($file);
    				// Change file Owner
    				$exec = 'chown -hR '.$settings['mainUser'].':'.$settings['mainUser'].' '.$dir;
    				shell_exec($exec);
    				// Echo Message
						echo '<li>WordPress hass been installed successfully</li>';
					}
					echo '<li>Process finish.</li>';
					// Reload & Restart apache
					$exec = 'chmod 4755 '.plugin_dir_path(__FILE__).'exec.sh';
					shell_exec($exec);
					$exec = '.'.plugin_dir_path(__FILE__).'exec.sh';
					shell_exec($exec);
				}
			}
		}
?>			
		<h2>Add New Domain</h2>
		<form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>?page=aquit_adddomain">
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
						<span class="description">or <a href="<?php bloginfo('url') ?>/wp-admin/admin.php?page=aquit_addclient">add a new client</a></span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label>Project Title:</label>
					</th>
					<td>
						<input type="text" name="domain_title" required />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label>Domain</label>
					</th>
					<td>
						<input type="text" name="domain_name" id="domain_name" required />
<?php if($settings['linodeAPI']!='') { ?>
						<select name="linode_did">
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
<?php } else { ?>
<input type="hidden" value="0" name="linode_did" />
<?php } ?>
						<span class="description">User domain or subdomain. Example: DOMAIN.justaquit.net</span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label>Install WordPress</label>
					</th>
					<td>
						<input type="checkbox" name="domain_wp" checked />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label>MySQL Database:</label>
					</th>
					<td>
						<?php echo $settings['tablePrefix'] ?><input type="text" name="db_name" id="db_name" readonly />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label>MySQL User</label>
					</th>
					<td>
						<?php echo $settings['userPrefix'] ?><input type="text" name="db_user" id="db_user" readonly />
					</td>
				</tr>
				<tr valign="top">
<?php
function get_data($url) {
	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}
$db_password = get_data('http://www.makeagoodpassword.com/password/strong/');
?>
					<th scope="row">
						<label>MySQL Password:</label>
					</th>
					<td>
						<input type="text" name="db_password" value="<?php echo $db_password ?>" />
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
					<th scope="col" id="title" class="manage-column column-title"><span>Title</span></th>
					<th scope="col" id="client" class="manage-column column-client"><span>Client</span></th>
					<th scope="col" id="url" class="manage-column column-url"><span>URL</span></th>
				</tr>
			</thead>
			<tbody id="the-list" class="list:domain">
<?php
	$table = $wpdb->prefix.'domains';
	$query = "SELECT * FROM $table";
	$domains = $wpdb->get_results( $wpdb->prepare($query) );
	foreach( $domains as $domain ):
?>
				<tr>
					<th scope="row" class="column-id"><?php echo $domain->ID ?></th>
					<td class="title column-title"><strong><?php echo $domain->domain_title ?></strong></td>
					<td class="client column-client">
						<span>
<?php 
	$table = $wpdb->prefix.'clients';
	$query = "SELECT client_name FROM $table WHERE ID = %s";
	$client = $wpdb->get_var( $wpdb->prepare( $query, $domain->client_id ) );
	echo $client;
?>
						</span>
					</td>
					<td class="url column-url"><a href="//<?php echo $domain->domain_url ?>" target="_blank"><?php echo $domain->domain_url ?></a></td>
				</tr>
<?php
	endforeach;
?>
			</tbody>
		</table>
	</div>
<?php		
	}

	function page_databases(){
		global $wpdb;
		$settings = get_option('justaquit_settings');
?>
	<div class="wrap">
		<h2>Lists Databases</h2>

		<table class="wp-list-table widefat fixed databases" cellspacing="0">
			<thead>
				<tr>
					<th scope="col" id="id" class="manage-column column-id"><span>ID</span></th>
					<th scope="col" id="domain" class="manage-column column-domain"><span>Domain</span></th>
					<th scope="col" id="name" class="manage-column column-name"><span>Name</span></th>
					<th scope="col" id="user" class="manage-column column-user"><span>User</span></th>
					<th scope="col" id="password" class="manage-column column-password"><span>Password</span></th>
					<th scope="col" id="client" class="manage-column column-client"><span>Client</span></th>
				</tr>
			</thead>
			<tbody id="the-list" class="list:domain">
<?php
	$table = $wpdb->prefix.'databases';
	$query = "SELECT * FROM $table";
	$databases = $wpdb->get_results( $wpdb->prepare($query) );
	foreach( $databases as $database ):
?>
				<tr>
					<th scope="row" class="column-id">
						<span><?php echo $database->ID ?></span>
					</th>
					<td class="domain column-domain">
						<span>
<?php
		$table = $wpdb->prefix.'domains';
		$query = "SELECT domain_url FROM $table WHERE database_id = %s";
		$domain_url = $wpdb->get_var( $wpdb->prepare($query, $database->ID) );
		echo '<a href="http://'.$domain_url.'" target="_blank">'.$domain_url.'</a>';
?>
						</span>
					</td>
					<td class="name column-name">
						<span><?php echo $database->db_name ?></span>
					</td>
					<td class="user column-user">
						<span><?php echo $database->db_user ?></span>
					</td>
					<td class="password column-password">
						<span><?php echo esc_attr($database->db_password) ?></span>
					</td>
					<td class="client column-client">
						<span>
<?php
	$table = $wpdb->prefix.'clients';
	$query = "SELECT client_name FROM $table WHERE ID = %s";
	$client_name = $wpdb->get_var( $wpdb->prepare($query, $database->client_id) );
	echo $client_name;
?>
						</span>
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

	function page_migrate(){
		global $wpdb;
		$settings = get_option('justaquit_settings');

		if( $_POST['submit'] ) :
			$domain = array(
					'ID' => $_POST['domainID'],
					'url' => $_POST['domainURL']
				);

			// Check if the new domain try to replace any already registered domain
			$table = $wpdb->prefix.'domains';
			$query = "SELECT * FROM $table WHERE domain_url = %s";
			$check = $wpdb->get_var( $wpdb->prepare($query, $domain['url']) );
			if( $check == NULL ) :
				// Update URL
				$table = $wpdb->prefix.'domains';
				$query = "UPDATE $table SET domain_url = %s WHERE ID = %d";
				$wpdb->query( $wpdb->prepare($query, $domain['url'], $domain['ID']) );

				// Erase Linode Resourse ID
				$table = $wpdb->prefix.'domains';
				$query = "SELECT domain_rid FROM $table WHERE ID = %s";
				$domain_rid = $wpdb->get_var( $wpdb->prepare($query, $domain['ID']) );
				require('Services/Linode.php');
				try {
					$linode = new Services_Linode($settings['linodeAPI']);
					$linode = $linode->domain_resource_delete( array( 'ResourceID' => $domain_rid ) );
				} catch (Services_Linode_Exception $e) {
					echo $e->getMessage();
				}

				// Create Linode Resourse ID
				require('Services/Linode.php');
				try {
					$linode = new Services_Linode($settings['linodeAPI']);
					$args = array(
							'DomainID' => $settings['linodeDomain'],
							'Type' => 'a',
							'Name' => $domain['url'],
							'Target' => $settings['mainIP']
						);
					$domain_rid = $linode->domain_resource_create( $args );
					$domain_rid = $domain_rid['DATA'][0]['ResourceID'];
				} catch (Services_Linode_Exception $e) {
					echo $e->getMessage();
				}

				// Save new Linode Resource ID
				$table = $wpdb->prefix.'domains';
				$query = "UPDATE $table SET linode_rid = %d WHERE ID = %d";
				$wpdb->query( $wpdb->prepare($query, $domain_rid, $domain['ID']) );

				// Destroy Virtual Host
				$table = $wpdb->prefix.'domains';
				$query = "SELECT domain_url FROM $table WHERE ID = %s";
				$domain_url = $wpdb->get_var( $wpdb->prepare($query, $domain['ID']) );
				$exec = 'a2dissite '.$domain_url;
				shell_exec($exec);
				$exec = 'rm -f '.$settings['virtualHost'].$domain_url;
				shell_exec($exec);

				// Rename Folder
				$exec = 'cd '.$settings['mainFolder'].' && mv '.$domain_url.' '.$domain['url'];
				shell_exec($exec);

				// Create Virtual Host
				$dir = $settings['mainFolder'].$domain['url'];
				$filename = plugin_dir_path(__FILE__).'virtualhost.txt';
				$file = fopen($filename, "r");
				$content = fread( $file, filesize($filename) );
				fclose($file);
				$content = preg_replace( "/admin_email/", get_bloginfo('admin_email'), $content );
				$content = preg_replace( "/domain_url/", $domain['url'], $content );
				$content = preg_replace( "/home_dir/", $home_dir, $content );
				$content = preg_replace( "/settings_user/", $settings['mainUser'], $content );
				$content = preg_replace( "/settings_home/", $settings['mainFolder'].'log', $content );
				$filename = $settings['virtualHost'].$domain['url'];
				$file = fopen($filename, "a+");
				fwrite( $file, $content );
				fclose($file);

				// Activa Virtual Host
				$exec = 'a2ensite '.$domain['url'];
				shell_exec($exec);

				// MySQL Fix
				$table = $wpdb->prefix.'databases';
				$query = "SELECT * FROM $table WHERE domain_id = %s";
				$database = $wpdb->get_row( $wpdb->prepare($query, $domain['ID']) );
				$domain_new = $domain['url'];
				$query = "USE $database->name; ";
				$query .= "UPDATE wp_options SET option_value = REPLACE(option_value, $domain_url, $domain_new); ";
				$query .= "UPDATE wp_posts SET post_content = REPLACE(post_content, $domain_url, $domain_new); ";
				$query .= "UPDATE wp_posts SET guid = REPLACE(guid, $domain_url, $domain_new); ";
				$query .= "UPDATE wp_postmeta SET meta_value = REPLACE(meta_value, $domain_url, $domain_new); ";
				$wpdb->query($query);
			else : // Check: NULL
				echo 'Domain already exists. Please use another domain name.';
			endif; // Check: NOT NULL
		else : // Post: Submit
	?>
		<div class="wrap">
			<h2>Migrate a Domain</h2>
			<form action="post" method="<?php echo $_SERVER['PHP_SELF'] ?>?page=aquit_migrate">
				<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row">
							<label>Old Domain</label>
						</th>
						<td>
							<select name="domainID">
	<?php
	$table = $wpdb->prefix.'domains';
	$query = "SELECT * FROM $table";
	$domains = $wpdb->get_results($query);
	foreach( $domains as $domain ) :
	?>
		<option value="<?php echo $domain->ID ?>"><?php echo $domain->domain_url ?></option>
	<?php	
	endforeach;
	?>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label>New Domain URL</label>
						</th>
						<td>
							<input type="text" name="domainURL" />
						</td>
					</tr>
				</tbody>
				</table>
				<p class="submit"><input type="submit" value="<?php _e('Save Changes') ?>" class="button-primary" /></p>
			</form>
		</div>
	<?php
		endif; // Post NO Submit
	}

	function page_settings(){
		function scripts(){
			echo '<script type="text/javascript" src="'.plugin_dir_url(__FILE__).'javascript/main.jquery.js"></script>';
		}
		add_action('admin_footer', 'scripts');
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
	$linode = new Services_Linode($settings['linodeAPI']);
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
				<tr valign="top">
					<th scope="row">
						<label>Main IP Address</label>
					</th>
					<td>
						<input type="text" name="justaquit_settings[mainIP]" value="<?php echo $settings['mainIP'] ?>" />
					</td>
				</tr>
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
				<tr valign="top">
					<th scope="row">
						<label>Table Prefix:</label>
					</th>
					<td>
						<input type="text" name="justaquit_settings[tablePrefix]" id="tablePrefix" value="<?php echo $settings['tablePrefix'] ?>" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label>User Prefix:</label>
					</th>
					<td>
						<input type="text" name="justaquit_settings[userPrefix]" id="userPrefix" value="<?php echo $settings['userPrefix'] ?>" />
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
	add_submenu_page( 'aquit', 'Manage Clients', 'Manage Clients', 'administrator', 'aquit_addclient', array( 'justaquit', 'page_addclient' ) );
	add_submenu_page( 'aquit', 'Manage Domains', 'Manage Domains', 'administrator', 'aquit_adddomain', array( 'justaquit', 'page_adddomain' ) );
	add_submenu_page( 'aquit', 'List Databases', 'List Databases', 'administrator', 'aquit_databases', array( 'justaquit', 'page_databases' ) );
	add_submenu_page( 'aquit', '', 'Migrate Domain', 'administrator', 'aquit_migrate', array( 'justaquit', 'page_migrate' ) );
	add_submenu_page( 'aquit', 'Settings', 'Settings', 'administrator', 'aquit_settings', array( 'justaquit', 'page_settings' ) );
	add_action('admin_init', array('justaquit', 'settings_register'));
}
add_action('admin_menu', 'justaquit_init');
register_activation_hook( __FILE__, array('justaquit', 'install') );
?>