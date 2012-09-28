<?php
function jadmin_page_main(){
?>
<div class="wrap">
	<div id="icon-index" class="icon32"><br></div>
	<h2>JustAquit Admin Center</h2>

	<table class="form-table">
		<h3>Requirements</h3>
		<p>Before go to settings run this commands on the bash of your Linode Server:</p>
		<ul style="padding-left:20px;list-style:disc;">
			<li><code>sudo pear install Net_URL2-0.3.1</code></li>
			<li><code>sudo pear install HTTP_Request2-0.5.2</code></li>
			<li><code>sudo pear channel-discover pear.keremdurmus.com</code></li>
			<li><code>sudo pear install krmdrms/Services_Linode</code></li>
		</ul>

		<h3>Firsts tasks:</h3>
		<ul style="padding-left:20px;list-style:disc;">
			<li>Go to settings and setup your Linode API Key.</li>
			<li>Your Linode IP number.</li>
			<li>Folder locations.</li>
			<li>User owner.</li>
			<li>Database prefix.</li>
		</ul>

		<h3>Comming soon:</h3>
		<ul style="padding-left:20px;list-style:disc;">
			<li>Use <code>wp_nonce_field</code> in forms.</li>
			<li>Add test mode.</li>
			<li>Use <a href="//name.com" target="_black">Name.com</a> API for domain management.</li>
			<li>More Linode functions.</li>
			<li>Add/manage clusters.</li>
		</ul>
		<p>&nbsp;</p>
		<p>
			Forked in <a href="https://github.com/ikcam/justaquit-admin" target="_blank">GitHub</a> |
			<a href="https://github.com/ikcam/justaquit-admin/issues" target="_blank">Report an issue</a>
		</p>
		<p>
			Creator: <a href="//ikcam.com" target="_blank">Irving Kcam</a> |
			Email: <a href="mailto:me@ikcam">me@ikcam.com</a> |
			License: GPL2
		</p>

	</table>
</div>
<?php
}
?>