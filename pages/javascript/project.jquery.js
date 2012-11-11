jQuery(document).ready(function($){
	var base_name = $('#server_folder_base').attr('value');
	$('#domain_id').change(function(){
		var domain_name = $('option:selected', this).attr('rel');
		var full_name = base_name + domain_name + '/';
		$('#location_base').attr('value',full_name);
	});
});