<?php
function jadmin_page_tools(){
	if( $_POST['update_plugin'] ):
?>
<div id="message" class="updated">
	<p><?php echo update_plugin() ?></p>
</div>
<?php
	elseif( $_POST['restart_apache'] ):
?>
<div id="message" class="updated">
	<p><?php echo restart_apache() ?></p>
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
			<th scope="row">Plugin</th>
			<td>
				<form method="post" action="">
					<input type="submit" name="update_plugin" value="Update" class="button-primary" />
				</form>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">Apache</th>
			<td>
				<form method="post" action="">
					<input type="submit" name="restart_apache" value="Restart" class="button-primary" />
				</form>
			</td>
		</tr>
	</tbody>
	</table>
</div>
<?php	
}
?>