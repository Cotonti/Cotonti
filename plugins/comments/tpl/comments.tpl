<!-- BEGIN: MAIN -->

<!-- BEGIN: COMMENTS_TITLE -->
<h2><a href="{COMMENTS_TITLE_URL}">{COMMENTS_TITLE}</a></h2>
<!-- END: COMMENTS_TITLE -->

{FILE ./themes/nemesis/warnings.tpl}

<!-- BEGIN: COMMENTS_FORM_EDIT -->
<div class="block">
	<form id="comments" name="comments" action="{COMMENTS_FORM_POST}" method="post">
	<table class="cells">
	<tr>
		<td class="width20"><b>{COMMENTS_POSTER_TITLE}:</b></td>
		<td class="width80">{COMMENTS_POSTER}</td>
	</tr>
	<tr>
		<td><b>{COMMENTS_IP_TITLE}:</b></td>
		<td>{COMMENTS_IP}</td>
	</tr>
	<tr>
		<td><b>{COMMENTS_DATE_TITLE}:</b></td>
		<td>{COMMENTS_DATE}</td>
	</tr>
	<tr>
		<td colspan="2">{COMMENTS_FORM_TEXT}</td>
	</tr>
	<tr>
		<td colspan="2" class="valid">
			<div align="center">
				<input type="submit" class="submit" value="{COMMENTS_FORM_UPDATE_BUTTON}">
			</div>
		</td>
	</tr>
	</table>
	</form>
</div>
<!-- END: COMMENTS_FORM_EDIT -->

<!-- END: MAIN -->

<!-- BEGIN: COMMENTS -->

<a name="comments"></a>

<div style="display:{COMMENTS_DISPLAY}">

<!-- BEGIN: COMMENTS_ROW -->
	<div class="comments1">
		<p>{COMMENTS_ROW_AVATAR}</p>
		<p><a href="{COMMENTS_ROW_URL}" id="c{COMMENTS_ROW_ID}">{COMMENTS_ROW_ORDER}.</a> {COMMENTS_ROW_AUTHOR}</p>
		<p>{COMMENTS_ROW_DATE}</p>
	</div>
	<div class="comments2">
		<p>{COMMENTS_ROW_TEXT}</p>
		{COMMENTS_ROW_EDIT} {COMMENTS_ROW_ADMIN}
	</div>
	<hr class="clear" />
<!-- END: COMMENTS_ROW -->

<!-- BEGIN: PAGNAVIGATOR -->
<p class="paging">{COMMENTS_PAGES_PAGESPREV}{COMMENTS_PAGES_PAGNAV}{COMMENTS_PAGES_PAGESNEXT}</p>
<p class="paging"><span class="a1">{COMMENTS_PAGES_INFO}</span></p>
<!-- END: PAGNAVIGATOR -->

<!-- BEGIN: COMMENTS_NEWCOMMENT -->
	<h2 class="comments">{PHP.L.Newcomment}</h2>
	<form action="{COMMENTS_FORM_SEND}" method="post" name="newcomment">
		<div>{COMMENTS_FORM_TEXT}</div>
		<div class="valid"><button type="submit">{PHP.L.Submit}</button></div>
	</form>
	<div class="help">{COMMENTS_FORM_HINT}</div>
<!-- END: COMMENTS_NEWCOMMENT -->

<!-- BEGIN: COMMENTS_EMPTY -->
<div class="block">{COMMENTS_EMPTYTEXT}</div>
<!-- END: COMMENTS_EMPTY -->

<!-- BEGIN: COMMENTS_ERROR -->
<div class="error">{COMMENTS_ERROR_BODY}</div>
<!-- END: COMMENTS_ERROR -->

<!-- BEGIN: COMMENTS_CLOSED -->
<div class="error">{COMMENTS_CLOSED}</div>
<!-- END: COMMENTS_CLOSED -->

</div>

<!-- END: COMMENTS -->