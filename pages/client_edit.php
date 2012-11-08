<?php
function jadmin_page_client_edit(){
	$ID = $_GET['ID'];

	if( isset($_POST['submit']) && $_POST['submit'] ):
		$first_name = $_POST['first_name'];
		$last_name  = $_POST['last_name'];
		$email      = $_POST['email'];
		$address    = $_POST['address'];
		$phone      = $_POST['phone'];
		$author     = $_POST['author'];
		$editor     = get_current_user_id();

		$client = new Client( $first_name, $last_name, $email, $address, $phone, $author, $editor );
		$client->update_client( $ID );
?>
<div id="message" class="updated">
	<p>Client updated succesfuly.</p>
</div>
<?php
	endif;

	$client = get_client( $ID );
	if( $client == NULL ):
?>
		<div id="message" class="error">
			<p>The client that you are looking for doesn't exists</p>
		</div>
<?php
		return;
	endif;

?>
<div class="wrap">
	<div id="icon-users" class="icon32"><br></div>
	<h2>Edit Client</h2>
	<form method="post" action="">
		<table class="form-table">
		<tbody>
			<input type="hidden" name="author" value="<?php echo $client->author ?>" />
			<tr valign="top">
				<th scope="row">Name:</th>
				<td>
					<input type="text" name="first_name" value="<?php echo $client->first_name ?>" placeholder="First Name" />
					<input type="text" name="last_name" value="<?php echo $client->last_name ?>" placeholder="Last Name" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Email</th>
				<td>
					<input type="text" name="email" value="<?php echo $client->email ?>" placeholder="Email" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Address</th>
				<td>
					<textarea type="text" name="address" cols="50"><?php echo $client->address ?></textarea>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Phone</th>
				<td>
					<input type="text" name="phone" value="<?php echo $client->phone ?>" placeholder="Phone" />
				</td>
			</tr>
		</tbody>
		</table>
		<p class="submit"><input type="submit" name="submit" value="Edit Client" class="button-primary" /></p>
	</form>
</div>
<?php
}
?>
