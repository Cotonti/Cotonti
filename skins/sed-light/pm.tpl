<!-- BEGIN: MAIN -->

	<div class="mboxHD">{PM_PAGETITLE}</div>
	<div class="mboxBody">
	
		<div id="subtitle">{PM_SUBTITLE}</div>

		<div class="paging">{PM_INBOX} &nbsp; &nbsp; {PM_ARCHIVES} &nbsp; &nbsp; {PM_SENTBOX} &nbsp; &nbsp; {PM_SENDNEWPM}</div>

		<div class="tCap"></div>
		<table class="cells" border="0" cellspacing="1" cellpadding="2">

			<!-- BEGIN: PM_TITLE -->
			<tr>
				<td class="coltop" style="width:16px;">{PHP.skinlang.pm.State}</td>
				<td class="coltop">{PHP.skinlang.pm.Sender}</td>
				<td class="coltop">{PHP.skinlang.pm.Subject}</td>
				<td class="coltop" style="width:176px;">{PHP.skinlang.pm.Date}</td>
				<td class="coltop" style="width:72px;">{PHP.skinlang.pm.Action}</td>
			</tr>
			<!-- END: PM_TITLE -->

			<!-- BEGIN: PM_TITLE_SENTBOX -->
			<tr>
				<td class="coltop" style="width:16px;">{PHP.skinlang.pm.State}</td>
				<td class="coltop">{PHP.skinlang.pm.Recipient}</td>
				<td class="coltop">{PHP.skinlang.pm.Subject}</td>
				<td class="coltop" style="width:176px; text-align:center;">{PHP.skinlang.pm.Date}</td>
				<td class="coltop" style="width:72px; text-align:center;">{PHP.skinlang.pm.Action}</td>
			</tr>
			<!-- END: PM_TITLE_SENTBOX -->

			<!-- BEGIN: PM_ROW -->
			<tr>
				<td style="width:16px;" class="centerall {PM_ROW_ODDEVEN}">{PM_ROW_ICON_STATUS}</td>
				<td class="{PM_ROW_ODDEVEN}">{PM_ROW_FROMORTOUSER}</td>
				<td class="{PM_ROW_ODDEVEN}">{PM_ROW_TITLE}</td>
				<td style="width:112px;" class="centerall {PM_ROW_ODDEVEN}">{PM_ROW_DATE}</td>
				<td style="width:112px;" class="centerall {PM_ROW_ODDEVEN}">{PM_ROW_ICON_ACTION}</td>
			 </tr>
			<!-- END: PM_ROW -->

			<!-- BEGIN: PM_ROW_EMPTY -->
			<tr>
				<td colspan="5" style="padding:16px;">{PHP.skinlang.pm.Nomessages}</td>
			</tr>
			<!-- END: PM_ROW_EMPTY -->

		</table>
		<div class="bCap"></div>

		<!-- BEGIN: PM_FOOTER -->
		<div class="paging">{PM_TOP_PAGEPREV}&nbsp;{PM_TOP_PAGES}&nbsp;{PM_TOP_PAGENEXT}</div>
		<!-- END: PM_FOOTER -->

		<!-- BEGIN: PM_DETAILS -->
		<div>

			{PHP.skinlang.pm.Subject} <strong>{PM_ROW_TITLE}</strong><br />
			{PHP.skinlang.pm.Sender} {PM_ROW_FROMUSER}<br />
			{PHP.skinlang.pm.Recipient} {PM_ROW_TOUSER}<br />
			{PHP.skinlang.pm.Date} {PM_ROW_DATE}

			<hr />

			<p>{PM_ROW_TEXT}</p>

			<div class="paging">{PM_ROW_ICON_ACTION}</div>

		</div>
		<!-- END: PM_DETAILS -->

		<div class="paging">
			<img src="skins/{PHP.skin}/img/system/icon-pm-new.gif" alt="" />: {PHP.skinlang.pm.Newmessage} &nbsp; &nbsp;
			<img src="skins/{PHP.skin}/img/system/icon-pm.gif" alt="" />: {PHP.skinlang.pm.Message} &nbsp; &nbsp;
			<img src="skins/{PHP.skin}/img/system/icon-pm-reply.gif" alt="" />: {PHP.skinlang.pm.Reply} &nbsp; &nbsp;
			<img src="skins/{PHP.skin}/img/system/icon-pm-archive.gif" alt="" />: {PHP.skinlang.pm.Sendtoarchives} &nbsp; &nbsp;
			<img src="skins/{PHP.skin}/img/system/icon-pm-trashcan.gif" alt="" />: {PHP.skinlang.pm.Delete}
		</div>

	</div>

<!-- END: MAIN -->