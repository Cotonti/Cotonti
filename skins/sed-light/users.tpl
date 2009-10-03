<!-- BEGIN: MAIN -->

	<div class="mboxHD">{USERS_TITLE}</div>
	<div class="mboxBody">

		<div id="subtitle">
			{USERS_TOP_FILTERS_COUNTRY} {USERS_TOP_FILTERS_MAINGROUP} {USERS_TOP_FILTERS_GROUP} {USERS_TOP_FILTERS_SEARCH} {USERS_TOP_FILTERS_OTHERS}
		</div>

		<div class="paging">{USERS_TOP_PAGEPREV}{USERS_TOP_PAGNAV}{USERS_TOP_PAGENEXT} &nbsp; {PHP.skinlang.users.usersperpage}: {USERS_TOP_MAXPERPAGE} {PHP.cfg.separator} {PHP.skinlang.users.usersinthissection}: {USERS_TOP_TOTALUSERS}</div>

		<div class="tCap"></div>
		<table class="cells">
			<tr>
				<td class="coltop" style="width:20px;">{USERS_TOP_PM}</td>
				<td class="coltop">{USERS_TOP_NAME}</td>
				<td class="coltop" style="width:128px;">{USERS_TOP_GRPTITLE}</td>
				<td class="coltop" style="width:128px;">{USERS_TOP_GRPLEVEL}</td>
				<td class="coltop" style="width:128px;">{USERS_TOP_COUNTRY}</td>
				<td class="coltop" style="width:112px;">{USERS_TOP_REGDATE}</td>
			</tr>

			<!-- BEGIN: USERS_ROW -->
			<tr>
				<td>{USERS_ROW_PM}</td>
				<td>{USERS_ROW_NAME}&nbsp;{USERS_ROW_TAG}</td>
				<td>{USERS_ROW_MAINGRP}</td>
				<td>{USERS_ROW_MAINGRPSTARS}</td>
				<td>{USERS_ROW_COUNTRYFLAG} {USERS_ROW_COUNTRY}</td>
				<td>{USERS_ROW_REGDATE}</td>
			</tr>
			<!-- END: USERS_ROW -->

		</table>
		<div class="bCap"></div>

		<div class="paging">{USERS_TOP_PAGEPREV}{USERS_TOP_PAGNAV}{USERS_TOP_PAGENEXT} &nbsp; {PHP.skinlang.users.usersperpage}: {USERS_TOP_MAXPERPAGE} {PHP.cfg.separator} {PHP.skinlang.users.usersinthissection}: {USERS_TOP_TOTALUSERS}</div>

	</div>

<!-- END: MAIN -->