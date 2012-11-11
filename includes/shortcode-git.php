<?php
Class shortcode_git {
	public function __construct(){
		add_shortcode('jadmin_git', array($this, 'shortcode') );
	}

	public function shortcode(){
		if( isset($_POST['payload']) ):
			$push = json_decode(stripslashes($_POST['payload']), true);
			$url = $push['repository']['url'];
			if( $url = '' )
				$url = $push['repository']['website'];

			$projects = get_projects_by_url($url);

			if( $projects ):
				foreach( $projects as $project ):
					update_git($project->ID);
				endforeach;
			endif;
		endif;
	}
}
?>