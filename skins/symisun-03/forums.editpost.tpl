<!-- BEGIN: MAIN -->

	<div id="content">
    	<div class="padding20 whitee">
        	
            <h1>{PHP.fs_title}</h1>
            <div class="breadcrumb">{PHP.skinlang.list.bread}: {FORUMS_EDITPOST_PAGETITLE}</div>
            
            <p class="details">{FORUMS_EDITPOST_SUBTITLE}</p>

<!-- BEGIN: FORUMS_EDITPOST_ERROR -->
<div class="error">{FORUMS_POSTS_EDITPOST_ERROR_BODY}</div>
<!-- END: FORUMS_EDITPOST_ERROR -->

<form action="{FORUMS_EDITPOST_SEND}" method="post">

<fieldset>
<legend>{PHP.skinlang.pageadd.basic}</legend>
<!-- BEGIN: FORUMS_EDITPOST_FIRSTPOST -->
<div>
<label>{PHP.L.Title}</label>
{FORUMS_EDITPOST_TOPICTITTLE}
</div>
<div>
<label>{PHP.L.Description}</label>
{FORUMS_EDITPOST_TOPICDESCRIPTION}
</div>
<!-- END: FORUMS_EDITPOST_FIRSTPOST -->
<!-- BEGIN: FORUMS_EDITPOST_TAGS -->
<div>
<label>{FORUMS_EDITPOST_TOP_TAGS}</label>
{FORUMS_EDITPOST_FORM_TAGS} &nbsp; <span class="hint">{FORUMS_EDITPOST_TOP_TAGS_HINT}</span>
</div>
<!-- END: FORUMS_EDITPOST_TAGS -->
</fieldset>

<fieldset>
<legend>{PHP.L.Text}</legend>
<div style="padding:0 10px; margin-top:-15px" class="pageadd">
	<div style="width:100%;">{FORUMS_EDITPOST_TEXTBOXER}</div>
</div>
</fieldset>

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
<input type="submit" value="{PHP.L.Update}" class="submit" />
				
</form>

</div>
</div>
<br class="clear" />

<!-- END: MAIN -->