<!-- BEGIN: MAIN -->
	{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}
<!-- BEGIN: CONFIG_URL -->
	<ul>
		<li><a title="{PHP.L.Configuration}" href="{ADMIN_EXTENSIONS_CONFIG_URL}">{PHP.L.Configuration}: {PHP.R.admin_icon_config}</a></li>
	</ul>
<!-- END: CONFIG_URL -->
<!-- BEGIN: DETAILS -->
	<h2>{ADMIN_EXTENSIONS_TYPE} {ADMIN_EXTENSIONS_NAME}:</h2>
	<div class="block">
		<table class="cells">
			<tr>
				<td class="width30">{PHP.L.Code}:</td>
				<td class="width70">{ADMIN_EXTENSIONS_CODE}</td>
			</tr>
			<tr>
				<td>{PHP.L.Description}:</td>
				<td>{ADMIN_EXTENSIONS_DESCRIPTION}</td>
			</tr>
			<tr>
				<td>{PHP.L.Version}:</td>
				<td>{ADMIN_EXTENSIONS_VERSION}</td>
			</tr>
			<tr>
				<td>{PHP.L.Date}:</td>
				<td>{ADMIN_EXTENSIONS_DATE}</td>
			</tr>
			<tr>
				<td>{PHP.L.Configuration}:</td>
				<td>
<!-- IF {ADMIN_EXTENSIONS_TOTALCONFIG} > 0 -->
					<a title="{PHP.L.Configuration}" href="{ADMIN_EXTENSIONS_CONFIG_URL}" class="button">{PHP.L.short_config} ({ADMIN_EXTENSIONS_TOTALCONFIG})</a>
<!-- ELSE -->
					{PHP.L.None}
<!-- ENDIF -->
				</td>
			</tr>
			<tr>
				<td>{PHP.L.Rights}:</td>
				<td>
<!-- IF {PHP.isinstalled} AND {PHP.exists} -->
					<a title="{PHP.L.Rights}" href="{ADMIN_EXTENSIONS_RIGHTS}" class="button">{PHP.L.short_rights}</a>
<!-- ELSE -->
					{PHP.L.None}
<!-- ENDIF -->
				</td>
			</tr>
<!--//<tr>
	<td>{PHP.L.adm_defauth_guests}:</td>
	<td>{ADMIN_EXTENSIONS_ADMRIGHTS_AUTH_GUESTS} ({ADMIN_EXTENSIONS_AUTH_GUESTS})</td>
</tr>
<tr>
	<td>{PHP.L.adm_deflock_guests}:</td>
	<td>{ADMIN_EXTENSIONS_ADMRIGHTS_LOCK_GUESTS} ({ADMIN_EXTENSIONS_LOCK_GUESTS})</td>
</tr>
<tr>
	<td>{PHP.L.adm_defauth_members}:</td>
	<td>{ADMIN_EXTENSIONS_ADMRIGHTS_AUTH_MEMBERS} ({ADMIN_EXTENSIONS_AUTH_MEMBERS})</td>
</tr>
<tr>
	<td>{PHP.L.adm_deflock_members}:</td>
	<td>{ADMIN_EXTENSIONS_ADMRIGHTS_LOCK_MEMBERS} ({ADMIN_EXTENSIONS_LOCK_MEMBERS})</td>
</tr>//-->
			<tr>
				<td>{PHP.L.Author}:</td>
				<td>{ADMIN_EXTENSIONS_AUTHOR}</td>
			</tr>
			<tr>
				<td>{PHP.L.Copyright}:</td>
				<td>{ADMIN_EXTENSIONS_COPYRIGHT}</td>
			</tr>
			<tr>
				<td>{PHP.L.Notes}:</td>
				<td>{ADMIN_EXTENSIONS_NOTES}</td>
			</tr>
		</table>
	</div>
	<div class="block">
		<h3>{PHP.L.Options}:</h3>
		<div class="button-toolbar">
<!-- IF !{PHP.isinstalled} -->

					<a title="{PHP.L.adm_opt_install_explain}" href="{ADMIN_EXTENSIONS_INSTALL_URL}" class="ajax button special large">{PHP.L.adm_opt_install}</a>
