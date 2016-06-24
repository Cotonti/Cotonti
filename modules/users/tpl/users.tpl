<!-- BEGIN: MAIN -->

		<div class="block">
			<h2 class="users">{USERS_TITLE}</h2>
			<table class="cells">
				<tr>
					<td class="coltop" class="width5">{USERS_TOP_PM}</td>
					<td class="coltop" class="width20">{USERS_TOP_NAME}</td>
					<td class="coltop" class="width20">{USERS_TOP_GRPTITLE}</td>
					<td class="coltop" class="width15">{USERS_TOP_GRPLEVEL}</td>
					<td class="coltop" class="width15">{USERS_TOP_COUNTRY}</td>
					<td class="coltop" class="width25">{USERS_TOP_REGDATE}</td>
				</tr>
<!-- BEGIN: USERS_ROW -->
				<tr>
					<td class="centerall">{USERS_ROW_PM}</td>
					<td>{USERS_ROW_NAME}&nbsp;{USERS_ROW_TAG}</td>
					<td>{USERS_ROW_MAINGRP}</td>
					<td class="centerall">{USERS_ROW_MAINGRPSTARS}</td>
					<td class="centerall">{USERS_ROW_COUNTRYFLAG} {USERS_ROW_COUNTRY}</td>
					<td class="centerall">{USERS_ROW_REGDATE}</td>
				</tr>
<!-- END: USERS_ROW -->
			</table>
		</div>
		<div class="block">
			<h2 class="prefs">{PHP.L.Filters}</h2>
			<form action="{USERS_TOP_FILTER_ACTION}" method="post">
				<h3>{PHP.L.Filter_search}:</h3>
				{USERS_TOP_FILTERS_COUNTRY}
				{USERS_TOP_FILTERS_MAINGROUP}
				{USERS_TOP_FILTERS_GROUP}
				<h3>{PHP.L.Username_search}:</h3>
				{USERS_TOP_FILTERS_SEARCH}
				<button type="submit" class="submit">{PHP.L.Submit}</button>
			</form>
		</div>
		<p class="paging"><span>{PHP.L.users_usersperpage}: {USERS_TOP_MAXPERPAGE}</span><span>{PHP.L.users_usersinthissection}: {USERS_TOP_TOTALUSERS}</span>{USERS_TOP_PAGEPREV}{USERS_TOP_PAGNAV}{USERS_TOP_PAGENEXT}</p>

<!-- END: MAIN -->