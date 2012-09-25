<?php
function jadmin_page_domains(){
	if( $_GET['action'] == 'add' ):
		jadmin_page_domain_add();
	else:
		if( $_GET['action'] == 'delete' ):
			jadmin_page_domain_delete();
		endif;
?>
<div class="wrap">
	<div id="icon-edit-pages" class="icon32"><br></div>
	<h2>
		Domains
		<a href="?page=jadmin_domains&amp;action=add" class="add-new-h2">Add New</a>
	</h2>

	<table class="wp-list-table widefat fixed domains" cellspacing="0">
		<thead>
			<tr>
				<th scope="col" id="title" class="manage-column column-title"><span>Title</span></th>
				<th scope="col" id="url" class="manage-column column-url"><span>URL</span></th>
				<th scope="col" id="priority" class="manage-column column-priority"><span>Priority</span></th>
				<th scope="col" id="client" class="manage-column column-client"><span>Client</span></th>
				<th scope="col" id="author" class="manage-column column-author"><span>Author</span></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th scope="col" id="title" class="manage-column column-title"><span>Title</span></th>
				<th scope="col" id="url" class="manage-column column-url"><span>URL</span></th>
				<th scope="col" id="priority" class="manage-column column-priority"><span>Priority</span></th>
				<th scope="col" id="client" class="manage-column column-client"><span>Client</span></th>
				<th scope="col" id="author" class="manage-column column-author"><span>Author</span></th>
			</tr>
		</tfoot>
		<tbody id="the-list">
		<?php
			$domains = get_domains();
 			if( is_array($domains) ):
				foreach( $domains as $domain ) :
		?>
			<tr id="client-<?php echo $domain->ID ?>" valign="middle" class="alternate">
				<td class="column-title">
					<strong><?php echo $domain->title ?></strong>
					<div class="row-actions">
						<span class="edit"><a href="?page=jadmin_domains&amp;action=migrate&amp;ID=<?php echo $domain->ID ?>">Migrate</a> |</span>
						<span class="delete"><a class="submitdelete" href="?page=jadmin_domains&amp;action=delete&amp;ID=<?php echo $domain->ID ?>" onclick="if ( confirm( 'You are about to delete the domain \'<?php echo $domain->title ?>\'\n \'Cancel\' to return, \'Accept\' to erase.' ) ) { return true;}return false;">Delete</a></span>
					</div>
				</td>
				<td class="column-url"><a href="http://<?php echo $domain->url ?>" target="_blank">http://<?php echo $domain->url ?></a></td>
				<td class="column-priority"><?php echo $domain->priority ?></td>
				<td class="column-client">
				<?php
					$user = get_client( $domain->client_id );
					echo $user->first_name.' '.$user->last_name;
				?>
				</td>
				<td class="column-author">
				<?php
					$user = get_userdata( $domain->author );
					echo $user->display_name;
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