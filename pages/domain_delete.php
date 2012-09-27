<?php
function jadmin_page_domain_delete(){
	$ID = $_GET['ID'];

	$domain = get_domain( $ID );

	if( $domain == NULL ) :
?>
<div id="message" class="error">
	<p>The domain that you are looking for doesn't exists.</p>
</div>
<?php
	else:
		$delete = new Domain( $domain->client_id, $domain->title, $domain->url, $domain->linode_did, $domain->priority, $domain->wordpress, $domain->author );
		$delete->delete_domain( $domain->ID );
?>
<div id="message" class="updated">
	<p>The domain has been deleted succesfuly.</p>
</div>
<?php
	endif;
}
?>