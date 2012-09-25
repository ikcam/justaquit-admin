<?php
function jadmin_page_client_delete(){
	$ID = $_GET['ID'];

	$client = get_client( $ID );

	if( $client == NULL ) :
?>
		<div id="message" class="error">
			<p>The client that you are looking for doesn't exists</p>
		</div>
<?php
	else:
		$first_name = $client->first_name;
		$last_name = $client->last_name;
		$email = $client->email;
		$address = $client->address;
		$phone = $client->phone;
		$author = get_current_user_id();
		$editor = $author;

		$delete = new Client( $first_name, $last_name, $email, $address, $phone, $author, $editor );
		$delete->delete_client( $client->ID );
?>
		<div id="message" class="updated">
			<p>The client deleted succesfuly.</p>
		</div>
<?php
	endif;
}
?>
