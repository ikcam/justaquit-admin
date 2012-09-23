<?php
/*
* Domain Class
*/

public class Domain extends JAdmin {
	private $ID;
	private $title;
	private $url;
	private $priority;
	private $client_id;
	private $author;
	private $linode_did;
	private $linode_rid;
	private $wordpress;
	private $creation_date;

	$settings = get_option( 'jadmin_settings' );

	public function __construct( $title, $url, $priority, $client_id, $author, $linode_rid, $wordpress, $creation_date ){
		$this->title         = esc_attr($title);
		$this->url           = esc_attr($url);
		$this->priority      = intval($priority);
		$this->client_id     = intval($client_id);
		$this->author        = intval($author);
		$this->linode_did    = intval($linode_did);
		$this->wordpress     = intval($wordpress);
		$this->creation_date = strtotime( current_date('mysql') );
	}

	private function check_domain(){
		global $wpdb;
		$table = $wpdb->prefix.'domains';

		$query = "SELECT COUNT(*) FROM $table WHERE url = %s;";
		$result = $wpdb->get_var( $wpdb->prepare($query, $this->url) );

		if( $result == NULL )
			return TRUE;
		else
			return FALSE;
	}

	private function create_linode_did(){
		global $settings;

		if( $this->linode_did == 0 ):
			require('Services/Linode.php');
			try {
				$linode = new Services_Linode($settings['linode_key']);
				$linode = $linode->domain_create(
						array(
							'Domain'    => $this->url,
							'Type'      => 'master',
							'SOA_Email' => get_bloginfo('admin_email')
						)
					);
				$this->linode_did = $linode['DATA'][0]['DomainID'];
				$this->linode_rid = 0;
			} catch (Services_Linode_Exception $e) {
				echo $e->getMessage();
			}
		endif;
	}

	private function create_linode_rid(){
		global $settings;

		if( $this->linode_did != 0 ):
			$this->url = $this->url.'.'.get_linode_domain_name( $this->linode_did );

			require('Services/Linode.php');
			try{
				$linode = new Services_Linode($settings['linode_key']);
				$linode = $linode->domain_resource_create(
						array(
							'DomainID' => $this->linode_did,
							'Type'     => 'a',
							'Name'     => $this->url,
							'Target'   => $settings['server_ip']
						)
					);
				$this->linode_rid = $linode['DATA'][0]['ResourceID'];
			} catch (Services_Linode_Exception $e) {
				echo $e->getMessage();
			}
		endif;
	}

	private function create_folder(){
		global $settings;

		$dir = $settings['server_folder'].$this->url.'/';
		$exec = 'mkdir '.$dir;
		shell_exec($exec);
	}

	private function create_vhost(){
		global $settings;
		// Read file
		$filename = plugin_dir_path(__FILE__).'virtualhost.txt';
		$file     = fopen($filename, "r");
		$content  = fread( $file, filesize($filename) );
		fclose($file);
		// Rewrite content
		$content  = preg_replace( "/admin_email/", get_bloginfo('admin_email'), $content );
		$content  = preg_replace( "/domain_url/", $this->url, $content );
		$content  = preg_replace( "/home_dir/", $settings['server_folder'].$this->url, $content );
		$content  = preg_replace( "/settings_user/", $settings['server_user'], $content );
		$content  = preg_replace( "/settings_home/", $settings['server_folder'].'log', $content );
		// Set filename and save it
		$filename = $settings['server_apache'].$this->url;
		$file     = fopen($filename, "a+");
		fwrite( $file, $content );
		fclose( $file );
		// Enable Virtual Host
		$exec = 'a2ensite '.$this->url;
		shell_exec($exec);	
	}

	private function create_wordpress( $db_id ){
		global $settings;
		$db = get_database( $db_id );

		$dir  = $settings['server_folder'].$this->url.'/';
		$exec = 'cd '.$dir.' && svn co http://core.svn.wordpress.org/tags/3.4.2 .';
		shell_exec($exec);

		// Read wp-config.php
		$filename = $dir.'wp-config-sample.php';
		$file     = fopen($filename, "r");
		$content  = fread( $file, filesize($filename) );
		fclose($file);
		// Rewrite wp-config.php
		$content = preg_replace("/database_name_here/", $db->name, $content);
		$content = preg_replace("/username_here/", $db->user, $content);
		$content = preg_replace("/password_here/", $db->password, $content);
		// Save file
		$filename = $dir.'wp-config.php';
		$file = fopen($filename, "a+");
		fwrite( $file, $content );
		fclose($file);
		// Change file Owner
		$exec = 'chown -hR '.$settings['server_user'].':'.$settings['server_user'].' '.$dir;
		shell_exec($exec);
	}

	public function add_domain(){
		global $wpdb;
		$table = $wpdb->prefix.'domains';

		// Step 1: Verify if domains already exists.
		if( $this->check_domain() ){
			// Step 2: Create Linode Domain ID
			$this->create_linode_did();
			// Step 3: Create Linode Resorce ID
			$this->create_linode_rid();
			// Step 4: Insert information into database.
			$data = array(
					'title'         => $this->title,
					'url'           => $this->url,
					'priority'      => $this->priority,
					'client_id'     => $this->client_id,
					'author'        => $this->author,
					'linode_did'    => $this->linode_did,
					'linode_rid'    => $this->linode_rid,
					'creation_date' => $this->creation_date,
				);
			$format = array( '%s', '%s', '%d', '%d', '%d', '%d', '%d', '%d' );
			$wpdb->insert( $table, $data, $format );
			$this->ID = $wpdb->insert_id;
			// Step 5: Create database for new domain
			$database = new Database( $this->url, $this->ID );
			$database->add_database();
			// Step 6: Create new folder
			$this->create_folder();
			// Step 7: Create virtual host
			$this->create_vhost();
			// Step 8: Install WordPress
			if( $this->wordpress == 1 )
				$this->create_wordpress( $database->get_ID() );
			// Step 9: Restart Apache
			restart_apache();
		else:
			echo 'Domain already exists';
		endif;
	}

	public function update_domain(){
		global $wpdb;
		$table = $wpdb->prefix.'domains';
	}
}

?>