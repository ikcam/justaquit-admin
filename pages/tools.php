<?php
function jadmin_page_tools(){
	if( $_POST['update'] ):
?>
<div id="message" class="updated">
	<p><?php echo update_plugin() ?></p>
</div>
<?php
	endif;
?>
<div class="wrap">
	<h2>Tools</h2>
	<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row">Update Plugin</th>
		</tr>
		<td>
			<form method="post" action="">
				<p class="submit"><input type="submit" name="submit" value="Add New Client" class="button-primary" /></p>
			</form>
		</td>
	</tbody>
	</table>
</div>
<?php	
}
?>