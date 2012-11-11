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

function get_clients(){
	global $wpdb;
	$table = $wpdb->prefix.'clients';

	$query = "SELECT * FROM $table ORDER BY ID DESC";

	$clients = $wpdb->get_results( $wpdb->prepare($query) );

	return $clients;
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

function get_domains(){
	global $wpdb;
	$table = $wpdb->prefix.'domains';

	$query = "SELECT * FROM $table ORDER BY ID DESC";

	$domains = $wpdb->get_results( $wpdb->prepare($query) );

	return $domains;
}

function get_linode_domains(){
	$settings = get_option( 'jadmin_settings' );

	if( $settings['linode_key'] != '' ):
		require_once('Services/Linode.php');
		try {
			$linode = new Services_Linode($settings['linode_key']);
			$linode = $linode->domain_list();
			$domains = $linode['DATA'];
		} catch (Services_Linode_Exception $e) {
			echo $e->getMessage();
		}
		return $domains;
	endif;
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

function get_databases(){
	global $wpdb;
	$table = $wpdb->prefix.'databases';

	$query = "SELECT * FROM $table ORDER BY ID DESC";

	$databases = $wpdb->get_results( $wpdb->prepare($query) );

	return $databases;	
}

function get_database_by_domain( $ID ){
	global $wpdb;
	$table = $wpdb->prefix.'databases';

	$query = "SELECT * FROM $table WHERE domain_id = %d;";
	$database = $wpdb->get_row( $wpdb->prepare($query, $ID) );

	return $database;
}

function get_projects(){
	global $wpdb;
	$table = $wpdb->prefix.'projects';

	$query = "SELECT * FROM $table ORDER BY ID DESC";

	$projects = $wpdb->get_results( $wpdb->prepare($query) );

	return $projects;
}

function get_project($ID){
	global $wpdb;
	$table  = $wpdb->prefix.'projects';

	$query  = "SELECT * FROM $table WHERE ID = %d;";
	$project = $wpdb->get_row( $wpdb->prepare($query, $ID) );

	return $project;
}

function get_projects_by_url($url){
	global $wpdb;
	$table = $wpdb->prefix.'projects';

	$query = "SELECT * FROM $table WHERE url = %s;";
	$projects = $wpdb->get_results( $wpdb->prepare($query, $url) );

	return $projects;
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
	$settings = get_option( 'jadmin_settings' );
	
	require_once('Services/Linode.php');
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

function update_plugin(){
	$exec = 'cd '.BASEPATH.'&& git pull';
	return shell_exec( $exec );
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
	return shell_exec('/etc/init.d/apache2 reload');
}
?>