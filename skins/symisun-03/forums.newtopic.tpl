<!-- BEGIN: MAIN -->

	<div id="content">
    	<div class="padding20 whitee">
        	
            <h1>{PHP.fs_title}</h1>
            <div class="breadcrumb">{PHP.skinlang.list.bread}: {FORUMS_NEWTOPIC_PAGETITLE}</div>
            
            <p class="details">{FORUMS_NEWTOPIC_SUBTITLE}</p>
		
<!-- BEGIN: FORUMS_NEWTOPIC_ERROR -->
<div class="error">{FORUMS_NEWTOPIC_ERROR_BODY}</div>
<!-- END: FORUMS_NEWTOPIC_ERROR -->

<form action="{FORUMS_NEWTOPIC_SEND}" method="post">

<fieldset>
<legend>{PHP.skinlang.pageadd.basic}</legend>
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
	<div style="width:100%;">{FORUMS_NEWTOPIC_TEXTBOXER}</div>
</div>
</fieldset>

<!-- BEGIN: PRIVATE -->
<fieldset>
<legend>{PHP.L.Private}</legend>
<div>
<label>{PHP.L.Private}</label>
{FORUMS_NEWTOPIC_ISPRIVATE} &nbsp; <span class="hint">{PHP.skinlang.forumsnewtopic.privatetopic1}. {PHP.skinlang.forumsnewtopic.privatetopic2}</span>
</div>
</fieldset>
<!-- END: PRIVATE -->

<!-- BEGIN: POLL -->
<fieldset>
<legend>{PHP.L.Poll}</legend>
<div>
<label>{PHP.L.Edit}</label>
<input type="text" class="text" name="poll_text" value="{EDIT_POLL_TEXT}" size="64" maxlength="255" />
</div>
<div>
<label>{PHP.L.Options}</label>
{EDIT_POLL_OPTIONS}
</div>
<div>
<label>{PHP.L.polls_multiple}</label>
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