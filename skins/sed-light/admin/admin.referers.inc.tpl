<!-- BEGIN: REFERERS -->
<!-- BEGIN: REFERERS_IS_ADMIN -->
<ul>
	<li>{PHP.L.adm_purgeall} : [<a href="{ADMIN_REFERERS_URL_PRUNE}">x</a>]</li>
	<li>{PHP.L.adm_ref_lowhits} : [<a href="{ADMIN_REFERERS_URL_PRUNELOWHITS}">x</a>]</li>
</ul>
<!-- END: REFERERS_IS_ADMIN -->

<!-- BEGIN: MESAGE -->
	<div class="error">
		{ADMIN_REFERERS_MESAGE}
	</div>
<!-- END: MESAGE -->

<!-- BEGIN: REFERERS_NOT_EMPTY -->
<table class="cells">
	<tr><td class="coltop">{PHP.L.Referer}</td><td class="coltop">{PHP.L.Hits}</td></tr>
<!-- BEGIN: REFERERS_ROW -->
	<tr><td colspan="2"><a href="http://{ADMIN_REFERERS_REFERER}">{ADMIN_REFERERS_REFERER}</a></td></tr>
<!-- BEGIN: REFERERS_URI -->
	<tr>
		<td>&nbsp; &nbsp; <a href="{ADMIN_REFERERS_URI}">{ADMIN_REFERERS_URI}</a></td>";
		<td style="text-align:right;">{ADMIN_REFERERS_COUNT}</td>
	</tr>
<!-- END: REFERERS_URI -->
<!-- END: REFERERS_ROW -->
	<tr><td colspan="2"><div class="pagnav">{ADMIN_REFERERS_PAGINATION_PREV} {ADMIN_REFERERS_PAGNAV} {ADMIN_REFERERS_PAGINATION_NEXT}</div></td></tr>
	<tr><td colspan="2">{PHP.L.Total} : {ADMIN_REFERERS_TOTALITEMS}, {PHP.L.adm_polls_on_page} : {ADMIN_REFERERS_ON_PAGE}</td></tr>
</table>
<!-- END: REFERERS_NOT_EMPTY -->

<!-- BEGIN: REFERERS_EMPTY -->
<table class="cells">
	<tr><td class="coltop">{PHP.L.Referer}</td><td class="coltop">{PHP.L.Hits}</td></tr>
	<tr><td colspan="2">{PHP.L.None}</td></tr>
</table>
<!-- END: REFERERS_EMPTY -->
<!-- END: REFERERS -->