<!-- ELSE -->
			<!-- IF {PHP.exists} -->

					<a title="{PHP.L.adm_opt_install_explain}" href="{ADMIN_EXTENSIONS_UPDATE_URL}" class="ajax button special large">{PHP.L.adm_opt_update}</a>
			<!-- ENDIF -->
					<a title="{PHP.L.adm_opt_uninstall_explain}" href="{ADMIN_EXTENSIONS_UNINSTALL_URL}" class="ajax button large">{PHP.L.adm_opt_uninstall}</a>
					<a title="{PHP.L.adm_opt_pauseall_explain}" href="{ADMIN_EXTENSIONS_PAUSE_URL}" class="ajax button large">{PHP.L.adm_opt_pauseall}</a>

			<!-- IF {PHP.exists} -->

					<a title="{PHP.L.adm_opt_unpauseall_explain}" href="{ADMIN_EXTENSIONS_UNPAUSE_URL}" class="ajax button large">{PHP.L.adm_opt_unpauseall}</a>

			<!-- ENDIF -->
<!-- ENDIF -->
</div>
	</div>
	<div class="block">
		<h3>{PHP.L.Parts}:</h3>
		<table class="cells">
			<tr>
				<td class="coltop width5">#</td>
				<td class="coltop width15">{PHP.L.Part}</td>
				<td class="coltop width20">{PHP.L.File}</td>
				<td class="coltop width20">{PHP.L.Hooks}</td>
				<td class="coltop width10">{PHP.L.Order}</td>
				<td class="coltop width15">{PHP.L.Status}</td>
				<td class="coltop width15">{PHP.L.Action}</td>
			</tr>
<!-- BEGIN: ROW_ERROR_PART -->
			<tr>
				<td colspan="3">{ADMIN_EXTENSIONS_DETAILS_ROW_X}</td>
				<td colspan="4">{ADMIN_EXTENSIONS_DETAILS_ROW_ERROR}</td>
			</tr>
<!-- END: ROW_ERROR_PART -->
<!-- BEGIN: ROW_PART -->
			<tr>
				<td class="centerall">{ADMIN_EXTENSIONS_DETAILS_ROW_I_1}</td>
				<td class="centerall">{ADMIN_EXTENSIONS_DETAILS_ROW_PART}</td>
				<td class="centerall">{ADMIN_EXTENSIONS_DETAILS_ROW_FILE}</td>
				<td class="centerall">{ADMIN_EXTENSIONS_DETAILS_ROW_HOOKS}</td>
				<td class="centerall">{ADMIN_EXTENSIONS_DETAILS_ROW_ORDER}</td>
				<td class="centerall">{ADMIN_EXTENSIONS_DETAILS_ROW_STATUS}</td>
				<td class="centerall">
<!-- BEGIN: ROW_PART_NOTINSTALLED -->
					&ndash;
<!-- END: ROW_PART_NOTINSTALLED -->
<!-- BEGIN: ROW_PART_PAUSE -->
					<a href="{ADMIN_EXTENSIONS_DETAILS_ROW_PAUSEPART_URL}" class="ajax button">{PHP.L.adm_opt_pause}</a>
<!-- END: ROW_PART_PAUSE -->
<!-- BEGIN: ROW_PART_UNPAUSE -->
					<a href="{ADMIN_EXTENSIONS_DETAILS_ROW_UNPAUSEPART_URL}" class="ajax button">{PHP.L.adm_opt_unpause}</a>
<!-- END: ROW_PART_UNPAUSE -->
				</td>
			</tr>
<!-- END: ROW_PART -->
		</table>
	</div>
	<div class="block">
		<h3>{PHP.L.Tags}:</h3>
		<table class="cells">
			<tr>
				<td class="coltop width5">#</td>
				<td class="coltop width25">{PHP.L.Part}</td>
				<td class="coltop width70">{PHP.L.Files} / {PHP.L.Tags}</td>
			</tr>
<!-- BEGIN: ROW_ERROR_TAGS -->
			<tr>
				<td class="centerall">{ADMIN_EXTENSIONS_DETAILS_ROW_I_1}</td>
				<td class="centerall">{ADMIN_EXTENSIONS_DETAILS_ROW_PART}</td>
				<td class="centerall">{PHP.L.None}</td>
			</tr>
<!-- END: ROW_ERROR_TAGS -->
<!-- BEGIN: ROW_TAGS -->
			<tr>
				<td class="centerall">{ADMIN_EXTENSIONS_DETAILS_ROW_I_1}</td>
				<td class="centerall">{ADMIN_EXTENSIONS_DETAILS_ROW_PART}</td>
				<td>{ADMIN_EXTENSIONS_DETAILS_ROW_LISTTAGS}</td>
			</tr>
<!-- END: ROW_TAGS -->
		</table>
	</div>
<!-- END: DETAILS -->

<!-- BEGIN: HOOKS -->
	<h2>{PHP.L.Hooks} ({ADMIN_EXTENSIONS_CNT_HOOK}):</h2>
	<table class="cells">
		<tr>
			<td class="coltop width40">{PHP.L.Hooks}</td>
			<td class="coltop width20">{PHP.L.Plugin}</td>
			<td class="coltop width20">{PHP.L.Order}</td>
			<td class="coltop width20">{PHP.L.Active}</td>
		</tr>
