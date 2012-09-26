<?php
/*
* Client Class
*/

class Client extends JAdmin {
	private $ID;
	private $first_name;
	private $last_name;
	private $email;
	private $address;
	private $phone;
	private $registration_date;
	private $author;
	private $editor;

	public function __construct( $first_name, $last_name, $email, $address, $phone, $author, $editor ){
		$this->first_name        = $first_name;
		$this->last_name         = $last_name;
		$this->email             = $email;
		$this->address           = $address;
		$this->phone             = $phone;
		$this->registration_date = strtotime( current_time('mysql') );
		$this->author            = intval($author);
		$this->editor            = intval($editor);
	}

	private function check_client(){
		global $wpdb;
		$table = $wpdb->prefix.'clients';

		$query = "SELECT COUNT(*) FROM $table WHERE email = %s";
		$result = $wpdb->get_var( $wpdb->prepare($query, $this->email) );

		if( $result == 0 )
			return TRUE;
		else
			return FALSE;
	}

	public function add_client(){
		global $wpdb;
		$table = $wpdb->prefix.'clients';

		$data = array(
				'first_name'        => $this->first_name,
				'last_name'         => $this->last_name,
				'email'             => $this->email,
				'address'           => $this->address,
				'phone'             => $this->phone,
				'registration_date' => $this->registration_date,
				'author'            => $this->author,
				'editor'            => $this->editor
			);
		$format = array( '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d' );

		if( $this->check_client() == TRUE ){
			$wpdb->insert( $table, $data, $format );
			$this->ID = $wpdb->insert_id;
		}
	}

	public function delete_client( $ID ){
		global $wpdb;
		$table = $wpdb->prefix.'clients';

		$client = get_client( $ID );

		if( is_array($client) ):
			if( has_domains($client->ID) ):
				return FALSE;
			else:
				$query = "DELETE FROM $table WHERE ID = %d;";
				$wpdb->query( $wpdb->prepare($query, $client->ID) );
			endif;
		else:
			return FALSE;
		endif;
	}

	public function update_client( $ID ){
		global $wpdb;
		$table = $wpdb->prefix.'clients';

		$data   = array(
			'first_name' => $this->first_name,
			'last_name'  => $this->last_name,
			'email'      => $this->email,
			'address'    => $this->address,
			'phone'      => $this->phone,
			'editor'     => $this->editor
		);
		$where  = array( 'ID' => $ID );
		$format = array( '%s', '%s', '%s', '%s', '%s', '%d' );

		return $wpdb->update( $table, $data, $where, $format );
	}

	public function get_ID(){
		return $this->ID;
	}
} 
?>