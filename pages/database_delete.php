<?php
function jadmin_page_database_delete(){
	$ID = $_GET['ID'];

	$database = get_database( $ID );
	print_r($database);

	if( is_array($database) ) :
		$delete = new Database( 'delete_database', $database->ID );
		$delete->delete_database( $database->ID );
?>
<div id="message" class="updated">
	<p>The detabase has been deleted succesfuly.</p>
</div>
<?php
	else:
?>
<div id="message" class="error">
	<p>The database that you are looking for doesn't exists.</p>
</div>
<?php
	endif;
}
?>
