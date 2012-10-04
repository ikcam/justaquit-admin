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

	private function destroy_linode_did( $did ){
		global $settings;

		require_once('Services/Linode.php');
		try {
			$linode = new Services_Linode($settings['linode_key']);
			$linode = $linode->domain_delete(
					array(
						'DomainID'    => $did
					)
				);
		} catch (Services_Linode_Exception $e) {
			echo $e->getMessage();
		}
	}

	private function update_linode_did( $did ){
		global $settings;

		require_once('Services/Linode.php');
		try {
			$linode = new Services_Linode($settings['linode_key']);
			$linode = $linode->domain_update(
					array(
						'DomainID'    => $did,
						'Domain'      => $this->url
					)
				);
			$this->linode_did = $linode['DATA']['DomainID'];
			$this->linode_rid = 0;
		} catch (Services_Linode_Exception $e) {
			echo $e->getMessage();
		}

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

	private function destroy_linode_rid( $did, $rid ){
		global $settings;

		require_once('Services/Linode.php');
		try{
			$linode = new Services_Linode($settings['linode_key']);
			$linode = $linode->domain_resource_delete(
					array(
						'DomainID'   => $did,
						'ResourceID' => $rid 
					)
				);
		} catch (Services_Linode_Exception $e) {
			echo $e->getMessage();
		}
	}

	private function create_folder(){
		global $settings;

		$dir = $settings['server_folder'].$this->url.'/';
		$exec = 'mkdir '.$dir;
		shell_exec($exec);
	}

	private function detroy_folder(){
		global $settings;

		$dir = $settings['server_folder'].$this->url;
		$exec = 'rm -rf '.$dir;
		shell_exec($exec);
	}

	private function update_folder( $ID ){
		global  $settings;

		$domain = get_domain( $ID );

		$dir1 = $settings['server_folder'].$domain->url;
		$dir2 = $settings['server_folder'].$this->url;
		$exec = 'mv '.$dir1.' '.$dir2;
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
		$exec = 'ln -f '.$settings['server_apache'].$this->url.' '.$settings['server_apache2'].$this->url;
		shell_exec($exec);	
	}

	private function destroy_vhost( $ID ){
		global $settings;
		
		$domain = get_domain( $ID );

		$dir  = $settings['server_apache2'].$domain->url;
		$exec = 'rm -f '.$dir;
		shell_exec( $exec );
		$dir  = $settings['server_apache'].$domain->url;
		$exec = 'rm -f '.$dir;
		shell_exec( $exec );
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

	private function update_wordpress( $ID ){
		global $wpdb;
		$database = get_database_by_domain( $ID );
		$domain   = get_domain( $ID );

		$query = "UPDATE $database->name.wp_posts SET post_content = REPLACE( post_content, %s, %s );";
		$wpdb->query( $wpdb->prepare($query, $domain->url, $this->url) );
		$query = "UPDATE $database->name.wp_posts SET guid = REPLACE( guid, %s, %s );";
		$wpdb->query( $wpdb->prepare($query, $domain->url, $this->url) );
		$query = "UPDATE $database->name.wp_options SET option_value = REPLACE( option_value, %s, %s );";
		$wpdb->query( $wpdb->prepare($query, $domain->url, $this->url) );
		$query = "UPDATE $database->name.wp_postmeta SET meta_value = REPLACE( meta_value, %s, %s );";
		$wpdb->query( $wpdb->prepare($query, $domain->url, $this->url) );
	}

	public function add_domain(){
		global $wpdb;
		$table = $wpdb->prefix.'domains';

		// Step 1: Verify if domains already exists.
		if( $this->check_domain() ) :
			// Step 3: Create Linode Resorce ID
			$this->create_linode_rid();
			// Step 2: Create Linode Domain ID
			$this->create_linode_did();
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
			// Step 5: Create database for new domain
			$database = new Database( $this->ID );
			$database->add_database();
			// Step 6: Create new folder
			$this->create_folder();
			// Step 7: Create virtual host
			$this->create_vhost();
			// Step 8: Install WordPress
			if( $this->wordpress == 1 ) :
				$this->create_wordpress( $database->get_ID() );
			endif;
			// Step 9: Restart Apache
			restart_apache();
		else:
			echo 'Domain already exists';
		endif;
	}

	public function delete_domain( $ID ){
		global $wpdb;
		$table = $wpdb->prefix.'domains';

		$domain = get_domain( $ID );
		if( $domain == NULL ):
		else:
			if( $domain->linode_rid == 0 )
				$this->destroy_linode_did( $domain->linode_did );
			else
				$this->destroy_linode_rid( $domain->linode_did, $domain->linode_rid );
			// Destoy Database
			$database = get_database_by_domain( $domain->ID );
			$db_delete = new Database( $domain->ID );
			$db_delete->delete_database( $database->ID );
			// Destoy folder
			$this->detroy_folder();
			// Destroy Virtual Host
			$this->destroy_vhost( $domain->ID );
			// Remove from database
			$query = "DELETE FROM $table WHERE ID = %d";
			$wpdb->query( $wpdb->prepare($query, $domain->ID) );
			//Restart Apache
			restart_apache();
		endif;
	}

	public function update_domain_info( $ID ){
		global $wpdb;
		$table = $wpdb->prefix.'domains';

		$data = array(
				'title' => $this->title,
				'priority' => $this->priority,
				'client_id' => $this->client_id,
			);
		$where = array( 'ID' => $ID );
		$format = array( '%s', '%d', '%d' );

		return $wpdb->update( $table, $data, $where, $format );
	}

	public function update_domain( $ID ){
		global $wpdb;
		$table = $wpdb->prefix.'domains';

		$domain = get_domain( $ID );
		if( $domain == NULL || $this->check_domain() == FALSE ):
		else:
			if( $this->linode_did == 0 && $domain->linode_rid == 0 ):
				// Old Domain to New Domain  
				$this->update_linode_did( $domain->linode_did );
			elseif( $this->linode_did == 0 && $domain->linode_rid != 0 ):
				// Old Subdomain to New Domain
				$this->destroy_linode_rid( $domain->linode_did, $domain->linode_rid );
				$this->create_linode_did();
			elseif( $this->linode_did != 0 && $domain->linode_rid == 0 ):
				// Old Domain to New Subdomain
				$this->destoy_linode_did( $domain->linode_did );
				$this->create_linode_did();
				$this->create_linode_rid();
			else:
				// Old Subdomain to New Subdomain
				$this->destroy_linode_rid( $domain->linode_did, $domain->linode_rid );
				$this->create_linode_did();
				$this->create_linode_rid();
			endif;

			$this->update_folder( $domain->ID );
			$this->destroy_vhost( $domain->ID );
			$this->create_vhost();
			$this->update_wordpress( $domain->ID );

			$data = array(
					'url' => $this->url,
					'linode_did' => $this->linode_did,
					'linode_rid' => $this->linode_rid
				);
			$where = array( 'ID' => $domain->ID );
			$format = array( '%s', '%d', '%d' );
			$wpdb->update( $table, $data, $where, $format );

			restart_apache();
		endif;
	}

	public function get_ID(){
		return $this->ID;
	}
}

?>