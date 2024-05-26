<!-- BEGIN: MAIN -->
<!-- IF {PHP.usr.isadmin} -->
<div class="block button-toolbar">
	<a href="{ADMIN_REFERERS_URL_PRUNE}" class="button">{PHP.L.adm_purgeall}</a>
	<a href="{ADMIN_REFERERS_URL_PRUNELOWHITS}" class="button">{PHP.L.adm_ref_prunelowhits}</a>
</div>
<!-- ENDIF -->

{FILE "{PHP.cfg.system_dir}/admin/tpl/warnings.tpl"}

<div class="block">
	<table class="cells">
		<tr>
			<td class="coltop w-70">{PHP.L.Referer}</td>
			<td class="coltop w-30">{PHP.L.Hits}</td>
		</tr>
		<!-- BEGIN: REFERERS_ROW -->
		<tr>
			<td colspan="2"><a href="//{ADMIN_REFERERS_REFERER}">{ADMIN_REFERERS_REFERER}</a></td>
		</tr>
		<!-- BEGIN: REFERERS_URI -->
		<tr>
			<td>&nbsp; &nbsp; <a href="{ADMIN_REFERERS_URI}">{ADMIN_REFERERS_URI}</a></td>
			<td class="textright">{ADMIN_REFERERS_COUNT}</td>
		</tr>
		<!-- END: REFERERS_URI -->
		<!-- END: REFERERS_ROW -->
		<!-- IF !{TOTAL_ENTRIES} -->
		<tr>
			<td class="centerall" colspan="2">{PHP.L.None}</td>
		</tr>
		<!-- ENDIF -->
	</table>
	<!-- IF {TOTAL_ENTRIES} -->
	<p class="paging">
		{PREVIOUS_PAGE}{PAGINATION}{NEXT_PAGE}
		<span>{PHP.L.Total}: {TOTAL_ENTRIES}, {PHP.L.Onpage}: {ENTRIES_ON_CURRENT_PAGE}</span>
	</p>
	<!-- ENDIF -->
</div>
<!-- END: MAIN -->