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
		<form class="form-table">
			<p class="submit"><input type="submit" name="submit" value="Add New Client" class="button-primary" /></p>
		</form>
	</tbody>
	</table>
</div>
<?php	
}
?>