<!-- BEGIN: MAIN -->

	<div id="content">
    	<div class="padding20 whitee">

            <h1>{PAGEEDIT_PAGETITLE}</h1>
            <p class="details">{PAGEEDIT_SUBTITLE}</p>

		<!-- BEGIN: PAGEEDIT_ERROR -->
		<div class="error">{PAGEEDIT_ERROR_BODY}</div>
		<!-- END: PAGEEDIT_ERROR -->

<form action="{PAGEEDIT_FORM_SEND}" method="post" enctype="multipart/form-data">

	<fieldset>
		<legend>{PHP.themelang.pageadd.save}</legend>
		<div>
			<label>{PHP.L.Category}</label>
			{PAGEEDIT_FORM_CAT}
		</div>
	</fieldset>

	<fieldset>
		<legend>{PHP.themelang.pageadd.basic}</legend>
		<div>
			<label>{PHP.L.Title}</label>
			{PAGEEDIT_FORM_TITLE} &nbsp; <span class="hint">{PHP.L.page_pageid}: #{PAGEEDIT_FORM_ID}</span>
		</div>
		<div>
			<label>{PHP.L.Description}</label>
			{PAGEEDIT_FORM_DESC}
		</div>
	</fieldset>

	<fieldset>
		<legend>{PHP.themelang.pageadd.adv}</legend>
		<div>
			<label>{PHP.L.Alias}</label>
			{PAGEEDIT_FORM_ALIAS}
		</div>
		<!-- BEGIN: TAGS -->
		<div>
			<label>{PAGEEDIT_TOP_TAGS}</label>
			{PAGEEDIT_FORM_TAGS} &nbsp; <span class="hint">{PAGEEDIT_TOP_TAGS_HINT}</span>
		</div>
		<!-- END: TAGS -->
		<div>
			<label>{PHP.L.Keywords}</label>
			{PAGEEDIT_FORM_KEYWORDS}
		</div>
		<!-- BEGIN: ADMIN -->
		<div>
			<label>{PHP.L.Owner}</label>
			{PAGEEDIT_FORM_OWNERID}
		</div>
		<div>
			<label>{PHP.L.Hits}</label>
			{PAGEEDIT_FORM_PAGECOUNT}
		</div>
		<!-- END: ADMIN -->
		<div>
			<label>{PHP.L.Parser}</label>
			{PAGEEDIT_FORM_PARSER}
		</div>
	</fieldset>

	<fieldset>
		<legend>{PHP.L.Text}</legend>
		<div style="padding:0 10px; margin-top:-15px" class="pageadd">
			{PAGEEDIT_FORM_TEXT}
			 &nbsp; &nbsp; {PAGEEDIT_FORM_PFS} &nbsp; {PAGEEDIT_FORM_SFS}
		</div>
	</fieldset>

	<fieldset>
		<legend>{PHP.themelang.pageadd.dates}</legend>
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
		<legend>{PHP.themelang.pageadd.down}</legend>
		<div>
			<label>{PHP.L.page_file}</label>
			{PAGEEDIT_FORM_FILE} &nbsp; <span class="hint">{PHP.L.page_filehint}</span>
		</div>
		<div>
			<label>{PHP.L.URL}</label>
			{PAGEEDIT_FORM_URL} &nbsp; {PAGEEDIT_FORM_URL_PFS} &nbsp; {PAGEEDIT_FORM_URL_SFS} &nbsp; <span class="hint">{PHP.L.page_urlhint}</span>
		</div>
		<div>
			<label>{PHP.L.page_filesize}</label>
			{PAGEEDIT_FORM_SIZE} &nbsp; <span class="hint">{PHP.L.page_filesizehint}</span>
		</div>
		<div>
			<label>{PHP.L.page_filehitcount}</label>
			{PAGEEDIT_FORM_FILECOUNT} &nbsp; <span class="hint">{PHP.L.page_filehitcounthint}</span>
		</div>
	</fieldset>

	<fieldset>
		<legend>{PHP.themelang.pageedit.del}</legend>
		<div>
			<label>{PHP.L.page_deletepage}</label>
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
