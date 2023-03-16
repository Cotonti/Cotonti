<!-- BEGIN: MAIN -->
{FILE "{PHP.cfg.system_dir}/admin/tpl/warnings.tpl"}

			<h3>{PHP.L.editdeleteentries}:</h3>
			<form action="{ADMIN_BBCODE_UPDATE_URL}" method="post">
			<table class="cells">
				<tr>
					<td class="coltop width35">{PHP.L.Name} / {PHP.L.adm_bbcodes_mode} / {PHP.L.Enabled} / {PHP.L.adm_bbcodes_container}</td>
					<td class="coltop width20">{PHP.L.adm_bbcodes_pattern}</td>
					<td class="coltop width20">{PHP.L.adm_bbcodes_replacement}</td>
					<td class="coltop width15">{PHP.L.Plugin} / {PHP.L.adm_bbcodes_priority} / {PHP.L.adm_bbcodes_postrender}</td>
					<td class="coltop width10">{PHP.L.Action}</td>
				</tr>
<!-- BEGIN: ADMIN_BBCODE_ROW -->
				<tr>
					<td class="centerall">
						{ADMIN_BBCODE_ROW_NAME}	{ADMIN_BBCODE_ROW_MODE} {ADMIN_BBCODE_ROW_ENABLED} {ADMIN_BBCODE_ROW_CONTAINER}
					</td>
					<td class="centerall">
						{ADMIN_BBCODE_ROW_PATTERN}
					</td>
					<td class="centerall">
						{ADMIN_BBCODE_ROW_REPLACEMENT}
					</td>
					<td class="centerall">
						<span style="display:block;">{ADMIN_BBCODE_ROW_PLUG}</span>
						{ADMIN_BBCODE_ROW_PRIO}
						{ADMIN_BBCODE_ROW_POSTRENDER}
					</td>
					<td class="centerall">
						<input type="button" value="{PHP.L.Delete}" onclick="if(confirm('{PHP.L.adm_bbcodes_confirm}')) location.href='{ADMIN_BBCODE_ROW_DELETE_URL}'" />
					</td>
				</tr>
<!-- END: ADMIN_BBCODE_ROW -->
			</table>
			<input onclick="" type="submit" value="{PHP.L.Update}" /><br />
			</form>
			<p class="paging">{ADMIN_BBCODE_PAGINATION_PREV} {ADMIN_BBCODE_PAGNAV} {ADMIN_BBCODE_PAGINATION_NEXT}<span>{PHP.L.Total}: {ADMIN_BBCODE_TOTALITEMS}, {PHP.L.Onpage}: {ADMIN_BBCODE_COUNTER_ROW}</span></p>

<div class="block">
	<h2>{PHP.L.adm_bbcodes_new}:</h2>
	<form action="{ADMIN_BBCODE_FORM_ACTION}" method="post">
	<table class="cells">
		<tr>
			<td class="coltop width35">{PHP.L.Name} / {PHP.L.adm_bbcodes_mode} / {PHP.L.adm_bbcodes_container}</td>
			<td class="coltop width20">{PHP.L.adm_bbcodes_pattern}</td>
			<td class="coltop width20">{PHP.L.adm_bbcodes_replacement}</td>
			<td class="coltop width15">{PHP.L.adm_bbcodes_priority} / {PHP.L.adm_bbcodes_postrender}</td>
			<td class="coltop width10">{PHP.L.Action}</td>
		</tr>
		<tr>
			<td class="centerall">
				{ADMIN_BBCODE_NAME} &nbsp;{ADMIN_BBCODE_MODE} &nbsp;{ADMIN_BBCODE_CONTAINER}
			</td>
			<td class="centerall">{ADMIN_BBCODE_PATTERN}</td>
			<td class="centerall">{ADMIN_BBCODE_REPLACEMENT}</td>
			<td class="centerall">{ADMIN_BBCODE_PRIO} &nbsp;{ADMIN_BBCODE_POSTRENDER}</td>
			<td class="centerall"><input type="submit" value="{PHP.L.Add}" /></td>
		</tr>
	</table>
	</form>
</div>

<div class="block">
	<h2>{PHP.L.adm_bbcodes_other}:</h2>
	<div class="wrapper">
		<ul>
			<li><a href="{ADMIN_BBCODE_URL_CLEAR_CACHE}" onclick="return confirm('{PHP.L.adm_bbcodes_clearcache_confirm}')">{PHP.L.adm_bbcodes_clearcache}</a></li>
			<!-- BEGIN: ADMIN_BBCODE_CONVERT -->
			<li><a href="{ADMIN_BBCODE_CONVERT_URL}" onclick="return confirm('{PHP.L.adm_bbcodes_convert_confirm}')">{ADMIN_BBCODE_CONVERT_TITLE}</a></li>
			<!-- END: ADMIN_BBCODE_CONVERT -->
		</ul>
	</div>
</div>
<!-- END: MAIN -->