<?php
function jadmin_page_project_edit(){
	$settings = get_option( 'jadmin_settings' );
	$ID = $_GET['ID'];

	if( isset($_POST['submit']) && $_POST['submit'] ):
		$name      = $_POST['name'];
		$url       = $_POST['url'];
		$location  = $_POST['location'];
		$domain_id = $_POST['domain_id'];

		$project = new Project( $name, $url, $location, $domain_id );
		$project->update_project($ID);
?>
		<div id="message" class="updated">
			<p>Project update successfully</p>
		</div>
<?php
	endif;

	$project = get_project($ID);
	if(!is_object($project)):
?>
	<div id="message" class="error">
			<p>The project that you are looking for doesn't exists</p>
		</div>
<?php
		return;
	endif;

?>
<div class="wrap">
	<div id="icon-page" class="icon32"><br></div>
	<h2>Edit Project</h2>
	<form method="post" action="">
		<input type="hidden" id="server_folder_base" value="<?php echo $settings['server_folder'] ?>" /> 
		<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row"><label for="domain_id">Linked Domain:</label></th>
				<td>
					<select name="domain_id" id="domain_id">
						<option value="0" <?php if($project->domain_id == 0){echo 'selected';} ?>>None</option>
					<?php 
						$domains = get_domains();
						foreach($domains as $domain):
					?>
						<option value="<?php echo $domain->ID ?>" <?php if($project->domain_id == $domain->ID){echo 'selected';} ?>><?php echo $domain->url ?></option>
					<?php
						endforeach;
					?>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="name">Name:</label></th>
				<td>
					<input type="text" name="name" id="name" placeholder="Name" value="<?php echo $project->name ?>" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="url">Source URL:</label></th>
				<td>
					<input type="text" name="url" id="url" placeholder="Source URL" value="<?php echo $project->url ?>" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="location">Server Location:</label></th>
				<td>
					<input type="text" name="location" id="location" placeholder="Server Location" value="<?php echo $project->location ?>" />
				</td>
			</tr>
		</tbody>
		</table>
		<p class="submit"><input type="submit" name="submit" value="Update Project" class="button-primary" /></p>
	</form>
</div>
<?php
}
?>
