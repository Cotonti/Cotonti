<!-- BEGIN: MAIN -->

	<div class="mboxHD">{USERS_TITLE}</div>
	<div class="mboxBody">
	
		<div id="subtitle">
{USERS_TOP_FILTERS_COUNTRY} {USERS_TOP_FILTERS_MAINGROUP} {USERS_TOP_FILTERS_GROUP} {USERS_TOP_FILTERS_SEARCH}
	{USERS_TOP_FILTERS_OTHERS}
</div>
		
		<div class="paging">{USERS_TOP_PAGEPREV} &nbsp; {USERS_TOP_PAGENEXT}  &nbsp; {PHP.skinlang.users.Page} {USERS_TOP_CURRENTPAGE}/ {USERS_TOP_TOTALPAGE} - {USERS_TOP_MAXPERPAGE} {PHP.skinlang.users.usersperpage} - {USERS_TOP_TOTALUSERS} {PHP.skinlang.users.usersinthissection}</div>

		<div class="tCap"></div>
		<table class="cells" border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td class="coltop" style="width:20px;">{USERS_TOP_PM}</td>
				<td class="coltop">{USERS_TOP_NAME}</td>
				<td class="coltop" style="width:224px;" colspan="2">{USERS_TOP_MAINGRP}</td>
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

		<div class="paging">{USERS_TOP_PAGEPREV} &nbsp; {USERS_TOP_PAGENEXT}  &nbsp; {PHP.skinlang.users.Page} {USERS_TOP_CURRENTPAGE}/ {USERS_TOP_TOTALPAGE} - {USERS_TOP_MAXPERPAGE} {PHP.skinlang.users.usersperpage} - {USERS_TOP_TOTALUSERS} {PHP.skinlang.users.usersinthissection}</div>
	
	</div>

<!-- END: MAIN -->