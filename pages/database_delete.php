<?php
function jadmin_page_database_delete(){
	$ID = $_GET['ID'];

	$database = get_database( $ID );

	if( $database==NULL ) :
?>
<div id="message" class="error">
	<p>The database that you are looking for doesn't exists.</p>
</div>
<?php
	else:
		$delete = new Database( 'delete_database', $database->ID );
		$delete->delete_database( $database->ID );
?>
<div id="message" class="updated">
	<p>The database has been deleted succesfuly.</p>
</div>
<?php
	endif;
}
?>
