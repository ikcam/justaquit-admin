<?php
/*
* Database Class
*/

public class Database extends JAdmin {
	private $ID;
	private $name;
	private $user;
	private $password;
	private $domain_id;

	$settings = get_option( 'jadmin_settings' );

	public function __construct( $url, $domain_id ){
		global $settings;

		$basename        = $settings['database_prefix'].$url;
		$name            = substr( $basename, 0, 27 );
		$user            = substr( $basename, 0, 16 );
		
		$this->name      = $name;
		$this->user      = $user;
		$this->password  = get_data('http://www.makeagoodpassword.com/password/strong/');
		$this->domain_id = $domain_id;
	}

	private function check_name(){
		global $wpdb;
		$table = $wpdb->prefix.'databases';

		$i = 0;
		do {
			$i++;
			$query  = "SELECT COUNT(*) FROM $table WHERE name = %s";
			$result = $wpdb->get_var( $wpdb->prepare($query, $this->name) );
			if( $result != NULL ){
				$length     = strlen( $this->name ) - 1;
				$name       = substr( $this->name, 0, $length ).$i;
				$this->name = $name;
			}
		} while( $result != NULL );
	}

	private function check_user(){
		global $wpdb;
		$table = $wpdb->prefix.'databases';

		$i = 0;
		do {
			$i++;
			$query  = "SELECT COUNT(*) FROM $table WHERE user = %s";
			$result = $wpdb->get_var( $wpdb->prepare($query, $this->user) );
			if( $result != NULL ){
				$length     = strlen( $this->user ) - 1;
				$user       = substr( $this->user, 0, $length ).$i;
				$this->user = $user;
			}
		} while( $result != NULL );
	}

	public function add_database(){
		global $wpdb;
		$table = $wpdb->prefix.'databases';

		$this->check_name();
		$this->check_user();

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
		$query = "CREATE DATABASE %s;";
		$wpdb->query( $wpdb->prepare($query, $this->name) );
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