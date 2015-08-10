/**
 * PM module
 *
 * @package PM
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

$( document ).on( "mouseenter", ".pm-star", function() {
	if (!$(this).hasClass('pm-star-readonly'))
	{
		$(this).addClass('pm-star-hover');
		if ($(this).hasClass('pm-star-on'))
		{
			$(this).addClass('pm-star-off').removeClass('pm-star-on');
		}
	}
});

$( document ).on( "mouseleave", ".pm-star", function() {
    if (!$(this).hasClass('pm-star-readonly'))
    {
        $(this).removeClass('pm-star-hover');
        if ($(this).hasClass('pm-star-off'))
        {
            $(this).addClass('pm-star-on').removeClass('pm-star-off');
        }
    }
});

$( document ).on( "click", ".pm-star", function(e) {
    if (!$(this).hasClass('pm-star-readonly') && ajaxEnabled) {
        e.preventDefault();
        var txt = $(this).children('a').attr('href');
        ajaxSend({
            url: txt,
            divId: 'pagePreview'
        });
        $(this).toggleClass('pm-star-off');
        $(this).children('a').attr('title', '');

        return(false);
    }
});

