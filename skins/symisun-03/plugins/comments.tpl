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
&nbsp;
<a name="com" id="com"></a>
<h2>{PHP.L.Comments} <span class="cominfo">{COMMENTS_PAGES_INFO}</span> <span class="leave">{<a href="
<!-- IF {PHP.pag.page_id} -->
page.php?id={PHP.pag.page_id}
<!-- ELSE -->
polls.php?id={PHP.id}
<!-- ENDIF -->
#post" title="{PHP.skinlang.comments.leave}"><strong>+</strong></a>}</span></h2>

<!-- IF {COMMENTS_DISPLAY} == false -->
<div style="display:none">
<!-- ELSE -->
<div class="commentlist">
<!-- ENDIF -->

<!-- BEGIN: COMMENTS_ROW -->
<a id="c{COMMENTS_ROW_ID}" name="c{COMMENTS_ROW_ID}"></a>&nbsp;
<div id="comment-{COMMENTS_ROW_ID}">
<!-- IF {COMMENTS_ROW_AUTHORID} == {PHP.pag.page_ownerid} -->
<span class="commenttext-owner"><span class="comav-owner">
<!-- ELSE -->
<span class="commenttext"><span class="comav">
<!-- ENDIF -->
	<a href="users.php?m=details&amp;id={COMMENTS_ROW_AUTHORID}">{COMMENTS_ROW_AVATAR}</a></span>
	<cite>{COMMENTS_ROW_AUTHOR}</cite>
	<span style="font-size:10px; color:#93adca">
		<span class="colright" style="margin-top:1px"> &nbsp; {COMMENTS_ROW_ADMIN} &nbsp; {COMMENTS_ROW_EDIT}</span>
		said on <span class="time"><a href="{COMMENTS_ROW_URL}">{COMMENTS_ROW_DATE}</a></span>
	</span><br />
	{COMMENTS_ROW_TEXT}
</span>
<br class="clear" />
</div>

<!-- END: COMMENTS_ROW -->
</div>

<div class="comments">
<!-- BEGIN: PAGNAVIGATOR -->
<div class="pagnav">{COMMENTS_PAGES_PAGESPREV} {COMMENTS_PAGES_PAGNAV} {COMMENTS_PAGES_PAGESNEXT}</div>
<!-- END: PAGNAVIGATOR -->

<!-- BEGIN: COMMENTS_EMPTY -->
<div class="padding10 red">{PHP.skinlang.comments.no}</div>
<!-- END: COMMENTS_EMPTY -->

<!-- BEGIN: COMMENTS_ERROR -->
<div class="error">{COMMENTS_ERROR_BODY}</div>
<!-- END: COMMENTS_ERROR -->

&nbsp;<a name="post" id="post"></a>
<!-- IF {PHP.usr.id} == 0 -->
<p><a href="users.php?m=auth" class="comm"><span>{PHP.L.Login} {PHP.skinlang.forumspost.to} {PHP.L.Comment}</span></a></p>
<!-- ENDIF -->

<!-- BEGIN: COMMENTS_NEWCOMMENT -->
<form action="{COMMENTS_FORM_SEND}" method="post">
	<h2>{PHP.skinlang.comments.Comment}</h2>
	<div style="width:100%;">{COMMENTS_FORM_TEXTBOXER}<br />{COMMENTS_FORM_HINT}</div>
	<p><input type="submit" value="{PHP.L.Submit}" class="submit" /></p>
</form>
<!-- END: COMMENTS_NEWCOMMENT -->

<!-- BEGIN: COMMENTS_CLOSED -->
<div class="error">{COMMENTS_CLOSED}</div>
<!-- END: COMMENTS_CLOSED -->

</div>
<!-- END: COMMENTS -->