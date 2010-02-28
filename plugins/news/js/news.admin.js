function changecats()
{
	var newstext = '';
	var unsetcats = '';
	for (var i = 1; i <= num; i++)
	{
		var mycat = $('#cay_'+i).val();
		$('#cag_' + i).html(('{' + 'INDEX_NEWS_' + mycat + '}').toUpperCase());
		$('#caf_' + i).html('news.' + mycat + '.tpl');
		if ($('#cat_' + i).length && mycat != '')
		{
			if (!(newstext.indexOf(mycat) + 1))
			{
				newstext += mycat;
				unsetcats = '';
				if ($('#cac_' + i).val() != $('[name=maxpages]').val())
					unsetcats = '|' + $('#cac_' + i).val();
				if ($('#cam_' + i).val() != '' && $('#cam_' + i).val() != '0')
				{
					if (unsetcats == '') unsetcats = "|";
					unsetcats += '|' + $('#cam_'+i).val();
				}
				newstext +=  unsetcats + ', ';

				$('#cat_' + i + ' > .cat_desc').show();
				$('#cat_' + i + ' > .cat_exists').hide();

			}
			else
			{
				$('#cat_' + i + ' > .cat_desc').hide();
				$('#cat_' + i + ' > .cat_exists').show();
			}
		}
	}
	unsetcats = '';
	if ($('[name=newsmaincac]').attr('checked'))
	{
		unsetcats = "|1";
	}
	if ($('#cam_main').val() != '' && $('#cam_main').val() != '0')
	{
		if(unsetcats == '')
			unsetcats = "|";
		unsetcats += '|' + $('#cam_main').val();
	}
	newstext = $('[name=newsmaincat]').val() + unsetcats+', ' + newstext;
	$('[name=category]').val(newstext);
}

$(".deloption").live("click", function () {
	$(this).parents('tr').remove();
	changecats();
	return false;
});

$('select').live("change", function(){
	changecats();
});
$('[name=cam]').live("change", function(){
	changecats();
});
$('[name=newsmaincac]').live("click", function(){
	changecats();
});

$(document).ready(function(){
	$('#helptext').insertAfter('[name=maxpages]');
	$('[name=maxpages]').insertBefore('#main_cat');
	$("#cat_new").hide();
	$("#catgenerator").show();
	$('[name=category]').insertBefore('#addoption');
	$('[name=x]').insertBefore('#addoption');
	$("#syncpag").html($('[name=syncpagination]').parent().html());
	$('[name=category]').hide();
	$('#catgenerator').parents('form#saveconfig').html($('#catgenerator').html());

	for (var i = 1; i <= num + 1; i++)
	{
		if(i == (num + 1))
		{
			i = 'new';
		}
		var input = $('[name=newsmaincat]').clone();
		newstext = $('#cay_' + i).val();
		$(input).val(newstext);
		$(input).insertBefore('#cay_' + i);
		$('#cay_' + i).remove();
		$(input).attr('name', 'cay');
		$(input).attr('id', 'cay_' + i);
		var input2 = $('[name=maxpages]').clone();
		newstext = $('#cac_'+i).val();
		if(newstext == '')
		{
			newstext = $('[name=maxpages]').val();
		}
		$(input2).val(newstext);
		$(input2).insertBefore('#cac_' + i);
		$('#cac_' + i).remove();
		$(input2).attr('name', 'cac');
		$(input2).attr('id', 'cac_' + i);
	}

	$('#cac_new').val($('[name=maxpages]').val());
	$('[name=category]').width('100%');
	$('[name=cay]').width('200px');
	$('[name=newsmaincat]').width('200px');
	changecats();

	$('#addoption').click(function(){
		num++;
		var object = $('#cat_new').clone().attr("id", 'cat_' + num);
		$(object).find('#cay_new').attr("id", 'cay_' + num);
		$(object).find('#cac_new').attr("id", 'cac_' + num);
		$(object).find('#cag_new').attr("id", 'cag_' + num);
		$(object).find('#caf_new').attr("id", 'caf_' + num);
		$(object).find('#cam_new').attr("id", 'cam_' + num);
		$(object).insertBefore('#addtr').show();
		changecats();
	});

});