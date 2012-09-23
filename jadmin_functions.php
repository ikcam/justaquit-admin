<?php
/* 
Functions
*/

/*
* Name:
*	 get_client( $ID )
*
* Args: 
* 	ID: Client ID
*
* Return:
* 	Array with client information.
*/
function get_client( $ID ){
	global $wpdb;
	$table  = $wpdb->prefix.'clients';

	$query  = "SELECT * FROM $table WHERE ID = %d;";
	$client = $wpdb->get_row( $wpdb->prepare($query, $ID) );

	return $client;
}

/*
* Name:
* 	has_domains( $ID )
*
* Args:
* 	ID: Client ID
* 
* Return:
* 	True if has domains or False if doesn't.
*/
function has_domains( $ID ){
	global $wpdb;
	$table = $wpdb->prefix.'domains';

	$query = "SELECT COUNT(*) FROM $table WHERE client_id = %d;";
	$result = $wpdb->get_var( $wpdb->prepare($query, $ID) );

	if(  $result > 0 )
		return TRUE;
	else
		return FALSE;
}

?>