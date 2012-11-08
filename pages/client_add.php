<?php
function jadmin_page_client_add(){
	if( isset($_POST['submit']) && $_POST['submit'] ):
		$first_name = $_POST['first_name'];
		$last_name  = $_POST['last_name'];
		$email      = $_POST['email'];
		$address    = $_POST['address'];
		$phone      = $_POST['phone'];
		$author     = get_current_user_id();
		$editor     = $author;

		$client = new Client( $first_name, $last_name, $email, $address, $phone, $author, $editor );
		$client->add_client();
?>
		<div id="message" class="updated">
			<p>New client created. <a href="?page=jadmin_clients&amp;action=edit&amp;ID=<?php echo $client->get_ID() ?>">Edit client</a></p>
		</div>
<?php
	endif;
?>
<div class="wrap">
	<div id="icon-users" class="icon32"><br></div>
	<h2>Add New Client</h2>
	<form method="post" action="">
		<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row">Name:</th>
				<td>
					<input type="text" name="first_name" placeholder="First Name" />
					<input type="text" name="last_name" placeholder="Last Name" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Email</th>
				<td>
					<input type="text" name="email" placeholder="Email" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Address</th>
				<td>
					<textarea name="address" cols="50"></textarea>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Phone</th>
				<td>
					<input type="text" name="phone" placeholder="Phone" />
				</td>
			</tr>
		</tbody>
		</table>
		<p class="submit"><input type="submit" name="submit" value="Add New Client" class="button-primary" /></p>
	</form>
</div>
<?php
}
?>
