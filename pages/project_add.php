<?php
function jadmin_page_project_add(){
	$settings = get_option( 'jadmin_settings' );

	if( isset($_POST['submit']) && $_POST['submit'] ):
		$name          = $_POST['name'];
		$url           = $_POST['url'];
		$location_base = $_POST['location_base'];
		$location      = $_POST['location'];
		$domain_id     = $_POST['domain_id'];

		// Check location path (Avoid starting slash)
		if( substr($location, 0, 1) == '/' )
			$location = substr($location, 1, strlen($location));

		// Setup full path
		$location = $location_base + $location;

		$project = new Project( $name, $url, $location, $domain_id );
		$project->add_project();
?>
		<div id="message" class="updated">
			<p>New project added. <a href="?page=jadmin_projects&amp;action=edit&amp;ID=<?php echo $project->ID ?>">Edit project</a>.</p>
		</div>
<?php
	endif;
?>
<div class="wrap">
	<div id="icon-page" class="icon32"><br></div>
	<h2>Add New Project</h2>
	<form method="post" action="">
		<input type="hidden" id="server_folder_base" value="<?php echo $settings['server_folder'] ?>" /> 
		<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row"><label for="domain_id">Linked Domain:</label></th>
				<td>
					<select name="domain_id" id="domain_id">
						<option value="0" rel="">None</option>
					<?php 
						$domains = get_domains();
						foreach($domains as $domain):
					?>
						<option value="<?php echo $domain->ID ?>" rel="<?php echo $domain->url ?>"><?php echo $domain->url ?></option>
					<?php
						endforeach;
					?>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="name">Name:</label></th>
				<td>
					<input type="text" name="name" id="name" placeholder="Name" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="url">Source URL:</label></th>
				<td>
					<input type="text" name="url" id="url" placeholder="Source URL" />
					<span class="description">From <a href="//github.com/" target="_blank">GitHub</a> or <a href="//bitbucket.org/" target="_blank">BitBucket</a></span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="location">Server Location:</label></th>
				<td>
					<input type="text" size="40" readonly="readonly" name="location_base" id="location_base" value="<?php echo $settings['server_folder'] ?>" />
					<input type="text" name="location" id="location" placeholder="Server Location" />
				</td>
			</tr>
		</tbody>
		</table>
		<p class="submit"><input type="submit" name="submit" value="Add New Project" class="button-primary" /></p>
	</form>
</div>
<?php
}
?>
