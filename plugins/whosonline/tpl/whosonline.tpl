<!-- BEGIN: MAIN -->

		<div class="block">
			<h2 class="users"><a href="plug.php?e=whosonline">{WHOSONlINE_TITLE}</a></h2>

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
				<td class="coltop">{WHOSONlINE_IN}</td>
				<td class="coltop">{WHOSONlINE_IP}</td>
<!-- END: IS_ADMIN -->
			</tr>
<!-- BEGIN: WHOSONlINE_ROW1 -->
			<tr>
				<td class="centerall">{WHOSONlINE_ROW1_SHOWAVATARS}</td>
				<td>{WHOSONlINE_ROW1_USER}</td>
				<td><a href="{WHOSONlINE_ROW1_USER_MAINGRP_URL}" rel="nofollow">{WHOSONlINE_ROW1_USER_MAINGRP_TITLE}</a></td>
				<td class="centerall">{WHOSONlINE_ROW1_USER_COUNTRYFLAG}</td>
				<td class="centerall">{WHOSONlINE_ROW1_USER_LOCATION}</td>
				<td>{WHOSONlINE_ROW1_USER_ONLINE_LASTSEEN}</td>
<!-- BEGIN: WHOSONlINE_ROW1_IS_ADMIN -->
				<td>{WHOSONlINE_ROW1_USER_ONLINE_LOCATION}</td>
				<td class="centerall"><a href="admin.php?m=tools&amp;p=ipsearch&amp;a=search&amp;id={WHOSONlINE_ROW1_USER_ONLINE_IP}&amp;{PHP.x}">{WHOSONlINE_ROW1_USER_ONLINE_IP}</a></td>
<!-- END: WHOSONlINE_ROW1_IS_ADMIN -->
			</tr>
<!-- END: WHOSONlINE_ROW1 -->
<!-- BEGIN: WHOSONlINE_ROW2 -->
			<tr>
				<td>{WHOSONlINE_ROW2_SHOWAVATARS}</td>
				<td colspan="4">{WHOSONlINE_ROW2_USER}</td>
				<td>{WHOSONlINE_ROW2_USER_ONLINE_LASTSEEN}</td>
<!-- BEGIN: WHOSONlINE_ROW2_IS_ADMIN -->
				<td>{WHOSONlINE_ROW2_USER_ONLINE_LOCATION}</td>
				<td class="centerall"><a href="admin.php?m=tools&amp;p=ipsearch&amp;a=search&amp;id={WHOSONlINE_ROW2_USER_ONLINE_IP}&amp;{PHP.x}">{WHOSONlINE_ROW2_USER_ONLINE_IP}</a></td>
<!-- END: WHOSONlINE_ROW2_IS_ADMIN -->
			</tr>
<!-- END: WHOSONlINE_ROW2 -->
			</table>
<!-- END: NOT_EMPTY -->

			<p class="paging"><span class="a1">{PHP.L.plu_mostonline}: {WHOSONlINE_MAXUSERS}</span> <span class="a1">{PHP.L.plu_therescurrently}: {WHOSONlINE_VISITORS} {WHOSONlINE_TEXTVISITORS} {WHOSONlINE_MEMBERS} {WHOSONlINE_TEXTMEMBERS}</span></p>

		</div>

<!-- END: MAIN -->