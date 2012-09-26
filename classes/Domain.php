<?php
/*
* Domain Class
*/

class Domain extends JAdmin {
	private $ID;
	private $client_id;
	private $title;
	private $url;
	private $linode_did;
	private $priority;
	private $wordpress;
	private $author;
	private $linode_rid;
	private $creation_date;

	public function __construct( $client_id, $title, $url, $linode_did, $priority, $wordpress, $author ){
		$this->client_id     = intval($client_id);
		$this->title         = $title;
		$this->url           = $url;
		$this->linode_did    = intval($linode_did);
		$this->priority      = intval($priority);
		$this->wordpress     = intval($wordpress);
		$this->author        = intval($author);
		$this->creation_date = strtotime( current_time('mysql') );
	}

	private function check_domain(){
		global $wpdb;
		$table = $wpdb->prefix.'domains';

		$query = "SELECT COUNT(*) FROM $table WHERE url = %s;";
		$result = $wpdb->get_var( $wpdb->prepare($query, $this->url) );

		if( $result == 0 )
			return TRUE;
		else
			return FALSE;
	}

	private function create_linode_did(){
		global $settings;

		if( $this->linode_did == 0 ):
			require_once('Services/Linode.php');
			try {
				$linode = new Services_Linode($settings['linode_key']);
				$linode = $linode->domain_create(
						array(
							'Domain'    => $this->url,
							'Type'      => 'master',
							'SOA_Email' => get_bloginfo('admin_email')
						)
					);
				$this->linode_did = $linode['DATA']['DomainID'];
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

			require_once('Services/Linode.php');
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
				$this->linode_rid = $linode['DATA']['ResourceID'];
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
		$filename = BASEPATH.'virtualhost.txt';
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
		if( $this->check_domain() ) :
			// Step 2: Create Linode Domain ID
			$this->create_linode_did();
			echo '<li>Linode domain ID saved.</li>';
			// Step 3: Create Linode Resorce ID
			$this->create_linode_rid();
			echo '<li>Linode resource ID saved.</li>';
			// Step 4: Insert information into database.
			$data = array(
					'title'         => $this->title,
					'url'           => $this->url,
					'priority'      => $this->priority,
					'client_id'     => $this->client_id,
					'author'        => $this->author,
					'linode_did'    => $this->linode_did,
					'linode_rid'    => $this->linode_rid,
					'wordpress'     => $this->wordpress,
					'creation_date' => $this->creation_date,
				);
			$format = array( '%s', '%s', '%d', '%d', '%d', '%d', '%d', '%d', '%d' );
			$wpdb->insert( $table, $data, $format );
			$this->ID = $wpdb->insert_id;
			echo '<li>Domain info inserted into database.</li>';
			// Step 5: Create database for new domain
			$database = new Database( $this->url, $this->ID );
			$database->add_database();
			echo '<li>Database created.</li>';
			// Step 6: Create new folder
			$this->create_folder();
			echo '<li>Folder created.</li>';
			// Step 7: Create virtual host
			$this->create_vhost();
			echo '<li>Virtual Host created.</li>';
			// Step 8: Install WordPress
			if( $this->wordpress == 1 ) :
				$this->create_wordpress( $database->get_ID() );
				echo '<li>WordPress installed.</li>';
			endif;
			// Step 9: Restart Apache
			// restart_apache();
		else:
			echo 'Domain already exists';
		endif;
	}

	public function get_ID(){
		return $this->ID;
	}

	public function update_domain(){
		global $wpdb;
		$table = $wpdb->prefix.'domains';
	}
}

?>