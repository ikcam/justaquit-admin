<?php
function jadmin_page_projects(){
	if( isset($_GET['action']) && $_GET['action'] == 'add' ):
		jadmin_page_project_add();
	elseif( isset($_GET['action']) && $_GET['action'] == 'edit' ):
		jadmin_page_project_edit();
	else:
		if( isset($_GET['action']) && $_GET['action'] == 'delete' ):
			jadmin_page_project_delete();
		endif;
?>
<div class="wrap">
	<div id="icon-page" class="icon32"><br></div>
	<h2>
		Projects
		<a href="?page=jadmin_projects&amp;action=add" class="add-new-h2">Add New</a>
	</h2>

	<table class="wp-list-table widefat fixed clients" cellspacing="0">
		<thead>
			<tr>
				<th scope="col" id="name" class="manage-column column-name"><span>Name</span></th>
				<th scope="col" id="url" class="manage-column column-url"><span>Source URL</span></th>
				<th scope="col" id="location" class="manage-column column-location"><span>Server Location</span></th>
				<th scope="col" id="domain" class="manage-column column-domain"><span>Linked Domain</span></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th scope="col" id="name" class="manage-column column-name"><span>Name</span></th>
				<th scope="col" id="url" class="manage-column column-url"><span>Source URL</span></th>
				<th scope="col" id="location" class="manage-column column-location"><span>Server Location</span></th>
				<th scope="col" id="domain" class="manage-column column-domain"><span>Linked Domain</span></th>
			</tr>
		</tfoot>
		<tbody id="the-list">
		<?php
			$projects = get_projects();
 			if( is_array($projects) ):
				foreach( $projects as $project ) :
		?>
			<tr id="client-<?php echo $client->ID ?>" valign="middle" class="alternate">
				<td class="column-name">
					<strong><?php echo $project->name ?></strong>
					<div class="row-actions">
						<span class="edit"><a href="?page=jadmin_projects&amp;action=edit&amp;ID=<?php echo $project->ID ?>">Edit</a> |</span>
						<span class="delete"><a class="submitdelete" href="?page=jadmin_projects&amp;action=delete&amp;ID=<?php echo $project->ID ?>" onclick="if ( confirm( 'You are about to delete the project \'<?php echo $project->name ?>\'\n \'Cancel\' to return, \'Accept\' to erase.' ) ) { return true;}return false;">Delete</a></span>
					</div>
				</td>
				<td class="column-url"><a href="//<?php echo $project->url ?>" target="_blank"><?php echo $project->url ?></td>
				<td class="column-location"><?php echo $project->location ?></td>
				<td class="column-domain">
				<?php
					if($project->domain_id==0):
						echo 'None';
					else:
						$domain = get_domain( $project->domain_id );
						echo '<a href="//'.$domain->url.'" target="_blank">'.$domain->url.'</a>';
					endif;
				?>
				</td>
			</tr>
		<?php
				endforeach;
			endif;
		?>
		</tbody>
	</table>
</div>
<?php
	endif;
}
?>