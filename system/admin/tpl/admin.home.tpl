<!-- BEGIN: MAIN -->

		<!-- IF {PHP.is_adminwarnings} --><div class="error">{PHP.L.adm_warnings}: {PHP.adm_nogd}</div><!-- ENDIF -->

<div class="col3-2">
    <h2>{PHP.L.adm_quick}</h2>
    <ul class="icons">
        <li>
            <a href="page.php?m=add"><img src="system/admin/tpl/img/big_icons/page_add.png">{PHP.L.Submitnew}</a>
        </li>
        <li>
            <a href="admin.php?m=structure&n=page"><img src="system/admin/tpl/img/big_icons/table_edit.png">{PHP.L.adm_quick_categories}</a>
        </li>
    </ul>
</div>

<div class="col3-1">
    <div class="block">
        <h2>{PHP.L.adm_valqueue}:</h2>
        <ul class="follow">
            <li><a href="{ADMIN_HOME_URL}">{PHP.L.Pages}: {ADMIN_HOME_PAGESQUEUED}</a></li>
        </ul>
    </div>
</div>

<div class="clear"></div>

<div class="col1 first">
<!-- BEGIN: UPDATE -->
			<div class="block">
				<h2>{PHP.L.adminqv_update_notice}:</h2>
				<p>{ADMIN_HOME_UPDATE_REVISION} {ADMIN_HOME_UPDATE_MESSAGE}</p>
			</div>
<!-- END: UPDATE -->
			{ADMIN_HOME_HITS}
			<div class="block">
				<h2>Cotonti:</h2>
				<table class="cells">
					<tr>
						<td class="width80">{PHP.L.Version} ({PHP.L.home_rev_title})</td>
						<td class="textcenter width20">{ADMIN_HOME_VERSION} ({ADMIN_HOME_REVISION})</td>
					</tr>
					<tr>
						<td>{PHP.L.Database}</td>
						<td class="textcenter">{ADMIN_HOME_DB_VERSION}</td>
					</tr>
					<!-- IF !{PHP.cfg.plugin.hits.disabledbstats} AND {PHP.cfg.plugin.hits}  --><tr>
						<td>{PHP.L.home_db_rows}</td>
						<td class="textcenter">{ADMIN_HOME_DB_TOTAL_ROWS}</td>
					</tr>
					<tr>
						<td>{PHP.L.home_db_indexsize}</td>
						<td class="textcenter">{ADMIN_HOME_DB_INDEXSIZE}</td>
					</tr>
					<tr>
						<td>{PHP.L.home_db_datassize}</td>
						<td class="textcenter">{ADMIN_HOME_DB_DATASSIZE}</td>
					</tr>
					<tr>
						<td>{PHP.L.home_db_totalsize}</td>
						<td class="textcenter">{ADMIN_HOME_DB_TOTALSIZE}</td>
					</tr>
					<tr>
						<td>{PHP.L.Plugins}</td>
						<td class="textcenter">{ADMIN_HOME_TOTALPLUGINS}</td>
					</tr>
					<tr>
						<td>{PHP.L.Hooks}</td>
						<td class="textcenter">{ADMIN_HOME_TOTALHOOKS}</td>
					</tr><!-- ENDIF -->
				</table>
			</div>
		</div>

<!-- END: MAIN -->