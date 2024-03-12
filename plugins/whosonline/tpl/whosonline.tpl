<!-- BEGIN: MAIN -->
<div class="block">
    <h2 class="users"><a href="{PHP|cot_url('plug','e=whosonline')}">{PHP.L.WhosOnline}</a></h2>

    <table class="cells">
        <thead>
        <tr>
            <th>{PHP.L.User}</th>
            <th>{PHP.L.Group}</th>
            <th>{PHP.L.Type}</th>
            <th>{PHP.L.Location}</th>
            <th>{PHP.L.LastSeen}</th>
            <!-- IF {PHP.usr.isadmin} -->
            <th>{PHP.L.Ip}</th><!-- ENDIF -->
        </tr>
        </thead>
        <tbody>
        <!-- BEGIN: USERS -->
        <tr>
            <td>{USER_LINK}</td>
            <td>{USER_MAIN_GROUP}</td>
            <td>
				<!-- IF {PHP.usr.isadmin} AND {USER_URL} != '' --><a href="{USER_URL}"><!-- ENDIF -->
				{USER_LOCATION}
				<!-- IF {PHP.usr.isadmin} AND {USER_URL} != '' --></a><!-- ENDIF -->
			</td>
            <td>
                <!-- IF {USER_SUBLOCATION} -->
                <!-- IF {PHP.usr.isadmin} AND {USER_URL} != '' --><a href="{USER_URL}"><!-- ENDIF -->
                {USER_SUBLOCATION}
                <!-- IF {PHP.usr.isadmin} AND {USER_URL} != '' --></a><!-- ENDIF -->
                <!-- ENDIF -->
            </td>
            <td>{USER_LASTSEEN} {PHP.L.Ago}</td>
            <!-- IF {PHP.usr.isadmin} -->
            <td>{USER_IP}</td>
			<!-- ENDIF -->
        </tr>
        <!-- END: USERS -->
        <!-- BEGIN: GUESTS -->
        <tr>
            <td colspan="2">{PHP.L.Guest} #{GUEST_NUMBER}</td>
            <td>
				<!-- IF {PHP.usr.isadmin} AND {GUEST_URL} != '' --><a href="{GUEST_URL}"><!-- ENDIF -->
				{GUEST_LOCATION}
				<!-- IF {PHP.usr.isadmin} AND {GUEST_URL} != '' --></a><!-- ENDIF -->
			</td>
            <td>
                <!-- IF {GUEST_SUBLOCATION} -->
                <!-- IF {PHP.usr.isadmin} AND {GUEST_URL} != '' --><a href="{GUEST_URL}"><!-- ENDIF -->
                {GUEST_SUBLOCATION}
                <!-- IF {PHP.usr.isadmin} AND {GUEST_URL} != '' --></a><!-- ENDIF -->
                <!-- ENDIF -->
            </td>
            <td>{GUEST_LASTSEEN} {PHP.L.Ago}</td>
            <!-- IF {PHP.usr.isadmin} -->
            <td>{GUEST_IP}</td>
			<!-- ENDIF -->
        </tr>
        <!-- END: GUESTS -->
        </tbody>
    </table>

	<!-- IF {TOTAL_PAGES} > 1 -->
	<p class="paging">
		<span>{PHP.L.Page} {CURRENT_PAGE} {PHP.L.Of} {TOTAL_PAGES}</span>{PREVIOUS_PAGE}{PAGINATION}{NEXT_PAGE}
	</p>
	<!-- ENDIF -->

    <p>
		<strong>{PHP.L.NowOnline}:</strong> {STAT_COUNT_USERS}
        {USERS}<!-- IF !{PHP.cfg.plugin.whosonline.disable_guests} -->, {STAT_COUNT_GUESTS} {GUESTS}<!-- ENDIF -->
	</p>
    <!-- IF {STAT_MAXUSERS} --><p><strong>{PHP.L.MostOnline}:</strong> {STAT_MAXUSERS}</p><!-- ENDIF -->
</div>
<!-- END: MAIN -->