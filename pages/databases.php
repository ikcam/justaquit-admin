<?php
function jadmin_page_databases(){
	if( $_GET['action'] == 'add' ):
		jadmin_page_database_add();
	elseif( $_GET['action'] == 'edit' ):
		jadmin_page_database_edit();
	else:
		if( $_GET['action'] == 'delete' ):
			jadmin_page_database_delete();
		endif;
?>
<div class="wrap">
	<div id="icon-edit-pages" class="icon32"><br></div>
	<h2>
		Databases
		<a href="?page=jadmin_databases&amp;action=add" class="add-new-h2">Add New</a>
	</h2>

	<table class="wp-list-table widefat fixed clients" cellspacing="0">
		<thead>
			<tr>
				<th scope="col" id="name" class="manage-column column-name"><span>Name</span></th>
				<th scope="col" id="user" class="manage-column column-user"><span>User</span></th>
				<th scope="col" id="password" class="manage-column column-password"><span>Password</span></th>
				<th scope="col" id="domain" class="manage-column column-domain"><span>Linked Domain</span></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th scope="col" id="name" class="manage-column column-name"><span>Name</span></th>
				<th scope="col" id="user" class="manage-column column-user"><span>User</span></th>
				<th scope="col" id="password" class="manage-column column-password"><span>Password</span></th>
				<th scope="col" id="domain" class="manage-column column-domain"><span>Linked Domain</span></th>
			</tr>
		</tfoot>
		<tbody id="the-list">
		<?php
			$databases = get_databases();
 			if( is_array($databases) ):
				foreach( $databases as $database ) :
		?>
			<tr id="client-<?php echo $database->ID ?>" valign="middle" class="alternate">
				<td class="column-name">
					<strong><?php echo $database->name ?></strong>
					<div class="row-actions">
						<span class="edit"><a href="?page=jadmin_databases&amp;action=edit&amp;ID=<?php echo $database->ID ?>">Edit</a> |</span>
						<span class="delete"><a class="submitdelete" href="?page=jadmin_databases&amp;action=delete&amp;ID=<?php echo $database->ID ?>" onclick="if ( confirm( 'You are about to delete client \'<?php echo $database->name ?>\'\n \'Cancel\' to return, \'Accept\' to erase.' ) ) { return true;}return false;">Delete</a></span>
					</div>
				</td>
				<td class="column-user"><?php echo $database->user ?></td>
				<td class="column-password"><?php echo $database->password ?></td>
				<td class="column-password">
				<?php
					$domain = get_domain( $database->domain_id );
					if( $domain_id == 0 )
						echo 'None';
					else
						echo '<a href="http://'.$domain->url.'" target="_blank">'.$domain->url.'</a>;
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