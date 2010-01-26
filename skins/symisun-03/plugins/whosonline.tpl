<!-- BEGIN: MAIN -->

<div id="content">
  <div class="padding20">
    <h1>{WHOSONlINE_TITLE}</h1>
    <p><strong>{PHP.L.plu_mostonline}</strong>: {WHOSONlINE_MAXUSERS}</p>
    <!-- BEGIN: NOT_EMPTY -->
    <p class="seccat" style="text-align:right; color:#ccc"> <strong> {PHP.L.plu_lastseen1} &nbsp; &nbsp;
      <!-- BEGIN: IS_ADMIN -->
      - &nbsp; &nbsp; {WHOSONlINE_IN} &nbsp; &nbsp; - &nbsp; &nbsp; {WHOSONlINE_IP}
      <!-- END: IS_ADMIN -->
      </strong> </p>
    <!-- BEGIN: WHOSONlINE_ROW1 -->
    <div class="secrow"> <span class="colright"> {WHOSONlINE_ROW1_USER_ONLINE_LASTSEEN} &nbsp;
      <!-- BEGIN: WHOSONlINE_ROW1_IS_ADMIN -->
      &nbsp; {WHOSONlINE_ROW1_USER_ONLINE_LOCATION} &nbsp; &nbsp; {WHOSONlINE_ROW1_USER_ONLINE_IP}
      <!-- END: WHOSONlINE_ROW1_IS_ADMIN -->
      </span>{WHOSONlINE_ROW1_USER_COUNTRYFLAG}
      <h4 style="display:inline" class="ug{WHOSONlINE_ROW1_USER_MAINGRPID}">{WHOSONlINE_ROW1_USER}</h4>
      &nbsp; <a href="{WHOSONlINE_ROW1_USER_MAINGRP_URL}">{WHOSONlINE_ROW1_USER_MAINGRP_TITLE}</a> &nbsp; 
      {WHOSONlINE_ROW1_USER_LOCATION} &nbsp; 
      {WHOSONlINE_ROW1_USER_ICQ} &nbsp; 
      {WHOSONlINE_ROW1_USER_AGE}
      {WHOSONlINE_ROW1_USER_GENDER} &nbsp; 
      {WHOSONlINE_ROW1_USER_OCCUPATION} </div>
    <!-- END: WHOSONlINE_ROW1 -->
    <!-- BEGIN: WHOSONlINE_ROW2 -->
    <div class="secrow"> <span class="colright"> {WHOSONlINE_ROW2_USER_ONLINE_LASTSEEN} &nbsp;
      <!-- BEGIN: WHOSONlINE_ROW2_IS_ADMIN -->
      &nbsp; {WHOSONlINE_ROW2_USER_ONLINE_LOCATION} &nbsp; &nbsp; {WHOSONlINE_ROW2_USER_ONLINE_IP}
      <!-- END: WHOSONlINE_ROW2_IS_ADMIN -->
      </span>
      <h4 style="display:inline; color:#999">{WHOSONlINE_ROW2_USER}</h4>
    </div>
    <!-- END: WHOSONlINE_ROW2 -->
    <!-- END: NOT_EMPTY -->
  </div>
</div>
<br class="clear" />

<!-- END: MAIN -->