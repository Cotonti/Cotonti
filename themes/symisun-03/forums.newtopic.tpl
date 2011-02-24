<!-- BEGIN: MAIN -->

	<div id="content">
    	<div class="padding20 whitee">
        	
            <h1>{FORUMS_NEWTOPIC_PAGETITLE}</h1>
            <div class="breadcrumb">{PHP.themelang.list.bread}: {FORUMS_NEWTOPIC_PAGETITLE}</div>
            
            <p class="details">{FORUMS_NEWTOPIC_SUBTITLE}</p>
		
<!-- BEGIN: FORUMS_NEWTOPIC_ERROR -->
<div class="error">{FORUMS_NEWTOPIC_ERROR_BODY}</div>
<!-- END: FORUMS_NEWTOPIC_ERROR -->

<form action="{FORUMS_NEWTOPIC_SEND}" method="post" name="newtopic">

<fieldset>
    <legend>{PHP.themelang.pageadd.basic}</legend>
    <div>
        <label>{PHP.L.Title}</label>
        {FORUMS_NEWTOPIC_TITLE}
    </div>
    <div>
        <label>{PHP.L.Description}</label>
        {FORUMS_NEWTOPIC_DESC}
    </div>

    <!-- BEGIN: FORUMS_NEWTOPIC_TAGS -->
    <div>
        <label>{FORUMS_NEWTOPIC_TOP_TAGS}</label>
        {FORUMS_NEWTOPIC_FORM_TAGS} &nbsp; <span class="hint">{FORUMS_NEWTOPIC_TOP_TAGS_HINT}</span>
    </div>
    <!-- END: FORUMS_NEWTOPIC_TAGS -->
</fieldset>

<fieldset>
    <legend>{PHP.L.Text}</legend>
    <div style="padding:0 10px; margin-top:-15px" class="pageadd">
        <div style="width:100%;">&nbsp; {FORUMS_NEWTOPIC_TEXT}</div>
    </div>
</fieldset>

<!-- BEGIN: PRIVATE -->
<fieldset>
    <legend>{PHP.L.forums_privatetopic1}</legend>
    <div>
        {PHP.L.forums_privatetopic1} &nbsp; {FORUMS_NEWTOPIC_ISPRIVATE} &nbsp; <span class="hint">{PHP.L.forums_privatetopic2}</span>
    </div>
</fieldset>
<!-- END: PRIVATE -->

<!-- BEGIN: POLL -->
<fieldset>
    <legend>{PHP.L.Poll}</legend>
    <div>
        <label>{PHP.L.Edit}</label>
        <script type="text/javascript" src="{PHP.cfg.modules_dir}/polls/js/polls.js"></script>
	<script type="text/javascript">
            var ansMax = {PHP.cfg.polls.max_options_polls};
	</script>
	{EDIT_POLL_IDFIELD}
	{EDIT_POLL_TEXT}
    </div>
    <div>
        <label>{PHP.L.Options}</label>
        <!-- BEGIN: OPTIONS -->
        <div class="polloptiondiv">
            {EDIT_POLL_OPTION_TEXT}
            <input name="deloption" value="x" type="button" class="deloption" style="display:none;" />
        </div>
        <!-- END: OPTIONS -->
        <input id="addoption" name="addoption" value="{PHP.L.Add}" type="button" style="display:none;" />
    </div>
    <div>
        {EDIT_POLL_MULTIPLE}
    </div>

    <!-- BEGIN: EDIT -->
    <div>
        <label>{PHP.L.Close}</label>
        {EDIT_POLL_CLOSE}
    </div>
    <div>
        <label>{PHP.L.Reset}</label>
        {EDIT_POLL_RESET}
    </div>
    <div>
        <label>{PHP.L.Delete}</label>
        {EDIT_POLL_DELETE}
    </div>
<!-- END: EDIT -->
</fieldset>
<!-- END: POLL -->

<p>&nbsp;</p>
<input type="submit" value="{PHP.L.Submit}" class="submit" />
				
</form>

</div>
</div>
<br class="clear" />

<!-- END: MAIN -->