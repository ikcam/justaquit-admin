<?php
/* 
Functions
*/

/*
* Name:
*	 get_client( $ID )
*
* Args: 
* 	ID: The client ID
*
* Return:
* 	Array with client information.
*/
function get_client( $ID ){
	global $wpdb;

	$table  = $wpdb->prefix.'clients';
	$query  = "SELECT * FROM $table WHERE ID = %d";
	$client = $wpdb->get_row( $wpdb->prepare($query, $ID) );

	return $client;
}


?>