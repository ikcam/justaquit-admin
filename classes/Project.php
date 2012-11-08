<?php
Class Project{
	private $ID;
	private $name;
	private $url;
	private $location;
	private $domain_id;

	public function __construct($name, $url, $location, $domain_id){
		$this->name      = $name;
		$this->url       = $url;
		$this->location  = $location;
		$this->domain_id = $domain_id;
	}

	public function add_project(){
		global $wpdb;
		$table = $wpdb->prefix.'projects';

		if( $this->exists() ):
			return FALSE;
		else:
			$data = array(
					'name'      => $this->name,
					'url'       => $this->url,
					'location'  => $this->location,
					'domain_id' => $this->domain_id
				);
			$format = array('%s', '%s', '%s', '%d');
			$wpdb->insert($table, $data, $format);

			$this->ID = $wpdb->insert_id;

			return TRUE;
		endif;
	}

	public function update_project($ID){
		global $wpdb;
		$table = $wpdb->prefix.'projects';

		if( $this->exists() ):
			return FALSE;
		else:
			$data = array(
					'name'      => $this->name,
					'url'       => $this->url,
					'location'  => $this->location,
					'domain_id' => $this->domain_id
				);
			$where = array('ID' => $ID);
			$format = array('%s', '%s', '%s', '%d');

			$wpdb->update($table, $data, $where, $format);
			return TRUE;
		endif;
	}

	public static function delete_project($ID){
		global $wpdb;
		$table = $wpdb->prefix.'projects';

		if( Project::exists($ID) ):
			$query = "DELETE FROM $table WHERE ID = %d;";
			$wpdb->query( $wpdb->prepare($query, $ID) );

			return TRUE;
		else:
			return FALSE;
		endif;
	}

	public function exists($ID=NULL){
		global $wpdb;
		$table = $wpdb->prefix.'projects';

		if( $ID == NULL ):
			$query = "SELECT COUNT(*) FROM $table WHERE location = %s;";
			$count = $wpdb->get_var( $wpdb->prepare($query, $this->location) );
		else:
			$query = "SELECT COUNT(*) FROM $table WHERE ID = %d;";
			$count = $wpdb->get_var( $wpdb->prepare($query, $ID) );
		endif;

		if($count > 0)
			return TRUE;
		else
			return FALSE;
	}
}
?>