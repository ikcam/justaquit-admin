<?php
function jadmin_page_domain_migrate(){
	$ID = $_GET['ID'];

	if( isset($_POST['submit']) && $_POST['submit'] ):
		$url = $_POST['url'];
		$linode_did = $_POST['linode_did'];
		$domain = get_domain( $ID );

		if( $domain == NULL ):
?>
<div id="message" class="error">
	<p>The Domain that you are looking for doesn't exists.</p>
</div>
<?php
		else:
			$update = new Domain( $domain->client_id, $domain->title, $url, $linode_did, $domain->priority, $doimain->wordpress, $domain->author );
			$update->update_domain( $domain->ID );
		endif;

	endif;
?>
<div class="wrap">
	<div id="icon-edit-pages" class="icon32"><br></div>
	<h2>Migrate Domain</h2>

	<form action="" method="post">
		<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row">Old Domain</th>
				<td>
					<select name="domain_old">
					<?php
						$domains = get_domains();
						if( $domains != NULL ):
							foreach( $domains as $domain ):
					?>
						<option value="<?php echo $domain->ID ?>" <?php if( $ID == $domain->ID ) echo 'selected'; ?>><?php echo $domain->url ?></option>
					<?php
							endforeach;
						endif;
					?>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">New Domain</th>
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
		</tbody>
		</table>
		<p class="submit"><input type="submit" name="submit" value="Migrate Domain" class="button-primary" /></p>
	</form>
</div>
<?php
}
?>