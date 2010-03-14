<!-- BEGIN: MAIN -->

<div class="block">

<!-- BEGIN: COMEDIT_TITLE -->
	<h2 class="comments"><a href="{COMEDIT_TITLE_URL}">{COMEDIT_TITLE}</a></h2>
<!-- END: COMEDIT_TITLE -->

<!-- BEGIN: COMEDIT_ERROR -->
	<div class="error">{COMEDIT_ERROR_BODY}</div>
<!-- END: COMEDIT_ERROR -->

<!-- BEGIN: COMEDIT_FORM_EDIT -->
	<form id="comedit" name="comedit" action="{COMEDIT_FORM_POST}" method="post" >
	<table class="cells">
		<tr>
			<td class="width20">{COMEDIT_POSTER_TITLE}:</td>
			<td class="width80">{COMEDIT_POSTER}</td>
		</tr>
		<tr>
			<td>{COMEDIT_IP_TITLE}:</td>
			<td>{COMEDIT_IP}</td>
		</tr>
		<tr>
			<td>{COMEDIT_DATE_TITLE}:</td>
			<td>{COMEDIT_DATE}</td>
		</tr>
		<tr>
			<td colspan="2">{COMEDIT_FORM_TEXT}</td>
		</tr>
		<tr>
			<td colspan="2" class="valid">
				<input type="submit" class="submit" value="{COMEDIT_FORM_UPDATE_BUTTON}">
			</td>
		</tr>
	</table>
	</form>
<!-- END: COMEDIT_FORM_EDIT -->

<!-- BEGIN: COMEDIT_EMPTY -->
	&nbsp;
<!-- END: COMEDIT_EMPTY -->

	</div>

<!-- END: MAIN -->