<!-- BEGIN: MAIN -->

<div id="center" class="column">
	<div class="block">
		<h2 class="stats">{PHP.L.adm_dnsrecord}: {IPSEARCH_RES_DNS}</h2>

<!-- BEGIN: IPSEARCH_RESULTS -->

		<p>Found {IPSEARCH_TOTALMATCHES1} match(es) for {IPSEARCH_IPMASK1}:</p>
		<ul>

<!-- BEGIN: IPSEARCH_IPMASK1 -->
			<li>{IPSEARCH_USER_IPMASK1}: {IPSEARCH_USER_LASTIP_IPMASK1}</li>
<!-- END: IPSEARCH_IPMASK1 -->

		</ul>
		<p>Found {IPSEARCH_TOTALMATCHES2} match(es) for {IPSEARCH_IPMASK2}.*:</p>
		<ul>

<!-- BEGIN: IPSEARCH_IPMASK2 -->
			<li>{IPSEARCH_USER_IPMASK2}: {IPSEARCH_USER_LASTIP_IPMASK2}</li>
<!-- END: IPSEARCH_IPMASK2 -->

		</ul>
		<p>Found {IPSEARCH_TOTALMATCHES3} match(es) for {IPSEARCH_IPMASK3}.*.*:</p>
		<ul>

<!-- BEGIN: IPSEARCH_IPMASK3 -->
			<li>{IPSEARCH_USER_IPMASK3}: {IPSEARCH_USER_LASTIP_IPMASK3}</li>
<!-- END: IPSEARCH_IPMASK3 -->

		</ul>

<!-- END: IPSEARCH_RESULTS -->
	</div>
</div>

<div id="side" class="column">
	<div class="block">
		<h2 class="search">{PHP.L.adm_searchthisuser}:</h2>
		<form id="search" action="{IPSEARCH_FORM_URL}" method="post">
			<input type="text" class="text" name="id" value="{IPSEARCH_ID}" size="16" maxlength="16" />
			<input type="submit" class="submit" value="{PHP.L.Search}" />
		</form>
	</div>
</div>

<!-- END: MAIN -->