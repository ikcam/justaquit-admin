jQuery(function($){
	$('#tablePrefix').keyup(function(){
		var value = $(this).attr('value');
		value = value + '_';
		$(this).attr('value', value);
	});

	$('#userPrefix').keyup(function(){
		var valie = $(this).attr('value');
		value = value + '_';
		$(this).attr('value', value);
	})
});