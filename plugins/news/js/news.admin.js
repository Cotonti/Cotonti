var num = 1;

function changecats()
{
	var newstext = '';
	var unsetcats = '';
	num = $('#catgenerator .newscat').length;
	$('[name=maxpages]').val($('#catgenerator .newscat').first().find('.cac').val());

	$('#catgenerator .newscat').each(function(i) {
		var mycat = $(this).find('.cay').val();
		if(i > 0)
		{
			var mycat2 = mycat.replace(/[,\.\s-]/g, "_");
			$(this).find('.cag').html(('{' + 'INDEX_NEWS_' + mycat2 + '}').toUpperCase());
			$(this).find('.caf').html('news.' + mycat2 + '.tpl');
		}
		else
		{
			$(this).find('.cag').html(('{' + 'INDEX_NEWS}').toUpperCase());
			$(this).find('.caf').html('news.tpl');
		}
		if ($(this).length && mycat !== '')
		{
			if (!(newstext.indexOf(mycat) + 1))
			{
				newstext += mycat;
				unsetcats = '|' + $(this).find('.cac').val();
				if ($(this).find('.cam').val() !== '' && $(this).find('.cam').val() != '0')
				{
					unsetcats += '|' + $(this).find('.cam').val();
				}
				newstext +=  unsetcats;
				if (i < num) newstext +=  ', ';

				$(this).find('.cat_desc').show();
				$(this).find('.cat_exists').hide();

			}
			else
			{
				$(this).find('.cat_desc').hide();
				$(this).find('.cat_exists').show();
			}
		}
	});
	$('[name=category]').val(newstext);
}

$(".deloption").live("click", function () {
	$(this).closest('tr').remove();
	changecats();
	return false;
});

$('#addoption').live("click", function(){
	var object = $('.newscat').last().clone();
	$(object).find('.deloption').show();
	$(object).insertBefore('#addtr').show();
	changecats();
	return false;
});

$('.cam, .cac, select').live("change", function(){
	changecats();
});

$(document).ready(function(){
	$('[name=category]').closest('tr').hide();
	$('[name=maxpages]').closest('tr').hide();

	$('#catgenerator .newscat').each(function(i) {
		var input = $('[name=newsmaincat]').clone();
		newstext = $(this).find('.cay').val();
		$(input).val(newstext).insertBefore($(this).find('.cay'));
		$(this).find('.cay').remove();
		$(input).attr('name', 'cay').attr('class', 'cay');
		if(i > 0) $(this).find('.deloption').show();

	});
	$('.cay').width('200px');
	changecats();
});