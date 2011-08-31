<!-- BEGIN: MAIN -->
		<h2>{PHP.L.Referers}</h2>
		{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}
<!-- IF {PHP.usr.isadmin} -->
			<div class="block button-toolbar">
				<a href="{ADMIN_REFERERS_URL_PRUNE}" class="button">{PHP.L.adm_purgeall}</a>
				<a href="{ADMIN_REFERERS_URL_PRUNELOWHITS}" class="button">{PHP.L.adm_ref_lowhits}</a>
			</div>
<!-- ENDIF -->
<!-- IF {PHP.is_ref_empty} -->
			<table class="cells">
				<tr>
					<td class="coltop width70">{PHP.L.Referer}</td>
					<td class="coltop width30">{PHP.L.Hits}</td>
				</tr>
<!-- ENDIF -->
<!-- BEGIN: REFERERS_ROW -->
			<tr>
				<td colspan="2"><a href="http://{ADMIN_REFERERS_REFERER}">{ADMIN_REFERERS_REFERER}</a></td>
			</tr>
<!-- BEGIN: REFERERS_URI -->
			<tr>
				<td>&nbsp; &nbsp; <a href="{ADMIN_REFERERS_URI}">{ADMIN_REFERERS_URI}</a></td>
				<td class="textright">{ADMIN_REFERERS_COUNT}</td>
			</tr>
<!-- END: REFERERS_URI -->
<!-- END: REFERERS_ROW -->
<!-- IF {PHP.is_ref_empty} -->
			</table>
			<p class="paging">{ADMIN_REFERERS_PAGINATION_PREV}{ADMIN_REFERERS_PAGNAV}{ADMIN_REFERERS_PAGINATION_NEXT} <span>{PHP.L.Total} : {ADMIN_REFERERS_TOTALITEMS}, {PHP.L.Onpage}: {ADMIN_REFERERS_ON_PAGE}</span></p>
<!-- ELSE -->
			<table class="cells">
				<tr>
					<td class="coltop width70">{PHP.L.Referer}</td>
					<td class="coltop width30">{PHP.L.Hits}</td>
				</tr>
				<tr>
					<td class="centerall" colspan="2">{PHP.L.None}</td>
				</tr>
			</table>
<!-- ENDIF -->

<!-- END: MAIN -->