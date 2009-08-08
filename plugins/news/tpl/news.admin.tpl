<!-- BEGIN: ADMIN -->
<script type="text/javascript">
    var num = {CATNUM};
    function removequest(object)
    {
        var objectparent = $(object).parent();
        $(objectparent).remove();
        changecats();
        return false;
    }

    function changecats()
    {
        var newstext="";
        for (var i=1; i<=num; i++)
        {
            var mycat=$('#cay_'+i).val();
            $('#cag_'+i).html('{'+'INDEX_NEWS_'+mycat+'}');
            $('#caf_'+i).html('news.'+mycat+'.tpl');
            if($('#cat_'+i).length && mycat!='')
            {
                if(!(newstext.indexOf(mycat) + 1))
                {
                    newstext += mycat;
                    if ($('#cac_'+i).val()!=$('[name=maxpages]').val())
                        newstext += '|'+$('#cac_'+i).val();
                    newstext +=', ';
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
        var unsetcats = '';
        if ($('[name=newsmaincac]').attr('checked'))
        {
            unsetcats = "|1";
        }
        newstext = $('[name=newsmaincat]').val()+unsetcats+', ' + newstext;
        $('[name=category]').val(newstext);
    }

    $(document).ready(function(){
        $('#addoption').click(function(){
            num++;
            var object = $('#cat_new').clone().attr("id", 'cat_'+num);
            $(object).find('#cay_new').attr("id", 'cay_'+num);
            $(object).find('#cac_new').attr("id", 'cac_'+num);
            $(object).find('#cag_new').attr("id", 'cag_'+num);
            $(object).find('#caf_new').attr("id", 'caf_'+num);
            $(object).insertBefore(this).show();
            $('select').attr('onchange', 'changecats()');
            changecats();
        });

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

        $('select').change(function(){ changecats(); });
        $('[name=newsmaincac]').click(function(){ changecats(); });
        $('#cac_new').val($('[name=maxpages]').val());
        $('#helptext').insertAfter('[name=maxpages]');
        $('[name=maxpages]').insertAfter('#main_cat');
        $("#cat_new").hide();
        $("#catgenerator").show();
        $('[name=category]').hide();
        $('#helptext').parent().parent().remove();
        $('[name=category]').width('100%');
        $('[name=cay]').width('250px');
        $('[name=newsmaincat]').width('250px');
        $('select').attr('onchange', 'changecats()');
        changecats();


    });
</script>

<div id="catgenerator" style="display:none">
    <span style="font-weight:bold">{PHP.L.Maincat} :</span> <br/>
    <span id="main_cat">{PHP.L.Category}: {MAINCATEGORY} {PHP.L.NewsCount}: </span><br/>
    &nbsp;  {PHP.L.Tag}: <span class="cat_tag" style="font-weight:bold">&#123;INDEX_NEWS}</span>
    {PHP.L.Template}: <span class="cat_file"  style="font-weight:bold">news.tpl</span><br/>
    <hr />
    <span style="font-weight:bold">{PHP.L.Addcat} :</span> <br/>
    <!-- BEGIN: ADDITIONAL -->
    <div id="cat_{ADDNUM}">
        {PHP.L.Category}: <input type="text" class="text" name="cay" id="cay_{ADDNUM}" value="{ADDCATEGORY}" size="32" maxlength="255" />
        {PHP.L.NewsCount}: <input type="text" class="text" name="cac" id="cac_{ADDNUM}" value="{ADDCOUNT}" size="3" maxlength="255" />
        <input  name='addoption' value='x' onclick='removequest(this)' type='button' /><br/>
        <div class="cat_desc"> &nbsp;  {PHP.L.Tag}: <span id="cag_{ADDNUM}" style="font-weight:bold; text-transform:uppercase;">&nbsp;</span>
        {PHP.L.Template}: <span id="caf_{ADDNUM}" style="font-weight:bold">&nbsp;</span></div>
        <div class="cat_exists" style="color:red; display:none;"> &nbsp;  {PHP.L.Newscat_exists}</div>
    </div>
    <!-- END: ADDITIONAL -->
    <input  name="addoption" value="{PHP.L.Add}" id="addoption" type="button" /><br/>
    {PHP.L.Template_help}<br/>
    <label><input type="checkbox" value="1" name="newsmaincac" {UNSETADD} />&nbsp; {PHP.L.Unsetadd}</label>
</div>
<!-- END: ADMIN -->
