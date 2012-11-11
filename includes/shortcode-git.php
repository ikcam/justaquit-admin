<?php
Class shortcode_git {
	public function __construct(){
		add_shortcode('jadmin_git', array($this, 'shortcode') );
	}

	public function shortcode(){
		global $wpdb;
		$table = $wpdb->prefix.'log';
		$data = array(
			'text' => $_POST['payload'],
			);
		$format = array('%s');
		$wpdb->insert($table, $data, $format);

		if( isset($_POST['payload']) ):
			$push = json_decode($_POST['payload'], true);
			$url = $push['repository']['url'];

			$data = array(
			'text' => $url,
			);
			$format = array('%s');
			$wpdb->insert($table, $data, $format);

			$projects = get_projects_by_url($url);

			foreach( $projects as $project ):
				update_project($project->ID);
			endforeach;
		endif;
	}
}
?>