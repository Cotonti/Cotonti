$('.star-rating').live('mouseenter', function () {
	if (!$(this).hasClass('star-rating-readonly'))
	{
		$(this).addClass('star-rating-hover');
		if ($(this).hasClass('star-rating-on'))
		{
			$(this).addClass('star-rating-off').removeClass('star-rating-on');
		}
	}
});

$('.star-rating').live('mouseleave',	function () {
	if (!$(this).hasClass('star-rating-readonly'))
	{
		$(this).removeClass('star-rating-hover');
		if ($(this).hasClass('star-rating-off'))
		{
			$(this).addClass('star-rating-on').removeClass('star-rating-off');
		}
	}
});

$('.star-rating').live('click', function () {
	if (!$(this).hasClass('star-rating-readonly') || !ajaxEnabled)
	{
		var txt = $(this).children('a').attr('href');
		ajaxSend({
			url: txt,
			divId: 'pagePreview'
		});
		$(this).toggleClass('star-rating-off');
		return(false);
	}
});
	
