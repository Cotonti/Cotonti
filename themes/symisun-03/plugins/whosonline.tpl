<!-- BEGIN: MAIN -->

<div id="content">
  <div class="padding20">
    <h1>{PHP.L.WhosOnline}</h1>		
	<p><strong>{PHP.L.NowOnline}:</strong> {STAT_COUNT_USERS} {USERS}, {STAT_COUNT_GUESTS} {GUESTS}</p>		
	<!-- IF {STAT_MAXUSERS} -->
	<p><strong>{PHP.L.MostOnline}:</strong> {STAT_MAXUSERS}</p>
	<!-- ENDIF -->		
	<br />		
	<table class="cells">				
		<thead>					
			<tr style="color:#999">						
				<th>{PHP.L.User}</th>						
				<th>{PHP.L.Group}</th>						
				<th>{PHP.L.Location}</th>						
				<th>{PHP.L.LastSeen}</th>						
				<!-- IF {PHP.usr.isadmin} -->
				<th>{PHP.L.IPaddress}</th>
				<!-- ENDIF -->					
			</tr>				
		</thead>				
		<tbody>					
		<!-- BEGIN: USERS -->					
			<tr>						
				<td class="ug{WHOSONlINE_ROW1_USER_MAINGRPID}">{USER_LINK}</td>						
				<td>{USER_MAINGRP}</td>						
				<td>{USER_LOCATION} {USER_SUBLOCATION}</td>						
				<td>{USER_LASTSEEN}</td>						
				<!-- IF {PHP.usr.isadmin} -->
				<td>{USER_IP}</td>
				<!-- ENDIF -->					
			</tr>					
			<!-- END: USERS -->					
			<!-- BEGIN: GUESTS -->					
			<tr>						
				<td colspan="2">{PHP.L.Visitor} #{GUEST_NUMBER}</td>						
				<td>{GUEST_LOCATION} {GUEST_SUBLOCATION}</td>						
				<td>{GUEST_LASTSEEN}</td>						
				<!-- IF {PHP.usr.isadmin} -->
				<td>{GUEST_IP}</td>
				<!-- ENDIF -->					
			</tr>					
			<!-- END: GUESTS -->				
		</tbody>			
	</table>
  </div>
</div>
<br class="clear" />

<!-- END: MAIN -->