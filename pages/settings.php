<?php
function jadmin_page_settings(){
	global $settings;
?>
<div class="wrap">
	<div id="icon-options-general" class="icon32"><br></div><h2>Settings</h2>

	<form action="options.php" method="post">
	<?php settings_fields('jadmin'); ?>
		<h3>API Keys</h3>
		<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row">Linode API Key</th>
				<td>
					<textarea name="jadmin_settings[linode_key]" cols="50"><?php echo $settings['linode_key'] ?></textarea>
				</td>
			</tr>
		</tbody>
		</table>

		<h3>Server Settings</h3>
		<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row">IP Number</th>
				<td>
					<input type="text" name="jadmin_settings[server_ip]" value="<?php echo $settings['server_ip'] ?>" />
					<span class="description">Of the Linode Server.</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Apache Folder</th>
				<td>
					<input type="text" name="jadmin_settings[server_apache]" value="<?php echo $settings['server_apache'] ?>" />
					<span class="description">Location of the "sites-available" apache folder.</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Main Folder</th>
				<td>
					<input type="text" name="jadmin_settings[server_folder]" value="<?php echo $settings['server_folder'] ?>" />
					<span class="description">Location of the files.</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">User</th>
				<td>
					<input type="text" name="jadmin_settings[server_user]" value="<?php echo $settings['server_user'] ?>" />
					<span class="description">Owner of files</span>
				</td>
			</tr>
		</tbody>
		</table>

		<h3>Database Settings</h3>
		<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row">Prefix</th>
				<td>
					<input type="text" name="jadmin_settings[database_prefix]" value="<?php echo $settings['database_prefix'] ?>" />
				</td>
			</tr>
		</tbody>
		</table>
		<p class="submit"><input type="submit" value="<?php _e('Save Changes') ?>" class="button-primary" /></p>
	</form>
</div>
<?php
}
?>