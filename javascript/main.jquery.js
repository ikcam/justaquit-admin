jQuery(function($){
	$('#tablePrefix').keyup(function(){
		var value = $(this).attr('value');
		value = value.replace(' ', '');
		value = value.replace('_', '');
		value = value + '_';
		$(this).attr('value', value);
	});

	$('#userPrefix').keyup(function(){
		var value = $(this).attr('value');
		value = value.replace(' ', '');
		value = value.replace('_', '');
		value = value + '_';
		$(this).attr('value', value);
	})
});