<?php 
class justaquit {
	function settings_register(){

	}

	function page_main(){
?>
	<div class="wrap">
		<h2>JustAquit Admin Options</h2>
	</div>
<?php
	}

	function page_addclient(){
?>
	<div class="wrap">
		<h2>Add New Client</h2>
	</div>
<?php		
	}
}

function justaquit_init(){
	add_menu_page( 'JustAquit', 'JustAquit', 'administrator', 'aquit', array('justaquit', 'page_main'), '', 59 );
	add_submenu_page( 'aquit', 'Add Client', 'Add Client', 'administrator', 'aquit_addclient', array( 'justaquit', 'page_addclient' ) );
	add_action('admin_init', array('justaquit', 'settings_register'));
}
add_action('admin_menu', 'justaquit_init');
?>