<!-- BEGIN: MAIN -->

<div id="title">

	{FORUMS_POSTS_PAGETITLE}<br />
	<span class="desc">{FORUMS_POSTS_TOPICDESC}</span>
	
</div>

<div id="subtitle">

	{FORUMS_POSTS_SUBTITLE}

</div>

<div id="main">

<!-- BEGIN: FORUMS_POSTS_TOPICPRIVATE -->

<div class="error">

	{PHP.skinlang.forumspost.privatetopic}

</div>

<!-- END: FORUMS_POSTS_TOPICPRIVATE -->

<table class="paging">

	<tr>
		<td class="paging_left">{FORUMS_POSTS_PAGEPREV}</td>
		<td class="paging_center">{FORUMS_POSTS_PAGES}</td>
		<td class="paging_right">{FORUMS_POSTS_PAGENEXT}</td>
	</tr>
	
</table>

<table class="cells">

	<tr>
		<td class="coltop" style="width:160px;">{PHP.skinlang.forumspost.Author}</td>
		<td class="coltop">{PHP.skinlang.forumspost.Message}</td>
	</tr>

	<!-- BEGIN: FORUMS_POSTS_ROW -->

	<tr>
		<td rowspan="3" class="{FORUMS_POSTS_ROW_ODDEVEN}">

			<h2 style="margin:2px;">{FORUMS_POSTS_ROW_POSTERNAME}</h2>
			{FORUMS_POSTS_ROW_AVATAR}

			<p>
				{FORUMS_POSTS_ROW_MAINGRP}<br />
				{FORUMS_POSTS_ROW_MAINGRPSTARS}<br />
				{FORUMS_POSTS_ROW_USEREXTRA2}<br />
				<img src="skins/{PHP.skin}/img/online{FORUMS_POSTS_ROW_USERONLINE}.gif" alt="{PHP.skinlang.forumspost.Onlinestatus}"><br />
				{FORUMS_POSTS_ROW_COUNTRYFLAG}
			</p>

			<p>
				{FORUMS_POSTS_ROW_POSTCOUNT} {PHP.skinlang.forumspost.posts}<br />
				{FORUMS_POSTS_ROW_WEBSITE}<br />
				{PHP.skinlang.forumspost.Location} {FORUMS_POSTS_ROW_COUNTRY} {FORUMS_POSTS_ROW_LOCATION}<br />
				{PHP.skinlang.forumspost.Occupation} {FORUMS_POSTS_ROW_OCCUPATION}<br />
				{PHP.skinlang.forumspost.Age} {FORUMS_POSTS_ROW_AGE}
			</p>

		</td>

    	<td style="height:24px; max-height:40px; text-align:right;" class="{FORUMS_POSTS_ROW_ODDEVEN}">
    	#{FORUMS_POSTS_ROW_IDURL} &nbsp;
    	{FORUMS_POSTS_ROW_CREATION} &nbsp; {FORUMS_POSTS_ROW_POSTERIP} &nbsp; {FORUMS_POSTS_ROW_ADMIN}
    	</td>
 	</tr>

	<tr>
		<td style="padding:8px; height:100%;" class="{FORUMS_POSTS_ROW_ODDEVEN}">

		<div style="width:510px; overflow-x:auto; overflow-y:visible; margin-bottom:8px;">
			{FORUMS_POSTS_ROW_TEXT}
		</div>

		{FORUMS_POSTS_ROW_UPDATEDBY}
		</td>
	</tr>

	<tr>
		<td style="padding:5px; vertical-align:bottom;" class="{FORUMS_POSTS_ROW_ODDEVEN}">
		<div class="signature">{FORUMS_POSTS_ROW_USERTEXT}</div>
		</td>
	</tr>


	<!-- END: FORUMS_POSTS_ROW -->

</table>

<table class="paging">

	<tr>
		<td class="paging_left">{FORUMS_POSTS_PAGEPREV}</td>
		<td class="paging_center">{FORUMS_POSTS_PAGES}</td>
		<td class="paging_right">{FORUMS_POSTS_PAGENEXT}</td>
	</tr>
	
</table>


<!-- BEGIN: FORUMS_POSTS_TOPICLOCKED -->

<div class="error">

	{FORUMS_POSTS_TOPICLOCKED_BODY}

</div>

<!-- END: FORUMS_POSTS_TOPICLOCKED -->

<!-- BEGIN: FORUMS_POSTS_ANTIBUMP -->

<div>

	{FORUMS_POSTS_ANTIBUMP_BODY}

</div>

<!-- END: FORUMS_POSTS_ANTIBUMP -->

<!-- BEGIN: FORUMS_POSTS_NEWPOST -->

<form action="{FORUMS_POSTS_NEWPOST_SEND}" method="post" name="newpost">

	<div>{FORUMS_POSTS_NEWPOST_TEXTBOXER}</div>

	<div class="valid">
	<input type="submit" value="{PHP.skinlang.forumspost.Reply}">
	</div>

</form>

<!-- END: FORUMS_POSTS_NEWPOST -->

</div>

<div class="paging">

{FORUMS_POSTS_JUMPBOX}

</div>

<!-- END: MAIN -->
