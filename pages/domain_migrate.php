<?php
function jadmin_page_domain_migrate(){
	$ID = $_GET['ID'];
?>
<div class="wrap">
	<div id="icon-edit-pages" class="icon32"><br></div>
	<h2>Migrate Domain</h2>

	<form action="" method="post">
		<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row">Base Domain</th>
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
		</tbody>
		</table>
	</form>
</div>
<?php
}
?>