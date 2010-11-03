<!-- BEGIN: MAIN -->

		<div class="block">
			<h2 class="users"><a href="plug.php?e=whosonline">{PHP.L.WhosOnline}</a></h2>
			<table class="cells">
				<thead>
					<tr>
						<th>{PHP.L.User}</th>
						<th>{PHP.L.Group}</th>
						<th>{PHP.L.Location}</th>
						<th>{PHP.L.LastSeen}</th>
						<th>{PHP.L.IPaddress}</th>
					</tr>
				</thead>
				<tbody>
					<!-- BEGIN: USERS -->
					<tr>
						<td>{USER_LINK}</td>
						<td>{USER_MAINGRP}</td>
						<td>{USER_LOCATION} {USER_SUBLOCATION}</td>
						<td>{USER_LASTSEEN}</td>
						<td>{USER_IP}</td>
					</tr>
					<!-- END: USERS -->
					<!-- BEGIN: GUESTS -->
					<tr>
						<td colspan="2">{PHP.L.Visitor} #{GUEST_NUMBER}</td>
						<td>{GUEST_LOCATION} {GUEST_SUBLOCATION}</td>
						<td>{GUEST_LASTSEEN}</td>
						<td>{GUEST_IP}</td>
					</tr>
					<!-- END: GUESTS -->
				</tbody>
			</table>
			<p><strong>{PHP.L.NowOnline}:</strong> {STAT_COUNT_USERS} {USERS}, {STAT_COUNT_GUESTS} {GUESTS}</p>
			<!-- IF {STAT_MAXUSERS} --><p><strong>{PHP.L.MostOnline}:</strong> {STAT_MAXUSERS}</p><!-- ENDIF -->
		</div>

<!-- END: MAIN -->