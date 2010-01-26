<!-- BEGIN: MAIN -->

	<div id="content">
    	<div class="padding20 whitee">
        	
            <h1>{PAGEEDIT_PAGETITLE}</h1>
            <p class="details">{PAGEEDIT_SUBTITLE}</p>

		<!-- BEGIN: PAGEEDIT_ERROR -->
		<div class="error">{PAGEEDIT_ERROR_BODY}</div>
		<!-- END: PAGEEDIT_ERROR -->

<form action="{PAGEEDIT_FORM_SEND}" method="post">

<fieldset>
<legend>{PHP.skinlang.pageadd.save}</legend>
<div>
<label>{PHP.L.Category}</label>
{PAGEEDIT_FORM_CAT}
</div>
</fieldset>

<fieldset>
<legend>{PHP.skinlang.pageadd.basic}</legend>
<div>
<label>{PHP.L.Title}</label>
{PAGEEDIT_FORM_TITLE} &nbsp; <span class="hint">{PHP.skinlang.pageedit.Pageid}: #{PAGEEDIT_FORM_ID}</span>
</div>
<div>
<label>{PHP.L.Description}</label>
{PAGEEDIT_FORM_DESC}
</div>
<!-- BEGIN: TAGS -->
<div>
<label>{PAGEEDIT_TOP_TAGS}</label>
{PAGEEDIT_FORM_TAGS} &nbsp; <span class="hint">{PAGEEDIT_TOP_TAGS_HINT}</span>
</div>
<!-- END: TAGS -->
</fieldset>

<fieldset>
<legend>{PHP.skinlang.pageadd.adv}</legend>
<div>
<label>{PHP.L.Extrakey}</label>
{PAGEEDIT_FORM_KEY}
</div>
<div>
<label>{PHP.L.Alias}</label>
{PAGEEDIT_FORM_ALIAS}
</div>
<!-- BEGIN: ADMIN -->
<div>
<label>{PHP.L.Owner}</label>
{PAGEEDIT_FORM_OWNERID}
</div>
<div>
<label>{PHP.L.Parser}</label>
{PAGEEDIT_FORM_TYPE}
</div>
<div>
<label>{PHP.L.Hits}</label>
{PAGEEDIT_FORM_PAGECOUNT}
</div>
<!-- END: ADMIN -->
</fieldset>

<fieldset>
<legend>{PHP.L.Text}</legend>
<div style="padding:0 10px; margin-top:-15px" class="pageadd">
{PAGEEDIT_FORM_TEXT}
 &nbsp; &nbsp; {PAGEEDIT_FORM_PFS_TEXT_USER} &nbsp; {PAGEEDIT_FORM_PFS_TEXT_SITE}
</div>
</fieldset>

<fieldset>
<legend>{PHP.skinlang.pageadd.dates}</legend>
<div>
<label>{PHP.L.Date}</label>
{PAGEEDIT_FORM_DATE}
</div>
<div>
<label>{PHP.L.Begin}</label>
{PAGEEDIT_FORM_BEGIN}
</div>
<div>
<label>{PHP.L.Expire}</label>
{PAGEEDIT_FORM_EXPIRE}
</div>
</fieldset>

<fieldset>
<legend>{PHP.skinlang.pageadd.down}</legend>
<div>
<label>{PHP.skinlang.pageadd.File}</label>
{PAGEEDIT_FORM_FILE} &nbsp; <span class="hint">{PHP.skinlang.pageadd.Filehint}</span>
</div>
<div>
<label>{PHP.L.URL}</label>
{PAGEEDIT_FORM_URL} &nbsp; {PAGEEDIT_FORM_PFS_URL_USER} &nbsp; {PAGEEDIT_FORM_PFS_URL_SITE} &nbsp; <span class="hint">{PHP.skinlang.pageadd.URLhint}</span>
</div>
<div>
<label>{PHP.skinlang.pageadd.Filesize}</label>
{PAGEEDIT_FORM_SIZE} &nbsp; <span class="hint">{PHP.skinlang.pageadd.Filesizehint}</span>
</div>
<div>
<label>{PHP.skinlang.pageedit.Filehitcount}</label>
{PAGEEDIT_FORM_FILECOUNT} &nbsp; <span class="hint">{PHP.skinlang.pageedit.Filehitcounthint}</span>
</div>
</fieldset>

<fieldset>
<legend>{PHP.skinlang.pageedit.del}</legend>
<div>
<label>{PHP.skinlang.pageedit.Deletethispage}</label>
{PAGEEDIT_FORM_DELETE}
</div>
</fieldset>

<p>&nbsp;</p>
<!-- IF {PHP.usr_can_publish} -->
<input name="rpublish" type="submit" class="submit" value="{PHP.L.Publish}" onclick="this.value='OK';return true" />
<input type="submit" value="{PHP.L.Putinvalidationqueue}" class="submit" />
<!-- ELSE -->
<input type="submit" value="{PHP.L.Update}" class="submit" />
<!-- ENDIF -->

</form>

</div>
</div>
<br class="clear" />

<!-- END: MAIN -->