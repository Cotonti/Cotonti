<!-- BEGIN: MAIN -->

	<div class="mboxHD">
	<div class="rss-icon-title">
	<a href="{FORUMS_POSTS_RSS}"><img src="skins/{PHP.skin}/img/rss-icon.png" border="0" alt="" /></a>
	</div>{FORUMS_POSTS_PAGETITLE}</div>
	<div class="mboxBody">

		<div style="float:right;">{FORUMS_POSTS_JUMPBOX}</div>

		<div id="subtitle">{FORUMS_POSTS_SUBTITLE}</div>

		<!-- BEGIN: FORUMS_POSTS_TOPICPRIVATE -->
		<div class="error">{PHP.skinlang.forumspost.privatetopic}</div>
		<!-- END: FORUMS_POSTS_TOPICPRIVATE -->
		<div class="paging">
			{FORUMS_POSTS_PAGEPREV} {FORUMS_POSTS_PAGENEXT} {FORUMS_POSTS_PAGES}

		</div>

	<!-- BEGIN: POLLS_VIEW -->
	{POLLS_TITLE}
	{POLLS_FORM}
	<!-- END: POLLS_VIEW -->

		<div class="tCap"></div><table class="cells" border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td class="coltop" style="width:128px;">{PHP.skinlang.forumspost.Author}</td>
				<td class="coltop">{PHP.skinlang.forumspost.Message}</td>
			</tr>

			<!-- BEGIN: FORUMS_POSTS_ROW -->
			<tr>
				<td style="width:176px;" rowspan="3" class="{FORUMS_POSTS_ROW_ODDEVEN}">
					{FORUMS_POSTS_ROW_ANCHORLINK}
					<h2 style="margin:2px;">{FORUMS_POSTS_ROW_POSTERNAME}</h2>
					{FORUMS_POSTS_ROW_AVATAR}

					<div style="padding:5px;">
						{FORUMS_POSTS_ROW_MAINGRP}<br />
						{FORUMS_POSTS_ROW_MAINGRPSTARS}<br />
						<img src="skins/{PHP.skin}/img/online{FORUMS_POSTS_ROW_USERONLINE}.gif" title="{PHP.skinlang.forumspost.Onlinestatus}: {FORUMS_POSTS_ROW_USERONLINETITLE}" alt="" />
					</div>
					<div style="padding:5px;">{FORUMS_POSTS_ROW_COUNTRYFLAG}</div>
					<div style="padding:5px;">
						{FORUMS_POSTS_ROW_POSTCOUNT} {PHP.skinlang.forumspost.posts}<br />
						{FORUMS_POSTS_ROW_WEBSITE}<br />
						{PHP.skinlang.forumspost.Location} {FORUMS_POSTS_ROW_COUNTRY} {FORUMS_POSTS_ROW_LOCATION}<br />
						{PHP.skinlang.forumspost.Occupation} {FORUMS_POSTS_ROW_OCCUPATION}<br />
						{PHP.skinlang.forumspost.Age} {FORUMS_POSTS_ROW_AGE}
					</div>

				</td>

		    	<td style="height:24px; max-height:40px; text-align:right;" class="{FORUMS_POSTS_ROW_ODDEVEN}">
		    	#{FORUMS_POSTS_ROW_IDURL} &nbsp;
		    	{FORUMS_POSTS_ROW_CREATION} &nbsp; {FORUMS_POSTS_ROW_POSTERIP} &nbsp; {FORUMS_POSTS_ROW_ADMIN}
		    	</td>
		 	</tr>
			<tr>
				<td style="padding:8px; height:100%;" class="{FORUMS_POSTS_ROW_ODDEVEN}">

				<div class="fmsg" style="width:550px; overflow-x:auto; overflow-y:visible; margin-bottom:8px;">
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

		</table><div class="bCap"></div>

		<div class="paging">{FORUMS_POSTS_PAGEPREV}  {FORUMS_POSTS_PAGENEXT} {FORUMS_POSTS_PAGES}</div>

		<!-- BEGIN: FORUMS_POSTS_TOPICLOCKED -->
		<div class="error">{FORUMS_POSTS_TOPICLOCKED_BODY}</div>
		<!-- END: FORUMS_POSTS_TOPICLOCKED -->

		<!-- BEGIN: FORUMS_POSTS_ANTIBUMP -->
		<div>{FORUMS_POSTS_ANTIBUMP_BODY}</div>
		<!-- END: FORUMS_POSTS_ANTIBUMP -->

		<!-- BEGIN: FORUMS_POSTS_NEWPOST -->
		<form action="{FORUMS_POSTS_NEWPOST_SEND}" method="post" name="newpost">
			<div style="width:100%;">{FORUMS_POSTS_NEWPOST_TEXTBOXER}</div>
			<div class="valid"><input type="submit" value="{PHP.skinlang.forumspost.Reply}" /></div>
		</form>
		<!-- END: FORUMS_POSTS_NEWPOST -->

	</div>

<!-- END: MAIN -->