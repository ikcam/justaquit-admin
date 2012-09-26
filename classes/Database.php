<?php
/*
* Database Class
*/

class Database extends JAdmin {
	private $ID;
	private $basename;
	private $name;
	private $user;
	private $password;
	private $domain_id;

	public function __construct( $basename, $domain_id ){
		$basename = preg_replace( '/ /', '', $basename );
		$basename = preg_replace( '/./', '', $basename );
		$basename = preg_replace( '/-/', '', $basename );
		$this->basename  = $basename;
		$this->password  = get_data('http://www.makeagoodpassword.com/password/strong/');
		$this->domain_id = $domain_id;
	}

	private function set_basename(){
		global $settings;

		$name = $settings['database_prefix'].$this->basename;

		$this->basename = $name;
	}

	private function set_name(){
		global $wpdb;
		$table = $wpdb->prefix.'databases';

		$this->name = substr($this->basename, 0, 25);
		$i = 0;
		do {
			$i++;
			$query  = "SELECT COUNT(*) FROM $table WHERE name = %s";
			$result = $wpdb->get_var( $wpdb->prepare($query, $this->name) );
			if( $result > 0 ){
				$length     = strlen( $this->name ) - 1;
				$name       = substr( $this->name, 0, $length ).$i;
				$this->name = $name;
			}
		} while( $result > 0 );
	}

	private function set_user(){
		global $wpdb;
		$table = $wpdb->prefix.'databases';

		$this->user = substr($this->basename, 0, 16);
		$i = 0;
		do {
			$i++;
			$query  = "SELECT COUNT(*) FROM $table WHERE user = %s";
			$result = $wpdb->get_var( $wpdb->prepare($query, $this->user) );
			if( $result > 0 ){
				$length     = strlen( $this->user ) - 1;
				$user       = substr( $this->user, 0, $length ).$i;
				$this->user = $user;
			}
		} while( $result > 0 );
	}

	public function add_database(){
		global $wpdb;
		$table = $wpdb->prefix.'databases';

		$this->set_basename();
		$this->set_name();
		$this->set_user();

		$data = array(
				'name'      => $this->name,
				'user'      => $this->user,
				'password'  => $this->password,
				'domain_id' => $this->domain_id
			);
		$format = array( '%s', '%s', '%s', '%d' );
		$wpdb->insert( $table, $data, $format );
		$this->ID = $wpdb->insert_id;

		// Step 1: Create Database
		$query = "CREATE DATABASE $this->name;";
		$wpdb->query( $query );
		// Step 2: Create User
		$query = "CREATE USER %s@'localhost' IDENTIFIED BY  %s;";
		$wpdb->query( $wpdb->prepare($query, $this->user, $this->password ) );
		// Step 3: Grant usage
		$query = "GRANT USAGE ON * . * TO  %s@'localhost' IDENTIFIED BY  %s;";
		$wpdb->query( $wpdb->prepare($query, $this->user, $this->password) );
		// Step 4: Grant access
		$query = "GRANT ALL PRIVILEGES ON  %s . * TO  %s@'localhost';";
		$wpdb->query( $wpdb->prepare($query, $this->name, $this->user) );
	}

	public function delete_database( $ID ){
		global $wpdb;
		$table = $wpdb->prefix.'databases';
		
		$database = get_database( $ID );

		if( $database == NULL ):
			return FALSE;
		else:
			$query = "DROP DATABASE $database->name;";
			$wpdb->query( $query );

			$query = "DROP USER %s;";
			$wpdb->query( $wpdb->prepare($query, $database->user) );

			$query = "DELETE FROM $table WHERE ID = %d;";
			$wpdb->query( $wpdb->prepare($query,$database->ID) );
		endif;
	}

	public function get_ID(){
		return $this->ID;
	}
}

Class DBCustom extends JAdmin{
	private $ID;
	private $name;
	private $user;
	private $password;

	public function __construct( $name, $user, $password ){
		$this->name     = $name;
		$this->user     = $user;
		$this->password = $password;
	}

	public function add_database(){
		global $wpdb;
		$table = $wpdb->prefix.'databases';

		$data = array(
				'name'      => $this->name,
				'user'      => $this->user,
				'password'  => $this->password,
				'domain_id' => 0
			);
		$format = array( '%s', '%s', '%s', '%d' );
		$wpdb->insert( $table, $data, $format );
		$this->ID = $wpdb->insert_id;

		// Step 1: Create Database
		$query = "CREATE DATABASE $this->name;";
		$wpdb->query( $query );
		// Step 2: Create User
		$query = "CREATE USER %s@'localhost' IDENTIFIED BY  %s;";
		$wpdb->query( $wpdb->prepare($query, $this->user, $this->password ) );
		// Step 3: Grant usage
		$query = "GRANT USAGE ON * . * TO  %s@'localhost' IDENTIFIED BY  %s;";
		$wpdb->query( $wpdb->prepare($query, $this->user, $this->password) );
		// Step 4: Grant access
		$query = "GRANT ALL PRIVILEGES ON  %s . * TO  %s@'localhost';";
		$wpdb->query( $wpdb->prepare($query, $this->name, $this->user) );
	}

	public function get_ID(){
		return $this->ID;
	}
}
?>