<!-- BEGIN: MAIN -->
<!-- BEGIN: IPSEARCH_RESULTS -->
	<p>{PHP.L.adm_dnsrecord} : {IPSEARCH_RES_DNS}</p>
	<p>
		Found {IPSEARCH_TOTALMATCHES1} matche(s) for {IPSEARCH_IPMASK1} :
		<ul>
<!-- BEGIN: IPSEARCH_IPMASK1 -->
			<li>{IPSEARCH_USER_IPMASK1} : {IPSEARCH_USER_LASTIP_IPMASK1}</li>
<!-- END: IPSEARCH_IPMASK1 -->
		</ul>
		Found {IPSEARCH_TOTALMATCHES2} matche(s) for {IPSEARCH_IPMASK2}.* :
		<ul>
<!-- BEGIN: IPSEARCH_IPMASK2 -->
			<li>{IPSEARCH_USER_IPMASK2} : {IPSEARCH_USER_LASTIP_IPMASK2}</li>
<!-- END: IPSEARCH_IPMASK2 -->
		</ul>
		Found {IPSEARCH_TOTALMATCHES3} matche(s) for {IPSEARCH_IPMASK3}.*.* :
		<ul>
<!-- BEGIN: IPSEARCH_IPMASK3 -->
			<li>{IPSEARCH_USER_IPMASK3} : {IPSEARCH_USER_LASTIP_IPMASK3}</li>
<!-- END: IPSEARCH_IPMASK3 -->
		</ul>
	</p>
<!-- END: IPSEARCH_RESULTS -->
<h4>{PHP.L.adm_searchthisuser} :</h4>
<form id="search" action="{IPSEARCH_FORM_URL}" method="post">
<input type="text" class="text" name="id" value="{IPSEARCH_ID}" size="16" maxlength="16" />
<input type="submit" class="submit" value="{PHP.L.Search}" />
</form>
<!-- END: MAIN -->