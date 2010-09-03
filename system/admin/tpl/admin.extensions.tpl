<!-- BEGIN: MAIN -->
	<div id="ajaxBlock">
<!-- BEGIN: MESSAGE -->
			<div class="error">
				<h4>{PHP.L.Message}</h4>
				<p>{ADMIN_EXTENSIONS_ADMINWARNINGS}</p>
			</div>
<!-- END: MESSAGE -->
<!-- BEGIN: CONFIG_URL -->
	<ul>
		<li><a title="{PHP.L.Configuration}" href="{ADMIN_EXTENSIONS_CONFIG_URL}">{PHP.L.Configuration}: {PHP.R.admin_icon_config}</a></li>
	</ul>
<!-- END: CONFIG_URL -->
<!-- BEGIN: DETAILS -->
	<h2>{PHP.L.Plugin} {ADMIN_EXTENSIONS_NAME}:</h2>
	<table class="cells">
		<tr>
			<td class="width20">{PHP.L.Code}:</td>
			<td class="width80">{ADMIN_EXTENSIONS_CODE}</td>
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
				<a title="{PHP.L.Configuration}" href="{ADMIN_EXTENSIONS_CONFIG_URL}">{PHP.R.icon_prefs} {PHP.L.Edit} ({ADMIN_EXTENSIONS_TOTALCONFIG})</a>
<!-- ELSE -->
				{PHP.L.None}
<!-- ENDIF -->
			</td>
		</tr>
		<tr>
			<td>{PHP.L.Rights}:</td>
			<td>
<!-- IF {PHP.isinstalled} -->
				<a title="{PHP.L.Rights}" href="{ADMIN_EXTENSIONS_RIGHTS}">{PHP.R.icon_rights}</a>
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
	<h3>{PHP.L.Options}:</h3>
	<table class="cells">
		<tr>
			<td class="width20">
				<a href="{ADMIN_EXTENSIONS_INSTALL_URL}" class="ajax">{PHP.L.adm_opt_installall}</a>
			</td>
			<td class="width80">
				{PHP.L.adm_opt_installall_explain}
<!-- IF !{PHP.isinstalled} AND {PHP.totalconfig} > 0 -->
				<p class="small"><a href="{ADMIN_EXTENSIONS_INSTALL_KO_URL}" class="ajax">{PHP.L.adm_opt_setoption_warn}</a></p>
<!-- ENDIF -->
			</td>
		</tr>
<!-- IF {PHP.isinstalled} > 0 -->
		<tr>
			<td>
				<a href="{ADMIN_EXTENSIONS_UNINSTALL}" class="ajax">{PHP.L.adm_opt_uninstallall}</a>
			</td>
			<td>
				{PHP.L.adm_opt_uninstallall_explain}
<!-- ENDIF -->
<!-- IF {PHP.isinstalled} > 0 AND {PHP.totalconfig} > 0 -->
				<p class="small"><a href="{ADMIN_EXTENSIONS_UNINSTALL_KO_URL}" class="ajax">{PHP.L.adm_opt_uninstall_warn}</a></p>
<!-- ENDIF -->
<!-- IF {PHP.isinstalled} -->
			</td>
		</tr>
		<tr>
			<td>
				<a href="{ADMIN_EXTENSIONS_PAUSE_URL}" class="ajax">{PHP.L.adm_opt_pauseall}</a>
			</td>
			<td>
				{PHP.L.adm_opt_pauseall_explain}
			</td>
		</tr>
		<tr>
			<td>
				<a href="{ADMIN_EXTENSIONS_UNPAUSE_URL}" class="ajax">{PHP.L.adm_opt_unpauseall}</a>
			</td>
			<td>
				{PHP.L.adm_opt_unpauseall_explain}
			</td>
		</tr>
