<?php
function jadmin_page_domain_add(){
	if( $_POST['submit'] ) :
		echo 'Text';
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
						foreach( $domains as $domain ):
					?>
							<option value="<?php echo $domain['DOMAINID'] ?>"><?php echo $domain['DOMAIN'] ?></option>
					<?php
						endforeach;
					?>
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