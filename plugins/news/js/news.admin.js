var num = 1;

function changecats()
{
	var newstext = '';
	var unsetcats = '';
	$('[name=maxpages]').val($('#cat_1 .cac').val());

	for (var i = 1; i <= num; i++)
	{
		var mycat = $('#cat_'+i+' .cay').val();
		if(i > 1)
		{
			var mycat2 = mycat.replace(/[,. -]/i, "_");
			$('#cat_'+i+' .cag').html(('{' + 'INDEX_NEWS_' + mycat2 + '}').toUpperCase());
			$('#cat_'+i+ ' .caf').html('news.' + mycat2 + '.tpl');
		}
		else
		{
			$('#cat_'+i+' .cag').html(('{' + 'INDEX_NEWS}').toUpperCase());
			$('#cat_'+i+' .caf').html('news.tpl');			
		}
		if ($('#cat_'+i).length && mycat != '')
		{
			if (!(newstext.indexOf(mycat) + 1))
			{
				newstext += mycat;
				unsetcats = '|' + $('#cat_'+i+' .cac').val();
				if ($('#cat_'+i+' .cam').val() != '' && $('#cat_'+i+' .cam').val() != '0')
				{
					unsetcats += '|' + $('#cat_'+i+' .cam').val();
				}
				newstext +=  unsetcats;
				if (i < num) newstext +=  ', ';

				$('#cat_'+i+' > .cat_desc').show();
				$('#cat_'+i+' > .cat_exists').hide();

			}
			else
			{
				$('#cat_'+i+' > .cat_desc').hide();
				$('#cat_'+i+' > .cat_exists').show();
			}
		}
	} 
	$('[name=category]').val(newstext);
}

$(".deloption").live("click", function () {
	$(this).parents('tr').remove();
	changecats();
	return false;
});

$('.cam, .cac, select').live("change", function(){
	changecats();
});

$(document).ready(function(){
	num = $('.newscat').length - 1;
	$('#helptext').insertAfter('[name=maxpages]');
	$('[name=maxpages]').insertBefore('#addoption').hide();
	$('[name=category]').insertBefore('#addoption').width('100%').hide();
	$('[name=x]').insertBefore('#addoption');
	$("#cat_new").hide();
	$("#syncpag").html($('[name=syncpagination]').parent().parent().html());
	$('#catgenerator').show().parents('form#saveconfig').html($('#catgenerator').html());

	for (var i = 1; i <= num + 1; i++)
	{
		if(i == (num + 1)) i = 'new';
		var input = $('[name=newsmaincat]').clone();
		newstext = $('#cat_'+i+' .cay').val();
		$(input).val(newstext).insertBefore('#cat_'+i+' .cay');
		$('#cat_'+i+' .cay').remove();
		$(input).attr('name', 'cay').attr('class', 'cay');
		if(i > 1) $('#cat_'+i).find('.deloption').show();
	}

	$('.cay').width('200px');
	changecats();

	$('#addoption').click(function(){
		num++;
		var object = $('#cat_new').clone().attr("id", 'cat_' + num);
		$(object).find('.deloption').show();
		$(object).insertBefore('#addtr').show();
		changecats();
	});

});