<!-- ENDIF -->
	</table>
	<h3>{PHP.L.Parts}:</h3>
	<table class="cells">
		<tr>
			<td class="coltop width5">#</td>
			<td class="coltop width15">{PHP.L.Part}</td>
			<td class="coltop width20">{PHP.L.File}</td>
			<td class="coltop width15">{PHP.L.Hooks}</td>
			<td class="coltop width15">{PHP.L.Order}</td>
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
			<td class="centerall">{ADMIN_EXTENSIONS_DETAILS_ROW_FILE}.php</td>
			<td class="centerall">{ADMIN_EXTENSIONS_DETAILS_ROW_HOOKS}</td>
			<td class="centerall">{ADMIN_EXTENSIONS_DETAILS_ROW_ORDER}</td>
			<td class="centerall">{ADMIN_EXTENSIONS_DETAILS_ROW_STATUS}</td>
			<td class="centerall">
<!-- BEGIN: ROW_PART_NOTINSTALLED -->
				&ndash;
<!-- END: ROW_PART_NOTINSTALLED -->
<!-- BEGIN: ROW_PART_PAUSE -->
				<a href="{ADMIN_EXTENSIONS_DETAILS_ROW_PAUSEPART_URL}" class="ajax">{PHP.L.adm_opt_pauseall}</a>
<!-- END: ROW_PART_PAUSE -->
<!-- BEGIN: ROW_PART_UNPAUSE -->
				<a href="{ADMIN_EXTENSIONS_DETAILS_ROW_UNPAUSEPART_URL}" class="ajax">{PHP.L.adm_opt_unpauseall}</a>
<!-- END: ROW_PART_UNPAUSE -->
			</td>
		</tr>
<!-- END: ROW_PART -->
	</table>
	<h3>{PHP.L.Tags}:</h3>
	<table class="cells">
		<tr>
			<td class="coltop width5">#</td>
			<td class="coltop width15">{PHP.L.Part}</td>
			<td class="coltop width80">{PHP.L.Files} / {PHP.L.Tags}</td>
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
<!-- END: DETAILS -->
<!-- BEGIN: DEFAULT -->
	<h2>{PHP.L.Plugins} ({ADMIN_EXTENSIONS_CNT_EXTP}):</h2>
	<table class="cells">
		<tr>
			<td class="coltop width5">&nbsp;</td>
			<td class="coltop width30">{PHP.L.Plugins} {PHP.L.adm_clicktoedit}</td>
			<td class="coltop width20">{PHP.L.Code}</td>
			<td class="coltop width10">{PHP.L.Parts}</td>
			<td class="coltop width20">{PHP.L.Status}</td>
			<td class="coltop width15">{PHP.L.Action}</td>
		</tr>
<!-- BEGIN: ROW -->
<!-- BEGIN: ROW_ERROR_PLUG -->
		<tr>
			<td>{ADMIN_EXTENSIONS_X_ERR}</td>
			<td colspan="5">{ADMIN_EXTENSIONS_ERROR_MSG}</td>
		</tr>
<!-- END: ROW_ERROR_PLUG -->
		<tr>
			<td class="centerall">
<!-- IF {PHP.ifthistools} -->
				{PHP.R.icon_tool}
<!-- ELSE -->
				{PHP.R.icon_plug}
<!-- ENDIF -->
			</td>
			<td><a href="{ADMIN_EXTENSIONS_DETAILS_URL}">{ADMIN_EXTENSIONS_NAME}</a></td>
			<td>{ADMIN_EXTENSIONS_CODE_X}</td>
			<td class="centerall">{ADMIN_EXTENSIONS_PARTSCOUNT}</td>
			<td class="centerall">{ADMIN_EXTENSIONS_STATUS}</td>
			<td class="centerall action">
<!-- IF {PHP.ent_code} > 0 -->
				<a title="{PHP.L.Configuration}" href="{ADMIN_EXTENSIONS_EDIT_URL}">{PHP.R.admin_icon_config}</a>
<!-- ENDIF -->
<!-- IF {PHP.part_status} != 3 -->
				<a title="{PHP.L.Rights}" href="{ADMIN_EXTENSIONS_RIGHTS_URL}">{PHP.R.admin_icon_rights2}</a>
