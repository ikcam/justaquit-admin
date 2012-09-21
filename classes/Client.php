<?php
/*
* Client Class
*/

public class Client extends JAdmin {
	private $first_name;
	private $last_name;
	private $email;
	private $address;
	private $phone;
	private $registration_date;

	public function __construct( $fist_name, $last_name, $email, $address, $phone ){
		$this->first_name = esc_attr($first_name);
		$this->last_name = esc_attr($last_name);
		$this->email = esc_attr($email);
		$this->address = esc_attr($address);
		$this->phone = esc_attr($phone);
		$this->registration_date = strtotime( current_date('mysql') );
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
				'registration_date' => $this->registration_date
			);
		$format = array( '%s', '%s', '%s', '%s', '%s', '%d' );
		$wpdb->insert( $table, $data, $format );

		return $wpdb->insert_id;
	}

	public function delete_client( $ID ){
		global $wpdb;
		$table = $wpdb->prefix.'clients';

		$query = "DELETE FROM $table WHERE ID = %d";

		return $wpdb->query( $wpdb->prepare($query, $ID) );
	}

	public function update_client( $ID ){
		global $wpdb;
		$table = $wpdb->prefix.'clients';

		$data   = array(
			'first_name'        => $this->first_name,
			'last_name'         => $this->last_name,
			'email'             => $this->email,
			'address'           => $this->address,
			'phone'             => $this->phone,
		);
		$where  = array( 'ID' => $ID );
		$format = array( '%s', '%s', '%s', '%s', '%s' );

		return $wpdb->update( $table, $data, $where, $format );
	}	
}
?>