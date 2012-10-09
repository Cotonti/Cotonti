<!-- BEGIN: MAIN -->

	<div id="content">
    	<div class="padding20 whitee">
        	
            <h1>{FORUMS_EDITPOST_PAGETITLE}</h1>
            <div class="breadcrumb">{PHP.themelang.list.bread}: {FORUMS_EDITPOST_PAGETITLE}</div>
            
            <p class="details">{FORUMS_EDITPOST_SUBTITLE}</p>

{FILE "./themes/symisun-03/warnings.tpl"}

<form action="{FORUMS_EDITPOST_SEND}" method="post" name="editpost">

<!-- BEGIN: FORUMS_EDITPOST_FIRSTPOST -->
<fieldset>
    <legend>{PHP.themelang.pageadd.basic}</legend>
        <div>
            <label>{PHP.L.forums_topic}</label>
            {FORUMS_EDITPOST_TOPICTITTLE}
        </div>
        <div>
            <label>{PHP.L.Description}</label>
            {FORUMS_EDITPOST_TOPICDESCRIPTION}
        </div>
<!-- END: FORUMS_EDITPOST_FIRSTPOST -->
<!-- BEGIN: FORUMS_EDITPOST_TAGS -->
        <div>
            <label>{PHP.L.Tags}</label>
            {FORUMS_EDITPOST_FORM_TAGS} &nbsp; <span class="hint">{FORUMS_EDITPOST_TOP_TAGS_HINT}</span>
        </div>
<!-- END: FORUMS_EDITPOST_TAGS -->
</fieldset>

<fieldset>
    <legend>{PHP.L.Text}</legend>
    <div style="padding:0 10px; margin-top:-15px" class="pageadd">
        <div style="width:100%;">&nbsp;{FORUMS_EDITPOST_TEXT}</div>
    </div>
</fieldset>

<!-- BEGIN: POLL -->
<script type="text/javascript" src="{PHP.cfg.modules_dir}/polls/js/polls.js"></script>
<script type="text/javascript">
    var ansMax = {PHP.cfg.polls.max_options_polls};
</script>

<fieldset>
    <legend>{PHP.L.Poll}</legend>
</fieldset>
    <table>
					<!-- BEGIN: POLL -->
					<tr>
						<script type="text/javascript" src="{PHP.cfg.modules_dir}/polls/js/polls.js"></script>
						<script type="text/javascript">
							var ansMax = {PHP.cfg.polls.max_options_polls};
						</script>
						<td>{PHP.L.poll}:</td>
						<td>
							{EDIT_POLL_IDFIELD}
							{EDIT_POLL_TEXT}
						</td>
					</tr>
					<tr>
						<td>{PHP.L.Options}:</td>
						<td>
							<!-- BEGIN: OPTIONS -->
							<div class="polloptiondiv">
								{EDIT_POLL_OPTION_TEXT}
								<input name="deloption" value="x" type="button" class="deloption" style="display:none;" />
							</div>
							<!-- END: OPTIONS -->
							<input id="addoption" name="addoption" value="{PHP.L.Add}" type="button" style="display:none;" /></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>{EDIT_POLL_MULTIPLE}</td>
					</tr>
					<!-- BEGIN: EDIT -->
					<tr>
						<td>&nbsp;</td>
						<td>{EDIT_POLL_LOCKED} {EDIT_POLL_RESET} {EDIT_POLL_DELETE}</td>
					</tr>
					<!-- END: EDIT -->
					<!-- END: POLL -->
                               </table>
<fieldset>
    <!-- BEGIN: EDIT -->
    <div>
        <label>{PHP.L.Close}</label>
        {EDIT_POLL_LOCKED}
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
<input type="submit" value="{PHP.L.Update}" class="submit" />
				
</form>

</div>
</div>
<br class="clear" />

<!-- END: MAIN -->