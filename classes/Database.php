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
		$this->basename  = $basename;
		$this->password  = get_data('http://www.makeagoodpassword.com/password/strong/');
		$this->domain_id = $domain_id;
	}

	private function setup_info(){
		global $wpdb;
		global $settings;
		$table = $wpdb->prefix.'databases';

		$basename = preg_replace( '/./', '', $this->basename );
		$basename = preg_replace( '/-/', '', $basename );
		$basename = $settings['database_prefix'].$basename;
		echo 'Basename: '.$basename.'<br />';
		// Check Name
		$name = substr( $basename, 0, 25);
		$i=0;
		do{
			$i++;
			$query = "SELECT COUNT(*) FROM $table WHERE name = %s";
			$result = $wpdb->get_var( $wpdb->prepare($query, $name) );
			if( $result > 0 ){
				$name = substr( $name, 0, strlen($name)-1 ).$i;
			}
		} while( $result > 0 );
		// Check User
		$user = substr( $basename, 0, 16);
		$i=0;
		do{
			$i++;
			$query = "SELECT COUNT(*) FROM $table WHERE user = %s";
			$result = $wpdb->get_var( $wpdb->prepare($query, $user) );
			if( $result > 0 ){
				$user = substr( $user, 0, strlen($user)-1 ).$i;
			}
		} while( $result > 0 );

		$this->name = $name;
		$this->user = $user;
		echo 'Name: '.$this->name.'<br />';
		echo 'User: '.$this->user.'<br />';
	}
	
	public function add_database(){
		global $wpdb;
		$table = $wpdb->prefix.'databases';

		$this->setup_info();

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
		$query = "GRANT ALL PRIVILEGES ON  $this->name . * TO  %s@'localhost';";
		$wpdb->query( $wpdb->prepare($query, $this->user) );
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

			$query = "DROP USER %s@'localhost';";
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
		$query = "GRANT ALL PRIVILEGES ON  $this->name . * TO  %s@'localhost';";
		$wpdb->query( $wpdb->prepare($query, $this->user) );
	}

	public function get_ID(){
		return $this->ID;
	}
}
?>