<!-- ENDIF -->
<!-- IF {PHP.ifthistools} -->
				<a title="{PHP.L.Open}" href="{ADMIN_EXTENSIONS_JUMPTO_URL_TOOLS}">{PHP.R.admin_icon_jumpto}</a>
<!-- ENDIF -->
<!-- IF !{PHP.ifthistools} AND {PHP.if_plg_standalone} -->
				<a title="{PHP.L.Open}" href="{ADMIN_EXTENSIONS_JUMPTO_URL}">{PHP.R.admin_icon_jumpto}</a>
<!-- ENDIF -->
			</td>
		</tr>
<!-- END: ROW -->
<!-- BEGIN: ROW_ERROR -->
		<tr>
			<td>plugins/{ADMIN_EXTENSIONS_X}</td>
			<td colspan="5">{PHP.L.adm_opt_setup_missing}</td>
		</tr>
<!-- END: ROW_ERROR -->
	</table>
	<h2>{PHP.L.Hooks} ({ADMIN_EXTENSIONS_CNT_HOOK}):</h2>
	<table class="cells">
		<tr>
			<td class="coltop width45">{PHP.L.Hooks}</td>
			<td class="coltop width20">{PHP.L.Plugin}</td>
			<td class="coltop width20">{PHP.L.Order}</td>
			<td class="coltop width15">{PHP.L.Active}</td>
		</tr>
<!-- BEGIN: HOOKS -->
		<tr>
			<td>{ADMIN_EXTENSIONS_HOOK}</td>
			<td>{ADMIN_EXTENSIONS_CODE}</td>
			<td class="centerall">{ADMIN_EXTENSIONS_ORDER}</td>
			<td class="centerall">{ADMIN_EXTENSIONS_ACTIVE}</td>
		</tr>
<!-- END: HOOKS -->
	</table>
<!-- END: DEFAULT -->
<!-- BEGIN: EDIT -->
<!-- BEGIN: INSTALL -->
		<h2>{PHP.L.adm_pluginstall_msg01}</h2>

<!-- BEGIN: ROW_PARTS_FOUND -->
		<p>&ndash; {PHP.L.Found}: {ADMIN_EXTENSIONS_EDIT_INSTALL_ROW_PARTS_FOUND_F}</p>
<!-- END: ROW_PARTS_FOUND -->
<!-- IF {PHP.extplugin_info_exists} -->
		<h3>{PHP.L.adm_pluginstall_msg05}</h3>
<!-- ENDIF -->
<!-- BEGIN: ROW_PARTS_INSTALLING -->
		<p>&ndash; {PHP.L.Part}: {ADMIN_EXTENSIONS_EDIT_INSTALL_ROW_PARTS_INSTALLING_X} ...<br />
		{ADMIN_EXTENSIONS_EDIT_INSTALL_ROW_PARTS_INSTALLING_MSG}</p>
<!-- END: ROW_PARTS_INSTALLING -->
<!-- IF {PHP.extplugin_info_exists} -->
		<h3>{PHP.L.adm_pluginstall_msg06}</h3>
<!-- ENDIF -->
<!-- BEGIN: ROW_PARTS_CFG -->
		<p>&ndash; {PHP.L.Found}: {ADMIN_EXTENSIONS_EDIT_INSTALL_ROW_PARTS_CFG_TOTALCONFIG}</p>
<!-- BEGIN: ROW_PARTS_CFG_ENTRY -->
		<p>&ndash; {PHP.L.Entry} #{ADMIN_EXTENSIONS_EDIT_INSTALL_ROW_PARTS_CFG_J} {ADMIN_EXTENSIONS_EDIT_INSTALL_ROW_PARTS_CFG_I} ({ADMIN_EXTENSIONS_EDIT_INSTALL_ROW_PARTS_CFG_LINE}) {PHP.L.adm_installed}</p>
