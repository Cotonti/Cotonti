
<!-- BEGIN: MAIN -->

<div id="title">

	{PAGEEDIT_PAGETITLE}

</div>

<div id="subtitle">

	{PAGEEDIT_SUBTITLE}

</div>

<div id="main">

<!-- BEGIN: PAGEEDIT_ERROR -->

<div class="error">

	{PAGEEDIT_ERROR_BODY}

</div>

<!-- END: PAGEEDIT_ERROR -->

<form action="{PAGEEDIT_FORM_SEND}" method="post" name="update">

<table class="cells">

	<tr>
		<td style="width:176px;">{PHP.skinlang.pageedit.Category}</td>
		<td>{PAGEEDIT_FORM_CAT}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.pageedit.Title}</td>
		<td>{PAGEEDIT_FORM_TITLE}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.pageedit.Description}</td>
		<td>{PAGEEDIT_FORM_DESC}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.pageedit.Author}</td>
		<td>{PAGEEDIT_FORM_AUTHOR}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.pageedit.Owner}</td>
		<td>{PAGEEDIT_FORM_OWNERID}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.pageedit.Date}</td>
		<td>{PAGEEDIT_FORM_DATE}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.pageedit.Begin}</td>
		<td>{PAGEEDIT_FORM_BEGIN}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.pageedit.Expire}</td>
		<td>{PAGEEDIT_FORM_EXPIRE}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.pageedit.Pagehitcount}</td>
		<td>{PAGEEDIT_FORM_PAGECOUNT}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.pageedit.Extrakey}</td>
		<td>{PAGEEDIT_FORM_KEY}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.pageedit.Alias}</td>
		<td>{PAGEEDIT_FORM_ALIAS}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.pageedit.Parsing}</td>
		<td>{PAGEEDIT_FORM_TYPE}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.pageedit.Bodyofthepage}</td>
		<td><div style="width:96%;">{PAGEEDIT_FORM_TEXTBOXER}</div></td>
	</tr>

	<tr>
		<td>{PHP.skinlang.pageedit.Filedownload}</td>
		<td>{PAGEEDIT_FORM_FILE}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.pageedit.URL}<br />
		{PHP.skinlang.pageedit.URLhint}</td>
		<td>{PAGEEDIT_FORM_URL}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.pageedit.Filesize}<br />
		{PHP.skinlang.pageedit.Filesizehint}</td>
		<td>{PAGEEDIT_FORM_SIZE}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.pageedit.Filehitcount}<br />
		{PHP.skinlang.pageedit.Filehitcounthint}</td>
		<td>{PAGEEDIT_FORM_FILECOUNT}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.pageedit.Pageid}</td>
		<td>#{PAGEEDIT_FORM_ID}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.pageedit.Deletethispage}</td>
		<td>{PAGEEDIT_FORM_DELETE}</td>
	</tr>

	<tr>
		<td colspan="2" class="valid">
		<input type="submit" class="submit" value="{PHP.skinlang.pageedit.Update}">
		</td>
	</tr>

</table>

</form>

</div>

<!-- END: MAIN -->