<!-- BEGIN: HOOKS_ROW -->
		<tr>
			<td>{ADMIN_EXTENSIONS_HOOK}</td>
			<td>{ADMIN_EXTENSIONS_CODE}</td>
			<td class="centerall">{ADMIN_EXTENSIONS_ORDER}</td>
			<td class="centerall">{ADMIN_EXTENSIONS_ACTIVE}</td>
		</tr>
<!-- END: HOOKS_ROW -->
	</table>
<!-- END: HOOKS -->

<!-- BEGIN: DEFAULT -->
<!-- BEGIN: SECTION-->
	<h2>{ADMIN_EXTENSIONS_SECTION_TITLE} ({ADMIN_EXTENSIONS_CNT_EXTP}):</h2>
	<div class="block">
		<table class="cells">
			<tr>
				<td class="coltop width25">{PHP.L.Name} {PHP.L.adm_clicktoedit}</td>
				<td class="coltop width15">{PHP.L.Code}</td>
				<td class="coltop width5">{PHP.L.Version}</td>
				<td class="coltop width5">{PHP.L.Parts}</td>
				<td class="coltop width15">{PHP.L.Status}</td>
				<td class="coltop width35">{PHP.L.Action}</td>
			</tr>
<!-- BEGIN: ROW -->
<!-- BEGIN: ROW_ERROR_EXT-->
			<tr>
				<td>{ADMIN_EXTENSIONS_X_ERR}</td>
				<td colspan="5">{ADMIN_EXTENSIONS_ERROR_MSG}</td>
			</tr>
<!-- END: ROW_ERROR_EXT -->
			<tr>
				<td><a href="{ADMIN_EXTENSIONS_DETAILS_URL}"><strong>{ADMIN_EXTENSIONS_NAME}</strong></a></td>
				<td>{ADMIN_EXTENSIONS_CODE_X}</td>
				<td>{ADMIN_EXTENSIONS_VERSION}</td>
				<td class="centerall">{ADMIN_EXTENSIONS_PARTSCOUNT}</td>
				<td class="centerall">{ADMIN_EXTENSIONS_STATUS}</td>
				<td class="action">
<!-- IF {PHP.totalinstalled} -->
					
					<a title="{PHP.L.Configuration}" href="{ADMIN_EXTENSIONS_EDIT_URL}" class="button">{PHP.L.short_config}</a>
<!-- ENDIF -->
<!-- IF {PHP.ifstruct} -->
					<a title="{PHP.L.Structure}" href="{ADMIN_EXTENSIONS_JUMPTO_URL_STRUCT}" class="button">{PHP.L.short_struct}</a>
<!-- ENDIF -->
<!-- IF {PHP.totalinstalled} -->
					<a title="{PHP.L.Rights}" href="{ADMIN_EXTENSIONS_RIGHTS_URL}" class="button">{PHP.L.short_rights}</a>
<!-- ENDIF -->
<!-- IF {PHP.ifthistools} -->
					<a title="{PHP.L.Administration}" href="{ADMIN_EXTENSIONS_JUMPTO_URL_TOOLS}" class="button special">{PHP.L.short_admin}</a>
<!-- ENDIF -->
<!-- IF {PHP.if_plg_standalone} -->
					<a title="{PHP.L.Open}" href="{ADMIN_EXTENSIONS_JUMPTO_URL}" class="button special">{PHP.L.Open}</a>
<!-- ENDIF -->
				</td>
			</tr>
<!-- END: ROW -->
<!-- BEGIN: ROW_ERROR -->
			<tr>
				<td>{ADMIN_EXTENSIONS_X}</td>
				<td colspan="5">{PHP.L.adm_opt_setup_missing}</td>
			</tr>
<!-- END: ROW_ERROR -->
		</table>
	</div>
<!-- END: SECTION -->

	<div class="block button-toolbar">
		<a href="{ADMIN_EXTENSIONS_HOOKS_URL}" class="button large">{PHP.L.Hooks}</a>
	</div>

<!-- END: DEFAULT -->
<!-- BEGIN: EDIT -->
		<h2>{ADMIN_EXTENSIONS_EDIT_TITLE}</h2>
		<div class="{ADMIN_EXTENSIONS_EDIT_RESULT}">
			{ADMIN_EXTENSIONS_EDIT_LOG}
		</div>
			<a href="{ADMIN_EXTENSIONS_EDIT_CONTINUE_URL}" class="ajax button">{PHP.L.Clickhere}</a>
<!-- END: EDIT -->
<!-- END: MAIN -->