<!-- END: ROW_PARTS_CFG_ENTRY -->
<!-- END: ROW_PARTS_CFG -->
<!-- BEGIN: ROW_PARTS_CFG_ERROR -->
		<p>{PHP.L.None}</p>
<!-- END: ROW_PARTS_CFG_ERROR -->
<!-- IF !{PHP.extplugin_info_exists} -->
		<h3>{PHP.L.adm_pluginstall_msg07}</h3>
<!-- ENDIF -->
<!-- IF !{PHP.ko} -->
		<h3>{PHP.L.adm_pluginstall_msg08}</h3>
		<p>{PHP.L.Found}: {ADMIN_EXTENSIONS_EDIT_AFFECTEDROWS3}</p>
<!-- ENDIF -->
		<h3>{PHP.L.adm_pluginstall_msg09}</h3>
<!-- BEGIN: ROW_RIGHTS -->
		<p>&ndash; {PHP.L.Group} #{ADMIN_EXTENSIONS_EDIT_INSTALL_ROW_RIGHTS_ID}, {ADMIN_EXTENSIONS_EDIT_INSTALL_ROW_RIGHTS_TITLE} : Auth={ADMIN_EXTENSIONS_EDIT_INSTALL_ROW_RIGHTS_AUTH} / Lock={ADMIN_EXTENSIONS_EDIT_INSTALL_ROW_RIGHTS_LOCK} ({ADMIN_EXTENSIONS_EDIT_INSTALL_ROW_RIGHTS_COMMENT})</p>
<!-- END: ROW_RIGHTS -->
		<h3>{PHP.L.adm_pluginstall_msg10}</h3>
		<p>{PHP.L.Found}: {ADMIN_EXTENSIONS_EDIT_AFFECTEDROWS4}</p>
		<h3>{PHP.L.adm_pluginstall_msg11}</h3>
		<p>{PHP.L.Found}: {ADMIN_EXTENSIONS_EDIT_EXTPLUGIN_INFO}</p>
		<h3>{PHP.L.Done}!</h3>
		<span>{ADMIN_EXTENSIONS_EDIT_LOG}</span>
		<ul class="follow">
			<li><a href="{ADMIN_EXTENSIONS_EDIT_CONTINUE_URL}" class="ajax">{PHP.L.Clickhere}</a></li>
		</ul>
<!-- END: INSTALL -->
<!-- BEGIN: UNINSTALL -->
	<h2>{PHP.L.adm_pluginstall_msg01}</h2>
		<p>{PHP.L.Found}: {ADMIN_EXTENSIONS_EDIT_AFFECTEDROWS1}</p>
<!-- IF !{PHP.ko} -->
		<h3>{PHP.L.adm_pluginstall_msg02}</h3>
		<p>{PHP.L.Found}: {ADMIN_EXTENSIONS_EDIT_AFFECTEDROWS2}</p>
		<h3>{PHP.L.adm_pluginstall_msg08}</h3>
		<p>{PHP.L.Found}: {ADMIN_EXTENSIONS_EDIT_AFFECTEDROWS3}</p>
<!-- ENDIF -->
		<h3>{PHP.L.adm_pluginstall_msg10}</h3>
		<p>{PHP.L.Found}: {ADMIN_EXTENSIONS_EDIT_AFFECTEDROWS4}</p>
		<h3>{PHP.L.adm_pluginstall_msg11}</h3>
		<p>{PHP.L.Found}: {ADMIN_EXTENSIONS_EDIT_EXTPLUGIN_INFO}</p>
		<h3>{PHP.L.Done}</h3>
		<span>{ADMIN_EXTENSIONS_EDIT_LOG}</span>
		<ul class="follow">
			<li><a href="{ADMIN_EXTENSIONS_EDIT_CONTINUE_URL}" class="ajax">{PHP.L.Clickhere}</a></li>
		</ul>
<!-- END: UNINSTALL -->
<!-- END: EDIT -->
	</div>
<!-- END: MAIN -->