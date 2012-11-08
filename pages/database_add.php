<?php
function jadmin_page_database_add(){
	if( isset($_POST['submit']) && $_POST['submit'] ):
		$name = $_POST['name'];
		$user = $_POST['user'];
		$password= $_POST['password'];
		$repassword = $_POST['repassword'];

		if( $password != $repassword ) :
?>
<div id="message" class="error">
	<p>The passwords that you enter didn't match</p>
</div>
<?php
		else:
			$database = new DBCustom( $name, $user, $password );
			$database->add_database();
?>
<div id="message" class="updated">
	<p>New database created. <a href="?page=jadmin_databases&amp;action=edit&amp;ID=<?php echo $database->get_ID() ?>">Edit Database</a></p>
</div>
<?php
		endif;
	endif;
?>
<div class="wrap">
<div id="icon-edit-pages" class="icon32"><br></div>
<h2>Add New Database</h2>

<form action="" method="post">
	<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row">Name</th>
			<td>
				<input type="text" name="name" value="<?php echo $_POST['name'] ?>" placeholder="Database Name" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">User</th>
			<td>
				<input type="text" name="user" value="<?php echo $_POST['user'] ?>" placeholder="Database User" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">Password</th>
			<td>
				<input type="text" name="password" placeholder="Password" />
				<input type="text" name="repassword" placeholder="Repeat Password" />
			</td>
		</tr>
	</tbody>
	</table>
	<p class="submit"><input type="submit" name="submit" value="Add New Database" class="button-primary" /></p>
</form>

<?php
}
?>