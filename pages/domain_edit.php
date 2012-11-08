<?php
function jadmin_page_domain_edit(){
	$settings = get_option( 'jadmin_settings' );

	$ID = $_GET['ID'];
	$domain =  get_domain( $ID );

	if( $domain != NULL ):
		if( $_POST['submit'] ) :
			$client_id  = $_POST['client_id'];
			$title      = $_POST['title'];
			$url        = '';
			$linode_did = 0;
			$priority   = $_POST['priority'];
			$wordpress  = 0;
			$author     = 0;

			$update = new Domain( $client_id, $title, $url, $linode_did, $priority, $wordpress, $author );
			$update->update_domain_info( $ID );
?>
		<div id="message" class="updated">
			<p>Domain edited.</p>
		</div>
<?php
		endif;
	else:
?>
		<div id="message" class="error">
			<p>The domain that you are looking for doesn't exists</p>
		</div>
<?php
		return;
	endif;
?>
<div class="wrap">
	<div id="icon-edit-pages" class="icon32"><br></div>
	<h2>
		Edit Domain
	</h2>
<?php $domain =  get_domain( $ID ); ?>
	<form action="" method="post">
		<table class="form-table">
		<tody>
			<tr valign="top">
				<th scope="row">Client Name</th>
				<td>
					<select name="client_id">
					<?php
						$clients = get_clients();
						foreach( $clients as $client ):
					?>
						<option value="<?php echo $client->ID ?>" <?php if( $client->ID == $domain->client_id ) echo 'selected'; ?>><?php echo $client->first_name.' '.$client->last_name ?></option>
					<?php
						endforeach;
					?>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Project Title</th>
				<td>
					<input type="text" name="title" placeholder="Name" value="<?php echo $domain->title ?>" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">URL</th>
				<td>
					<input type="text" readonly name="url" placeholder="Domain or Subdomain" value="<?php echo $domain->url ?>" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Priority</th>
				<td>
					<select name="priority">
						<option value="1" <?php if( $domain->priorirty == 1 ) echo 'selected'; ?>>Low</option>
						<option value="2" <?php if( $domain->priorirty == 1 ) echo 'selected'; ?>>Normal</option>
						<option value="3" <?php if( $domain->priorirty == 1 ) echo 'selected'; ?>>High</option>
					</select>
				</td>
			</tr>
		</tody>
		</table>
		<p class="submit"><input type="submit" name="submit" value="Edit Domain" class="button-primary" /></p>
	</form>
</div>
<?php
}
?>