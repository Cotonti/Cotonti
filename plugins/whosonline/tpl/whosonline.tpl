<!-- BEGIN: MAIN -->

		<div class="block">
			<h2 class="users"><a href="plug.php?e=whosonline">{WHOSONLINE_TITLE}</a></h2>

<!-- BEGIN: NOT_EMPTY -->
			<table class="cells">
			<tr>
				<td class="coltop">{PHP.L.Avatar}</td>
				<td class="coltop">{PHP.L.User}</td>
				<td class="coltop">{PHP.L.Group}</td>
				<td class="coltop">{PHP.L.Country}</td>
				<td class="coltop">{PHP.L.Location}</td>
				<td class="coltop">{PHP.L.plu_lastseen1}</td>
<!-- BEGIN: IS_ADMIN -->
				<td class="coltop">{WHOSONLINE_IN}</td>
				<td class="coltop">{WHOSONLINE_IP}</td>
<!-- END: IS_ADMIN -->
			</tr>
<!-- BEGIN: WHOSONLINE_ROW1 -->
			<tr>
				<td class="centerall">{WHOSONLINE_ROW1_SHOWAVATARS}</td>
				<td>{WHOSONLINE_ROW1_USER}</td>
				<td><a href="{WHOSONLINE_ROW1_USER_MAINGRP_URL}" rel="nofollow">{WHOSONLINE_ROW1_USER_MAINGRP_TITLE}</a></td>
				<td class="centerall">{WHOSONLINE_ROW1_USER_COUNTRYFLAG}</td>
				<td class="centerall">{WHOSONLINE_ROW1_USER_LOCATION}</td>
				<td>{WHOSONLINE_ROW1_USER_ONLINE_LASTSEEN}</td>
<!-- BEGIN: WHOSONLINE_ROW1_IS_ADMIN -->
				<td>{WHOSONLINE_ROW1_USER_ONLINE_LOCATION}</td>
				<td class="centerall"><a href="admin.php?m=tools&amp;p=ipsearch&amp;a=search&amp;id={WHOSONLINE_ROW1_USER_ONLINE_IP}&amp;{PHP.x}">{WHOSONLINE_ROW1_USER_ONLINE_IP}</a></td>
<!-- END: WHOSONLINE_ROW1_IS_ADMIN -->
			</tr>
<!-- END: WHOSONLINE_ROW1 -->
<!-- BEGIN: WHOSONLINE_ROW2 -->
			<tr>
				<td>{WHOSONLINE_ROW2_SHOWAVATARS}</td>
				<td colspan="4">{WHOSONLINE_ROW2_USER}</td>
				<td>{WHOSONLINE_ROW2_USER_ONLINE_LASTSEEN}</td>
<!-- BEGIN: WHOSONLINE_ROW2_IS_ADMIN -->
				<td>{WHOSONLINE_ROW2_USER_ONLINE_LOCATION}</td>
				<td class="centerall"><a href="admin.php?m=tools&amp;p=ipsearch&amp;a=search&amp;id={WHOSONLINE_ROW2_USER_ONLINE_IP}&amp;{PHP.x}">{WHOSONLINE_ROW2_USER_ONLINE_IP}</a></td>
<!-- END: WHOSONLINE_ROW2_IS_ADMIN -->
			</tr>
<!-- END: WHOSONLINE_ROW2 -->
			</table>
<!-- END: NOT_EMPTY -->

			<p class="paging"><span class="a1">{PHP.L.plu_mostonline}: {WHOSONLINE_MAXUSERS}</span> <span class="a1">{PHP.L.plu_therescurrently}: {WHOSONLINE_VISITORS} {WHOSONLINE_TEXTVISITORS} {WHOSONLINE_MEMBERS} {WHOSONLINE_TEXTMEMBERS}</span></p>

		</div>

<!-- END: MAIN -->