<?php
function jadmin_page_domain_add(){
	global $settings;

	if( $_POST['submit'] ) :
?>
<div id="message" class="updated">
<?php
		$client_id  = $_POST['client_id'];
		$title      = $_POST['title'];
		$url        = $_POST['url'];
		$linode_did = $_POST['linode_did'];
		$priority   = $_POST['priority'];
		if( $_POST['wordpress'] == TRUE ):
			$wordpress = 1;
		else:
			$wordpress = 0;
		endif;
		$author     = get_current_user_id();

		$domain = new Domain( $client_id, $title, $url, $linode_did, $priority, $wordpress, $author );
		$domain->add_domain();
?>
	<p>New Domain created.</p>
</div>
<?php
	endif;
?>
<div class="wrap">
	<div id="icon-edit-pages" class="icon32"><br></div>
	<h2>
		Add New Domain
	</h2>

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
						<option value="<?php echo $client->ID ?>"><?php echo $client->first_name.' '.$client->last_name ?></option>
					<?php
						endforeach;
					?>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Project Title</th>
				<td>
					<input type="text" name="title" placeholder="Name" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">URL</th>
				<td>
					<input type="text" name="url" placeholder="Domain or Subdomain" />
					<select name="linode_did">
						<option value="0">Custom Domain</option>
					<?php
						$domains = get_linode_domains();
						if( $domains ):
							foreach( $domains as $domain ):
					?>
							<option value="<?php echo $domain['DOMAINID'] ?>" <?php if( $domain['DOMAINID'] == $settings['linode_main'] ) echo 'selected' ?>><?php echo $domain['DOMAIN'] ?></option>
					<?php
							endforeach;
						endif;
					?>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Priority</th>
				<td>
					<select name="priority">
						<option value="1">Low</option>
						<option value="2" selected>Normal</option>
						<option value="3">High</option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">Install WordPress?</th>
				<td>
					<input type="checkbox" name="wordpress" checked="checked" />
				</td>
			</tr>
		</tody>
		</table>
		<p class="submit"><input type="submit" name="submit" value="Add New Domain" class="button-primary" /></p>
	</form>
</div>
<?php
}
?>