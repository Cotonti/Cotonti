<!-- BEGIN: MAIN -->

<div id="content">
	<div class="padding20">
		<div id="left">
			<h1>{COMMENTS_TITLE}</h1>


			{FILE "./{PHP.cfg.themes_dir}/{PHP.theme}/warnings.tpl"}

			<!-- BEGIN: COMMENTS_FORM_EDIT -->
					<form id="comments" name="comments" action="{COMMENTS_FORM_POST}" method="post">
						<fieldset>
							<legend>{COMMENTS_TITLE}</legend>
							<div>
								<label>{COMMENTS_POSTER_TITLE}</label>
								{COMMENTS_POSTER}
							</div>
							<div>
								<label>{COMMENTS_IP_TITLE}</label>
								{COMMENTS_IP}
							</div>
							<div>
								<label>{COMMENTS_DATE_TITLE}</label>
								{COMMENTS_DATE}
							</div>
							<div>
								{COMMENTS_FORM_TEXT}
								<input type="submit" class="submit" value="{COMMENTS_FORM_UPDATE_BUTTON}">
							</div>
						</fieldset>
					</form>
			<!-- END: COMMENTS_FORM_EDIT -->
		</div>
	</div>
</div>
<br class="clear" />
<!-- END: MAIN -->

<!-- BEGIN: COMMENTS -->
&nbsp;
<a name="com" id="com"></a>
<h2>{PHP.L.comments_comments} <span class="cominfo">{COMMENTS_PAGES_INFO}</span> <span class="leave">{<a href="
<!-- IF {PHP.pag.page_id} -->
{PHP.pag.page_id|cot_url('page','id=$this')}
<!-- ELSE -->
{PHP.id|cot_url('polls','id=$this')}
<!-- ENDIF -->
#post" title="{PHP.L.comments_comments}"><strong>+</strong></a>}</span></h2>

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
<!-- IF {COMMENTS_ROW_AUTHORID} == 0 -->
<img src="/datas/defaultav/blank.png" alt="Guest" /></span>
<!-- ELSE -->
<a href="{COMMENTS_ROW_AUTHORID|cot_url('users','m=details&id=$this')}">{COMMENTS_ROW_AUTHOR_AVATAR}</a></span>
<!-- ENDIF -->	
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
<p class="paging">{COMMENTS_PAGES_PAGESPREV}{COMMENTS_PAGES_PAGNAV}{COMMENTS_PAGES_PAGESNEXT}</p>
<p class="paging"><span>{COMMENTS_PAGES_INFO}</span></p>
<!-- END: PAGNAVIGATOR -->

<!-- BEGIN: COMMENTS_EMPTY -->
<div class="padding10 red">{COMMENTS_EMPTYTEXT}</div>
<!-- END: COMMENTS_EMPTY -->

&nbsp;
<!-- BEGIN: COMMENTS_NEWCOMMENT -->
<a name="post" id="post"></a>
<form action="{COMMENTS_FORM_SEND}" method="post">
	<h2>{PHP.L.Newcomment}</h2>
		{FILE "./{PHP.cfg.themes_dir}/{PHP.theme}/warnings.tpl"}
		<!-- BEGIN: GUEST -->
	<div style="width:100%;">	{PHP.L.Name}: {COMMENTS_FORM_AUTHOR}</div>
		<!-- END: GUEST -->
	<div style="width:100%;">{COMMENTS_FORM_TEXT}<br />{COMMENTS_FORM_HINT}</div>
	<!-- IF {PHP.usr.id} == 0 AND {COMMENTS_FORM_VERIFYIMG} -->
	<div style="width:100%;">	{COMMENTS_FORM_VERIFYIMG} : {COMMENTS_FORM_VERIFY}</div>
	<!-- ENDIF -->
	<p><input type="submit" value="{PHP.L.Submit}" class="submit" /></p>
</form>
<!-- END: COMMENTS_NEWCOMMENT -->

<!-- BEGIN: COMMENTS_CLOSED -->
<div class="error">{COMMENTS_CLOSED}</div>
<!-- END: COMMENTS_CLOSED -->

</div>


<!-- END: COMMENTS -->