<!-- BEGIN: MAIN -->

			<div id="left" style="margin-right:25px">

				<h1>{WHOSONlINE_TITLE}</h1>
				<p><strong>{PHP.L.plu_mostonline}</strong>: {WHOSONlINE_MAXUSERS}</p>

				<!-- BEGIN: NOT_EMPTY -->
				&nbsp;

				<!-- BEGIN: WHOSONlINE_ROW1 -->
				<div class="secrow nou" id="m{PHP.visituser}" style="padding-bottom:1px"> 

					<span class="fright"><!-- IF {WHOSONlINE_ROW1_USER_ONLINE_LASTSEEN} --> {WHOSONlINE_ROW1_USER_ONLINE_LASTSEEN} &nbsp; - <!-- ENDIF -->
					<!-- BEGIN: WHOSONlINE_ROW1_IS_ADMIN -->
					 &nbsp; {WHOSONlINE_ROW1_USER_ONLINE_LOCATION} &nbsp; - &nbsp; {WHOSONlINE_ROW1_USER_ONLINE_IP}
					<!-- END: WHOSONlINE_ROW1_IS_ADMIN -->
					</span>{WHOSONlINE_ROW1_USER_COUNTRYFLAG}
					<h4 style="display:inline;" class="ug{WHOSONlINE_ROW1_USER_MAINGRPID}">{WHOSONlINE_ROW1_USER}</h4>
					 &nbsp; - &nbsp; <a href="{WHOSONlINE_ROW1_USER_MAINGRP_URL}">{WHOSONlINE_ROW1_USER_MAINGRP_TITLE}</a> &nbsp; {WHOSONlINE_ROW1_USER_ICQ} &nbsp; {WHOSONlINE_ROW1_USER_AGE} {WHOSONlINE_ROW1_USER_GENDER} &nbsp; {WHOSONlINE_ROW1_USER_OCCUPATION} 

				</div>
				<!-- END: WHOSONlINE_ROW1 -->

				<!-- BEGIN: WHOSONlINE_ROW2 -->
				<div class="secrow nou" id="g{PHP.visitornum}" style="padding-bottom:1px"> 

					<span class="fright"><!-- IF {WHOSONlINE_ROW2_USER_ONLINE_LASTSEEN} --> {WHOSONlINE_ROW2_USER_ONLINE_LASTSEEN} &nbsp; - <!-- ENDIF -->
					<!-- BEGIN: WHOSONlINE_ROW2_IS_ADMIN -->
					 &nbsp; {WHOSONlINE_ROW2_USER_ONLINE_LOCATION} &nbsp; - &nbsp; {WHOSONlINE_ROW2_USER_ONLINE_IP}
					<!-- END: WHOSONlINE_ROW2_IS_ADMIN -->
					</span>
					<h4 style="display:inline; color:#999">{WHOSONlINE_ROW2_USER}</h4>

				</div>
				<!-- END: WHOSONlINE_ROW2 -->

				<!-- END: NOT_EMPTY -->

			</div>

		</div>
	</div>

	<br class="clear" />

<!-- END: MAIN -->