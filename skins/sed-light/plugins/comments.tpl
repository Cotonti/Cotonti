<!-- BEGIN: MAIN -->
<!-- BEGIN: COMMENTS_TITLE -->
<div id="title">
	<a href="{COMMENTS_TITLE_URL}">{COMMENTS_TITLE}</a>
</div>
<!-- END: COMMENTS_TITLE -->

<!-- BEGIN: COMMENTS_ERROR -->
<div class="block">
	<span style="color:red;">{COMMENTS_ERROR_BODY}</span>
</div>
<!-- END: COMMENTS_ERROR -->

<!-- BEGIN: COMMENTS_FORM_EDIT -->
<div class="block">
	<form id="comments" name="comments" action="{COMMENTS_FORM_POST}" method="post">
	<table class="cells" style="width:100%;">
	<tr>
		<td width="20%"><b>{COMMENTS_POSTER_TITLE}:</b></td>
		<td width="80%">{COMMENTS_POSTER}</td>
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

<!-- BEGIN: COMMENTS_EMPTY -->
<div class="block">
	<b>{GUESTBOOK_EMPTYTEXT}</b>
</div>
<!-- END: COMMENTS_EMPTY -->
<br />
<!-- END: MAIN -->

<!-- BEGIN: COMMENTS -->
<a name="comments">&nbsp;</a>
<div class="comments" style="display:{COMMENTS_DISPLAY}">
<!-- BEGIN: COMMENTS_ROW -->
<span class="title">
<a href="{COMMENTS_ROW_URL}" id="c{COMMENTS_ROW_ID}"><img src="skins/{PHP.skin}/img/system/icon-comment.gif" alt="" /> {COMMENTS_ROW_ORDER}.</a>
 &nbsp; {PHP.skinlang.comments.Postedby} {COMMENTS_ROW_AUTHOR}</span> &nbsp; {COMMENTS_ROW_DATE} &nbsp; {COMMENTS_ROW_ADMIN} &nbsp; {COMMENTS_ROW_EDIT}

<p>{COMMENTS_ROW_TEXT}</p>

<hr />
<!-- END: COMMENTS_ROW -->

<!-- BEGIN: PAGNAVIGATOR -->
<div class="pagnav">{COMMENTS_PAGES_PAGESPREV} {COMMENTS_PAGES_PAGNAV} {COMMENTS_PAGES_PAGESNEXT}</div>
<p>{COMMENTS_PAGES_INFO}</p>
<!-- END: PAGNAVIGATOR -->

<!-- BEGIN: COMMENTS_EMPTY -->
<div class="block">{COMMENTS_EMPTYTEXT}</div>
<!-- END: COMMENTS_EMPTY -->

<!-- BEGIN: COMMENTS_ERROR -->
<div class="error">{COMMENTS_ERROR_BODY}</div>
<!-- END: COMMENTS_ERROR -->

<!-- BEGIN: COMMENTS_NEWCOMMENT -->
<form action="{COMMENTS_FORM_SEND}" method="post" name="newcomment">
	<h4>{PHP.skinlang.comments.Comment}:</h4>
	<div style="width:100%;">{COMMENTS_FORM_TEXTBOXER}<br />{COMMENTS_FORM_HINT}</div>
	<p><input type="submit" value="{PHP.L.Submit}" /></p>
</form>
<!-- END: COMMENTS_NEWCOMMENT -->

<!-- BEGIN: COMMENTS_CLOSED -->
<div class="error">{COMMENTS_CLOSED}</div>
<!-- END: COMMENTS_CLOSED -->

</div>
<!-- END: COMMENTS -->