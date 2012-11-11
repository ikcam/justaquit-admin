<?php
Class shortcode_git {
	public function __construct(){
		add_shortcode('jadmin_git', array($this, 'shortcode') );
	}

	public function shortcode(){
		if( isset($_POST['payload']) ):
			$push = json_decode(stripslashes($_POST['payload']), true);
			$url  = $push['repository']['url'];
			// For BitBucket
			if( !$url ){
				$absolute_url = $push['repository']['absolute_url'];
				$absolute_url = substr( $absolute_url, 1, strlen($absolute_url) ).'.git';
				$owner        = $push['repository']['owner'];
				$url          = 'https://'.$owner.'@bitbucket.org'.$absolute_url;
			}

			global $wpdb;
			$table = $wpdb->prefix.'log';
			$data = array(
				'text' => $url
				);
			$format = array('%s');
			$wpdb->insert($table,$data,$format);
			
			if( $url != '' ){
				$projects = get_projects_by_url($url);

				if( $projects ):
					foreach( $projects as $project ):
						update_git($project->ID);
					endforeach;
				endif;
			}
		endif;
	}
}
?>