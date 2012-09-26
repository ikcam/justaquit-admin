<?php
function jadmin_page_database_edit(){
	$ID = $_GET['ID'];
	$database = get_database( $ID );
	if( is_array($database) ):
?>
<div class="wrap">
<div id="icon-edit-pages" class="icon32"><br></div>
<h2>Edit Database</h2>

<form action="" method="post">
	<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row">Name</th>
			<td>
				<input type="text" name="name" value="<?php echo $database->name ?>" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">User</th>
			<td>
				<input type="text" name="user" value="<?php echo $database->user ?>" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">Password</th>
			<td>
				<input type="text" name="password" value="<?php echo  $database->password ?>" />
			</td>
		</tr>
	</tbody>
	</table>
	<p class="submit"><input type="submit" name="submit" value="Edit Database" class="button-primary" /></p>
</form>

<?php
	else:
?>
<div id="message" class="error">
	<p>The detabase that you are looking for doesn't exists.</p>
</div>
<?php
	endif;
}
?>