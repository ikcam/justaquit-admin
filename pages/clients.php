<?php
function jadmin_page_clients(){
	if( isset($_GET['action']) && $_GET['action'] == 'add' ):
		jadmin_page_client_add();
	elseif( isset($_GET['action']) && $_GET['action'] == 'edit' ):
		jadmin_page_client_edit();
	else:
		if( isset($_GET['action']) && $_GET['action'] == 'delete' ):
			jadmin_page_client_delete();
		endif;
?>
<div class="wrap">
	<div id="icon-users" class="icon32"><br></div>
	<h2>
		Clients
		<a href="?page=jadmin_clients&amp;action=add" class="add-new-h2">Add New</a>
	</h2>

	<table class="wp-list-table widefat fixed clients" cellspacing="0">
		<thead>
			<tr>
				<th scope="col" id="name" class="manage-column column-name"><span>Name</span></th>
				<th scope="col" id="email" class="manage-column column-email"><span>Email</span></th>
				<th scope="col" id="phone" class="manage-column column-phone"><span>Phone</span></th>
				<th scope="col" id="author" class="manage-column column-author"><span>Author</span></th>
				<th scope="col" id="editor" class="manage-column column-editor"><span>Last Edit</span></th>
				<th scope="col" id="count" class="manage-column column-count"><span>Domain Count</span></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th scope="col" id="name" class="manage-column column-name"><span>Name</span></th>
				<th scope="col" id="email" class="manage-column column-email"><span>Email</span></th>
				<th scope="col" id="phone" class="manage-column column-phone"><span>Phone</span></th>
				<th scope="col" id="author" class="manage-column column-author"><span>Author</span></th>
				<th scope="col" id="editor" class="manage-column column-editor"><span>Last Edit</span></th>
				<th scope="col" id="count" class="manage-column column-count"><span>Domain Count</span></th>
			</tr>
		</tfoot>
		<tbody id="the-list">
		<?php
			$clients = get_clients();
 			if( is_array($clients) ):
				foreach( $clients as $client ) :
		?>
			<tr id="client-<?php echo $client->ID ?>" valign="middle" class="alternate">
				<td class="column-name">
					<strong><?php echo $client->first_name.' '.$client->last_name ?></strong>
					<div class="row-actions">
						<span class="edit"><a href="?page=jadmin_clients&amp;action=edit&amp;ID=<?php echo $client->ID ?>">Edit</a> |</span>
						<span class="delete"><a class="submitdelete" href="?page=jadmin_clients&amp;action=delete&amp;ID=<?php echo $client->ID ?>" onclick="if ( confirm( 'You are about to delete client \'<?php echo $client->first_name.' '.$client->last_name ?>\'\n \'Cancel\' to return, \'Accept\' to erase.' ) ) { return true;}return false;">Delete</a></span>
					</div>
				</td>
				<td class="column-email"><?php echo $client->email ?></td>
				<td class="column-phone"><?php echo $client->phone ?></td>
				<td class="column-author">
				<?php
					$user = get_userdata( $client->author );
					echo $user->display_name;
				?>
				</td>
				<td class="column-editor">
				<?php
					$user = get_userdata( $client->editor );
					echo $user->display_name;
				?>
				</td>
				<td class="column-count"><?php echo get_domains_count( $client->ID ) ?></td>
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