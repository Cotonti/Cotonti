<!-- BEGIN: ADMIN -->
<script type="text/javascript">
    var num = {CATNUM};
    function removequest(object)
    {
        $(object).parents('tr').remove();
        changecats();
        return false;
    }

    function changecats()
    {
        var newstext='';
        var unsetcats = '';
        for (var i=1; i<=num; i++)
        {
            var mycat=$('#cay_'+i).val();
            $('#cag_'+i).html(('{'+'INDEX_NEWS_'+mycat+'}').toUpperCase());
            $('#caf_'+i).html('news.'+mycat+'.tpl');
            if($('#cat_'+i).length && mycat!='')
            {
                if(!(newstext.indexOf(mycat) + 1))
                {
                    newstext += mycat;
                    unsetcats = '';
                    if ($('#cac_'+i).val()!=$('[name=maxpages]').val())
                        unsetcats = '|'+$('#cac_'+i).val();
                    if ($('#cam_'+i).val()!='' && $('#cam_'+i).val()!='0')
                    {
                        if (unsetcats=='') unsetcats="|";
                        unsetcats += '|'+$('#cam_'+i).val();
                    }
                    newstext +=  unsetcats + ', ';

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
        unsetcats = '';
        if ($('[name=newsmaincac]').attr('checked'))
        {
            unsetcats = "|1";
        }
        if ($('#cam_main').val()!='' && $('#cam_main').val()!='0')
        {
            if(unsetcats=='')
                unsetcats = "|";
            unsetcats += '|'+$('#cam_main').val();
        }
        newstext = $('[name=newsmaincat]').val()+unsetcats+', ' + newstext;
        $('[name=category]').val(newstext);
    }

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

        for (var i=1; i<=num+1; i++)
        {
            if(i==(num+1))
            {
                i='new';
            }
            var input = $('[name=newsmaincat]').clone();
            newstext = $('#cay_'+i).val();
            $(input).val(newstext);
            $(input).insertBefore('#cay_'+i);
            $('#cay_'+i).remove();
            $(input).attr('name', 'cay');
            $(input).attr('id', 'cay_'+i);
            var input2 = $('[name=maxpages]').clone();
            newstext = $('#cac_'+i).val();
            if(newstext=='')
            {
                newstext=$('[name=maxpages]').val();
            }
            $(input2).val(newstext);
            $(input2).insertBefore('#cac_'+i);
            $('#cac_'+i).remove();
            $(input2).attr('name', 'cac');
            $(input2).attr('id', 'cac_'+i);
        }


        $('#cac_new').val($('[name=maxpages]').val());


        $('[name=category]').width('100%');
        $('[name=cay]').width('200px');
        $('[name=newsmaincat]').width('200px');
        changecats();

        $('select').change(function(){ changecats(); });
        $('[name=cam]').change(function(){ changecats(); });
        $('[name=newsmaincac]').click(function(){ changecats(); });
        $('#addoption').click(function(){
            num++;
            var object = $('#cat_new').clone().attr("id", 'cat_'+num);
            $(object).find('#cay_new').attr("id", 'cay_'+num);
            $(object).find('#cac_new').attr("id", 'cac_'+num);
            $(object).find('#cag_new').attr("id", 'cag_'+num);
            $(object).find('#caf_new').attr("id", 'caf_'+num);
            $(object).find('#cam_new').attr("id", 'cam_'+num);
            $(object).insertBefore('#addtr').show();
            $('select').change(function(){ changecats(); });
            $('[name=cam]').change(function(){ changecats(); });
            $('[name=newsmaincac]').click(function(){ changecats(); });
            changecats();
        });

    });
</script>

<div id="catgenerator" style="display:none">
    <table class="cells">
        <tr>
            <td class="coltop" style="width:200px;">{PHP.L.Category}</td>
            <td class="coltop" style="width:50px;">{PHP.L.NewsCount}</td>
            <td class="coltop" style="width:50px;">{PHP.L.Newsautocut} *</td>
            <td class="coltop">{PHP.L.Tag}</td>
            <td class="coltop" style="width:150px;">{PHP.L.Template} **</td>
            <td class="coltop" style="width:20px;"></td>
        </tr>
        <tr>
            <td  class="coltop" colspan="6">{PHP.L.Maincat}</td>
        </tr>
        <tr>
            <td>{MAINCATEGORY}</td>
            <td><span id="main_cat">&nbsp;</span></td>
            <td><input type="text" class="text" name="cam" id="cam_main" value="{MAINCUT}" size="4" maxlength="4" /></td>
            <td>&#123;INDEX_NEWS}</td>
            <td>news.tpl</td>
            <td></td>
        </tr>
        <tr>
            <td  class="coltop" colspan="6">{PHP.L.Addcat}</td>
        </tr>


        <!-- BEGIN: ADDITIONAL -->
        <tr id="cat_{ADDNUM}">
            <td>
                <input type="text" class="text" name="cay" id="cay_{ADDNUM}" value="{ADDCATEGORY}" size="32" maxlength="255" /><div class="cat_exists" style="color:red; display:none;"> &nbsp;  {PHP.L.Newscat_exists}</div>
            </td>
            <td><input type="text" class="text" name="cac" id="cac_{ADDNUM}" value="{ADDCOUNT}" size="3" maxlength="255" /></td>
            <td><input type="text" class="text" name="cam" id="cam_{ADDNUM}" value="{ADDCUT}" size="4" maxlength="4" /></td>
            <td class="cat_desc"><span id="cag_{ADDNUM}">&nbsp;</span></td>
            <td class="cat_desc"><span id="caf_{ADDNUM}">&nbsp;</span></td>
            <td colspan="2" class="cat_exists" style="color:red; display:none;">{PHP.L.Newscat_exists}</td>
            <td><img src="images/admin/delete.gif" onclick='removequest(this)' alt="{PHP.L.Delete}" title="{PHP.L.Delete}" /></td>
        </tr>
        <!-- END: ADDITIONAL -->
        <tr id="addtr">
            <td  style="text-align:right;" colspan="6"><input  name="addoption" value="{PHP.L.Add}" id="addoption" type="button" /></td>
        </tr>
    </table>
    
    <label><input type="checkbox" value="1" name="newsmaincac" {UNSETADD} />&nbsp; {PHP.L.Unsetadd}</label>
    <hr />
    {PHP.L.cfg_syncpagination.0}: &nbsp;  <span id="syncpag">&nbsp; </span>

    <div class="centerall" style="padding:10px;"><input type="submit" class="submit" value="{PHP.L.Update}" /></div>
    * {PHP.L.Newsautocutdesc} <br/>
    ** {PHP.L.Template_help}
</div>
<!-- END: ADMIN -->
