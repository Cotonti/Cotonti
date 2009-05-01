<!-- BEGIN: MAIN -->

	<div class="mboxHD">{PAGEADD_PAGETITLE}</div>
	<div class="mboxBody">

		<div id="subtitle">{PAGEADD_SUBTITLE}</div>

		<!-- BEGIN: PAGEADD_ERROR -->
		<div class="error">{PAGEADD_ERROR_BODY}</div>
		<!-- END: PAGEADD_ERROR -->

		<form action="{PAGEADD_FORM_SEND}" method="post" name="newpage">
			<div class="tCap2"></div>
			<table class="cells" border="0" cellspacing="1" cellpadding="2">
				<tr>
					<td style="width:176px;">{PHP.L.Category}:</td>
					<td>{PAGEADD_FORM_CAT}</td>
				</tr>
				<tr>
					<td>{PHP.L.Title}:</td>
					<td>{PAGEADD_FORM_TITLE}</td>
				</tr>
				<tr>
					<td>{PHP.L.Description}:</td>
					<td>{PAGEADD_FORM_DESC}</td>
				</tr>
				<tr>
					<td>{PHP.L.Author}:</td>
					<td>{PAGEADD_FORM_AUTHOR}</td>
				</tr>
				<tr>
					<td>{PHP.L.Extrakey}</td>
					<td>{PAGEADD_FORM_KEY}</td>
				</tr>
				<tr>
					<td>{PHP.L.Alias}:</td>
					<td>{PAGEADD_FORM_ALIAS}</td>
				</tr>
				<!-- BEGIN: TAGS -->
				<tr>
					<td>{PAGEADD_TOP_TAGS}:</td>
					<td>{PAGEADD_FORM_TAGS} ({PAGEADD_TOP_TAGS_HINT})</td>
				</tr>
				<!-- END: TAGS -->
				<tr>
					<td>{PHP.L.Owner}:</td>
					<td>{PAGEADD_FORM_OWNER}</td>
				</tr>
				<tr>
					<td>{PHP.L.Begin}:</td>
					<td>{PAGEADD_FORM_BEGIN}</td>
				</tr>
				<tr>
					<td>{PHP.L.Expire}:</td>
					<td>{PAGEADD_FORM_EXPIRE}</td>
				</tr>
				<tr>
					<td colspan="2">{PHP.L.Text}:
						<div style="width:100%;">{PAGEADD_FORM_TEXT}</div>
						{PAGEADD_FORM_PFS_TEXT_USER}&nbsp;&nbsp; {PAGEADD_FORM_PFS_TEXT_SITE}
					</td>
				</tr>
				<tr>
					<td>{PHP.skinlang.pageadd.File}:<br />
					{PHP.skinlang.pageadd.Filehint}</td>
					<td>{PAGEADD_FORM_FILE}</td>
				</tr>
				<tr>
					<td>{PHP.L.URL}:<br />
					{PHP.skinlang.pageadd.URLhint}</td>
					<td>{PAGEADD_FORM_URL}<br>
					{PAGEADD_FORM_PFS_URL_USER}&nbsp;&nbsp; {PAGEADD_FORM_PFS_URL_SITE}</td>
				</tr>
				<tr>
					<td>{PHP.skinlang.pageadd.Filesize}:<br />
					{PHP.skinlang.pageadd.Filesizehint}</td>
					<td>{PAGEADD_FORM_SIZE}</td>
				</tr>
				<tr>
					<td colspan="2">{PHP.skinlang.pageadd.Formhint}</td>
				</tr>
				<tr>
					<td colspan="2" class="valid">
					<input type="submit" value="{PHP.L.Submit}" />
					</td>
				</tr>
			</table>
			<div class="bCap"></div>
		</form>
	</div>

<!-- END: MAIN -->