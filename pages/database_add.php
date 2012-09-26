<?php
function jadmin_page_database_add(){
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
				<input type="text" name="name" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">User</th>
			<td>
				<input type="text" name="user" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">Password</th>
			<td>
				<input type="text" name="password" />
			</td>
		</tr>
	</tbody>
	</table>
	<p class="submit"><input type="submit" name="submit" value="Add New Database" class="button-primary" /></p>
</form>

<?php
}
?>