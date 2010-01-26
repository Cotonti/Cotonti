<!-- BEGIN: MAIN -->

	<div id="content">
    	<div class="padding20 whitee">
        	
            <h1>{PAGEADD_PAGETITLE}</h1>
            <p class="details">{PAGEADD_SUBTITLE}</p>

		<!-- BEGIN: PAGEADD_ERROR -->
		<div class="error">{PAGEADD_ERROR_BODY}</div>
		<!-- END: PAGEADD_ERROR -->

<form action="{PAGEADD_FORM_SEND}" method="post">

<fieldset>
<legend>{PHP.skinlang.pageadd.save}</legend>
<div>
<label>{PHP.L.Category}</label>
{PAGEADD_FORM_CAT}
</div>
</fieldset>

<fieldset>
<legend>{PHP.skinlang.pageadd.basic}</legend>
<div>
<label>{PHP.L.Title}</label>
{PAGEADD_FORM_TITLE}
</div>
<div>
<label>{PHP.L.Description}</label>
{PAGEADD_FORM_DESC}
</div>
<!-- BEGIN: TAGS -->
<div>
<label>{PAGEADD_TOP_TAGS}</label>
{PAGEADD_FORM_TAGS} &nbsp; <span class="hint">{PAGEADD_TOP_TAGS_HINT}</span>
</div>
<!-- END: TAGS -->
</fieldset>

<fieldset>
<legend>{PHP.skinlang.pageadd.adv}</legend>
<div>
<label>{PHP.L.Extrakey}</label>
{PAGEADD_FORM_KEY}
</div>
<div>
<label>{PHP.L.Alias}</label>
{PAGEADD_FORM_ALIAS}
</div>
</fieldset>

<fieldset>
<legend>{PHP.L.Text}</legend>
<div style="padding:0 10px; margin-top:-15px" class="pageadd">
{PAGEADD_FORM_TEXT}
 &nbsp; &nbsp; {PAGEADD_FORM_PFS_TEXT_USER} &nbsp; {PAGEADD_FORM_PFS_TEXT_SITE}
</div>
</fieldset>

<fieldset>
<legend>{PHP.skinlang.pageadd.dates}</legend>
<div>
<label>{PHP.L.Begin}</label>
{PAGEADD_FORM_BEGIN}
</div>
<div>
<label>{PHP.L.Expire}</label>
{PAGEADD_FORM_EXPIRE}
</div>
</fieldset>

<fieldset>
<legend>{PHP.skinlang.pageadd.down}</legend>
<div>
<label>{PHP.skinlang.pageadd.File}</label>
{PAGEADD_FORM_FILE} &nbsp; <span class="hint">{PHP.skinlang.pageadd.Filehint}</span>
</div>
<div>
<label>{PHP.L.URL}</label>
{PAGEADD_FORM_URL} &nbsp; {PAGEADD_FORM_PFS_URL_USER} &nbsp; {PAGEADD_FORM_PFS_URL_SITE} &nbsp; <span class="hint">{PHP.skinlang.pageadd.URLhint}</span>
</div>
<div>
<label>{PHP.skinlang.pageadd.Filesize}</label>
{PAGEADD_FORM_SIZE} &nbsp; <span class="hint">{PHP.skinlang.pageadd.Filesizehint}</span>
</div>
</fieldset>

<p style="color:#c66; padding:10px 15px">{PHP.skinlang.pageadd.Formhint}</p>
<p>&nbsp;</p>
<!-- IF {PHP.usr_can_publish} -->
<input name="rpublish" type="submit" class="submit" value="{PHP.L.Publish}" onclick="this.value='OK';return true" />
<input type="submit" value="{PHP.L.Putinvalidationqueue}" class="submit" />
<!-- ELSE -->
<input type="submit" value="{PHP.L.Submit}" class="submit" />
<!-- ENDIF -->
				
</form>

</div>
</div>
<br class="clear" />

<!-- END: MAIN -->