<!-- BEGIN: MAIN -->
		<h2>{PHP.L.comments_comments}</h2>
		{FILE "{PHP.cfg.themes_dir}/{PHP.usr.theme}/warnings.tpl"}
			<div class="block button-toolbar">
				<a title="{PHP.L.Configuration}" href="{ADMIN_COMMENTS_CONFIG_URL}" class="button">{PHP.L.Configuration}</a>
			</div>
			<h3>{PHP.L.viewdeleteentries}:</h3>
			<table class="cells">
				<tr>
					<td class="coltop width5">#</td>
					<td class="coltop width5">{PHP.L.adm_area}</td>
					<td class="coltop width5">{PHP.L.Code}</td>
					<td class="coltop width15">{PHP.L.Author}</td>
					<td class="coltop width15">{PHP.L.Date}</td>
					<td class="coltop width35">{PHP.L.comments_comment}</td>
					<td class="coltop width20">{PHP.L.Action}</td>
				</tr>
<!-- BEGIN: ADMIN_COMMENTS_ROW -->
				<tr>
					<td class="textcenter">{ADMIN_COMMENTS_ITEM_ID}</td>
					<td class="textcenter">{ADMIN_COMMENTS_AREA}</td>
					<td class="textcenter">{ADMIN_COMMENTS_CODE}</td>
					<td class="textcenter">{ADMIN_COMMENTS_AUTHOR}</td>
					<td class="textcenter">{ADMIN_COMMENTS_DATE}</td>
					<td>{ADMIN_COMMENTS_TEXT}</td>
					<td class="centerall action">
						<a title="{PHP.L.Open}" href="{ADMIN_COMMENTS_URL}" class="button special">{PHP.L.Open}</a><a title="{PHP.L.Delete}" href="{ADMIN_COMMENTS_ITEM_DEL_URL}" class="ajax button">{PHP.L.Delete}</a>

					</td>
				</tr>
<!-- END: ADMIN_COMMENTS_ROW -->
			</table>
			<p class="paging">{ADMIN_COMMENTS_PAGINATION_PREV}{ADMIN_COMMENTS_PAGNAV}{ADMIN_COMMENTS_PAGINATION_NEXT}<span>{PHP.L.Total}: {ADMIN_COMMENTS_TOTALITEMS}, {PHP.L.Onpage}: {ADMIN_COMMENTS_COUNTER_ROW}</span></p>
<!-- END: MAIN -->