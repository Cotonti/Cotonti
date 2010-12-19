<!-- BEGIN: COMMENTS -->

				&nbsp;<a name="com" id="com"></a>
				<h2>{PHP.L.Comments} <span class="cominfo">{COMMENTS_PAGES_INFO}</span> 

				<span class="leave"><a href="
				<!-- IF {PHP.pag.page_id} -->
				page.php?id={PHP.pag.page_id}
				<!-- ELSE -->
				polls.php?id={PHP.id}
				<!-- ENDIF -->
				#post" title="{PHP.themelang.comments.Comment}">
				<!-- IF {PHP.usr.id} > 0 -->
				<strong>{+}</strong>
				<!-- ENDIF -->
				</a></span></h2>

				<!-- IF {COMMENTS_DISPLAY} == 0 -->
				<div class="none">
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

						<span class="cominfo">
							<span class="fright"> &nbsp; {COMMENTS_ROW_ADMIN} &nbsp; {COMMENTS_ROW_EDIT}</span> 
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
					<div class="padding10 red">{PHP.themelang.comments.no}</div>
					<!-- END: COMMENTS_EMPTY -->

					<!-- BEGIN: COMMENTS_ERROR -->
					<div class="error">{COMMENTS_ERROR_BODY}</div>
					<!-- END: COMMENTS_ERROR -->

					&nbsp;<a name="post" id="post"></a>
					<!-- IF {PHP.usr.id} == 0 -->
					<p><a href="users.php?m=auth" class="comm"><span>{PHP.L.Login} {PHP.themelang.forumspost.to} {PHP.L.Comment}</span></a></p>
					<!-- ENDIF -->

					<!-- BEGIN: COMMENTS_NEWCOMMENT -->
					<form action="{COMMENTS_FORM_SEND}" method="post">
					<h2>{PHP.themelang.comments.Comment}</h2>
					<div style="width:100%;">{COMMENTS_FORM_TEXTBOXER}<br />{COMMENTS_FORM_HINT}</div>
					<p><input type="submit" value="{PHP.L.Submit}" class="submit" /></p>
					</form>
					<!-- END: COMMENTS_NEWCOMMENT -->

					<!-- BEGIN: COMMENTS_CLOSED -->
					<div class="error">{COMMENTS_CLOSED}</div>
					<!-- END: COMMENTS_CLOSED -->

				</div>

<!-- END: COMMENTS -->