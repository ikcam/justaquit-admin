<?php
/*
* Client Class
*/

public class Client extends justaquit_admin {
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

	public function addClient(){
		
	}	
}

?>