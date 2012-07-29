$(function(){
	$('#domainName').keydown(function(){
		var value = $this.attr('value');
		$('#domainUser').attr( 'value', value );
	})
})