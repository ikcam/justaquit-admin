<?php
function jadmin_page_project_delete(){
	$ID = $_GET['ID'];

	$project = get_project( $ID );

	if( !is_object($project) ) :
?>
		<div id="message" class="error">
			<p>The project that you are looking for doesn't exists</p>
		</div>
<?php
	else:
		Project::delete_project($ID);
?>
		<div id="message" class="updated">
			<p>The project has been deleted succesfuly.</p>
		</div>
<?php
	endif;
}
?>
