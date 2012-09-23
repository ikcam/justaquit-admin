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
*	 get_domain( $ID )
*
* Args: 
* 	ID: Domain ID
*
* Return:
* 	Array with domain information.
*/
function get_domain( $ID ){
	global $wpdb;
	$table  = $wpdb->prefix.'domains';

	$query  = "SELECT * FROM $table WHERE ID = %d;";
	$domain = $wpdb->get_row( $wpdb->prepare($query, $ID) );

	return $domain;
}

/*
* Name:
*	 get_database( $ID )
*
* Args: 
* 	ID: Database ID
*
* Return:
* 	Array with database information.
*/
function get_database( $ID ){
	global $wpdb;
	$table  = $wpdb->prefix.'databases';

	$query  = "SELECT * FROM $table WHERE ID = %d;";
	$database = $wpdb->get_row( $wpdb->prepare($query, $ID) );

	return $database;
}

/*
* Name:
* 	has_domains( $ID )
*
* Args:
* 	ID: Client ID
* 
* Return:
* 	Boolean: True if has domains or False if doesn't.
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

/*
* Name:
* 	get_domains_count( $ID )
*
* Args:
* 	ID: Client ID
* 
* Return:
* 	Integer: Count of domains.
*/
function get_domains_count( $ID ){
	global $wpdb;
	$table = $wpdb->prefix.'domains';

	$query = "SELECT COUNT(*) FROM $table WHERE client_id = %d;";
	$result = $wpdb->get_var( $wpdb->prepare($query, $ID) );

	if(  $result == NULL )
		return 0;
	else
		return $result;
}

/*
* Name:
* 	get_linode_domain_name( $ID )
*
* Args:
* 	ID: Linode Domain ID
* 
* Return:
* 	String: Domain name from Linode.
*/
function get_linode_domain_name( $ID ){
	require('Services/Linode.php');
	try {
		$linode = new Services_Linode($settings['linode_key']);
		$linode = $linode->domain_list(
				array(
					'DomainID' => $ID
				)
			);
		$name = $linode['DATA'][0]['DOMAIN'];
	} catch (Services_Linode_Exception $e) {
		echo $e->getMessage();
	}

	return $name;
}

/*
* Name:
* 	get_data( $url )
*
* Args:
* 	url: URL you want to get data from.
* 
* Return:
* 	String: URL Data.
*/
function get_data( $url ){
	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}

function restart_apache(){
	$exec = plugin_dir_path(__FILE__).'exec.sh';
	shell_exec($exec);
}
?>