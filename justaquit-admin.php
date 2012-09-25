<?php
/*
Plugin Name: JustAquit Admin
Plugin URI: http://justaquit.net
Description: Controls JustAquit Clients
Version: v1.0
Author: Irving Kcam
Author URI: http://ikcam.com
License: GPL2
*/
?>
<?php
// Settings
$settings = get_option( 'jadmin_settings' );
// Classes
require_once(plugin_dir_path(__FILE__).'classes/Client.php');
require_once(plugin_dir_path(__FILE__).'classes/Database.php');
require_once(plugin_dir_path(__FILE__).'classes/Domain.php');
// Funcions
require_once(plugin_dir_path(__FILE__).'jadmin_functions.php');
// Pages
require_once(plugin_dir_path(__FILE__).'pages/main.php');
// Clients
require_once(plugin_dir_path(__FILE__).'pages/clients.php');
require_once(plugin_dir_path(__FILE__).'pages/client_add.php');
require_once(plugin_dir_path(__FILE__).'pages/client_edit.php');
require_once(plugin_dir_path(__FILE__).'pages/client_delete.php');
// Domains
require_once(plugin_dir_path(__FILE__).'pages/domains.php');
require_once(plugin_dir_path(__FILE__).'pages/domain_add.php');
//require_once(plugin_dir_path(__FILE__).'pages/domain_delete.php');
// Databases
require_once(plugin_dir_path(__FILE__).'pages/databases.php');
// Settings
require_once(plugin_dir_path(__FILE__).'pages/settings.php');

class JAdmin {
	public function install(){
		global $wpdb;
		
		$table = $wpdb->prefix.	"clients";
		$sql = "CREATE TABLE $table (
			ID mediumint(9) NOT NULL AUTO_INCREMENT,
			first_name varchar(100) NOT NULL,
			last_name varchar(100) NOT NULL,
			email varchar(100) NOT NULL,
			address varchar(150),
			phone varchar(20),
			registration_date bigint(12) NOT NULL,
			author mediumint(9) DEFAULT 0 NOT NULL,
			editor mediumint(9) DEFAULT 0 NOT NULL,
			UNIQUE KEY ID (ID)
		);";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);

		$table = $wpdb->prefix."domains";
		$sql = "CREATE TABLE $table (
			ID mediumint(9) NOT NULL AUTO_INCREMENT,
			title text NOT NULL,
			url varchar(55) NOT NULL,
			priority mediumint(9) DEFAULT 2 NOT NULL,
			client_id mediumint(9) NOT NULL,
			author mediumint(9) NOT NULL,
			linode_did mediumint(9) NOT NULL,
			linode_rid mediumint(9) NOT NULL,
			wordpress tinyint(1) NOT NULL,
			creation_date bigint(12) NOT NULL,
			UNIQUE KEY ID (ID)
		);";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);

		$table = $wpdb->prefix."databases";
		$sql = "CREATE TABLE $table(
			ID mediumint(9) NOT NULL AUTO_INCREMENT,
			name varchar(30) NOT NULL,
			user varchar(16) NOT NULL,
			password varchar(55) NOT NULL,
			domain_id mediumint(9) NOT NULL,
			UNIQUE KEY ID (ID)
		);";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

	public function settings_register(){
		register_setting( 'jadmin', 'jadmin_settings', array('JAdmin', 'settings_callback') );
	}

	public function settings_callback( $input ){
		if( $input['linode_key'] == NULL )
			$input['linode_key'] = '';

		if( $input['linode_did'] == NULL )
			$input['linode_did'] = '';

		if( $input['server_ip'] == NULL )
			$input['server_ip'] = '0.0.0.0';

		if( $input['server_folder'] == NULL )
			$input['server_folder'] = '/home/';

		if( $input['server_user'] == NULL )
			$input['server_user'] = 'root';

		if( $input['server_apache'] == NULL )
			$input['server_apache'] = '/etc/apache2/sites-available/';

		if( $input['database_prefix'] == NULL )
			$input['database_prefix'] = 'client_';

		return $input;
	}

	public function init(){
		add_menu_page( 'JustAquit Admin', 'JustAquit Admin', 'administrator', 'jadmin', 'jadmin_page_main', '' );
		add_submenu_page( 'jadmin', 'Clients', 'Clients', 'administrator', 'jadmin_clients', 'jadmin_page_clients' );
		add_submenu_page( 'jadmin', 'Domains', 'Domains', 'administrator', 'jadmin_domains', 'jadmin_page_domains' );
		add_submenu_page( 'jadmin', 'Databases', 'Databases', 'administrator', 'jadmin_databases', 'jadmin_page_databases' );
		add_submenu_page( 'jadmin', 'Settings', 'Settings', 'administrator', 'jadmin_settings', 'jadmin_page_settings' );
	}
}

add_action('admin_menu', array('JAdmin', 'init') );
add_action('admin_init', array('JAdmin', 'settings_register') );
register_activation_hook( __FILE__, array('JAdmin', 'install') );
?>