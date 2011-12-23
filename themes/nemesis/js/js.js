$().ready(function() {

	$('.block').each(function(){
		$(this).children('h2').contents().unwrap().wrap('<div class="blockHeader"/>');
		$(this).children(':gt(0)').wrapAll('<div class="blockWrapper"/>')
	});
	
});