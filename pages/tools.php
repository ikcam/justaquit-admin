<?php
function jadmin_page_tools(){
	if( $_POST['tool_update'] ):
?>
<div id="message" class="updated">
	<p><?php echo update_plugin() ?></p>
</div>
<?php
	endif;
?>
<div class="wrap">
	<div id="icon-tools" class="icon32"><br></div>
	<h2>Tools</h2>
	<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row">Update Plugin</th>
			<td>
				<form method="post" action="">
					<input type="submit" name="tool_update" value="Update" class="button-primary" />
				</form>
			</td>
		</tr>
	</tbody>
	</table>
</div>
<?php	
}
?>