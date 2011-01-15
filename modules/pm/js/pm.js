$('.pm-star').live('mouseenter', function () {
	if (!$(this).hasClass('pm-star-readonly'))
	{
		$(this).addClass('pm-star-hover');
		if ($(this).hasClass('pm-star-on'))
		{
			$(this).addClass('pm-star-off').removeClass('pm-star-on');
		}
	}
});

$('.pm-star').live('mouseleave', function () {
	if (!$(this).hasClass('pm-star-readonly'))
	{
		$(this).removeClass('pm-star-hover');
		if ($(this).hasClass('pm-star-off'))
		{
			$(this).addClass('pm-star-on').removeClass('pm-star-off');
		}
	}
});

$('.pm-star').live('click', function () {
	if (!$(this).hasClass('pm-star-readonly') || !ajaxEnabled)
	{
		var txt = $(this).children('a').attr('href');
		ajaxSend({
			url: txt,
			divId: 'pagePreview'
		});
		$(this).toggleClass('pm-star-off');
		$(this).children('a').attr('title', '')
		return(false);
	}
});
	
