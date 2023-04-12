<!-- BEGIN: MAIN -->
{FILE "{PHP.cfg.system_dir}/admin/tpl/warnings.tpl"}

<!-- BEGIN: DEFAULT -->
<div class="button-toolbar">
	<a class="button <!-- IF {ADMIN_EXTENSIONS_SORT_ALP_SEL} -->special<!-- ENDIF -->" href="{ADMIN_EXTENSIONS_SORT_ALP_URL}">{PHP.L.adm_sort_alphabet}</a>
	<a class="button <!-- IF {ADMIN_EXTENSIONS_SORT_CAT_SEL} -->special<!-- ENDIF -->" href="{ADMIN_EXTENSIONS_SORT_CAT_URL}">{PHP.L.adm_sort_category}</a>
	<a class="button <!-- IF {ADMIN_EXTENSIONS_ONLY_INSTALLED_SEL} -->special" href="{ADMIN_EXTENSIONS_ONLY_INSTALLED_URL}"<!-- ELSE-->" href="{ADMIN_EXTENSIONS_ONLY_INSTALLED_URL}"<!-- ENDIF -->>{PHP.L.adm_only_installed}</a>
	<a href="{ADMIN_EXTENSIONS_HOOKS_URL}" class="button">{PHP.L.Hooks}</a>
</div>

<!-- BEGIN: SECTION-->
<div class="block">
	<h2>{ADMIN_EXTENSIONS_SECTION_TITLE} ({ADMIN_EXTENSIONS_CNT_EXTP})</h2>
	<div class="wrapper">
		<table class="cells">
			<thead>
				<tr>
					<th class="w-30">{PHP.L.Name} {PHP.L.adm_clicktoedit}</th>
					<th class="w-10">{PHP.L.Code}</th>
					<th class="w-10">{PHP.L.Version}</th>
					<th class="w-10">{PHP.L.Parts}</th>
					<th class="w-10">{PHP.L.Status}</th>
					<th class="w-40">{PHP.L.Action}</th>
				</tr>
			</thead>
			<tbody>
				<!-- BEGIN: ROW -->
				<!-- BEGIN: ROW_CAT -->
				<tr>
					<td colspan="6">
						<h3>{ADMIN_EXTENSIONS_CAT_TITLE}</h3>
					</td>
				</tr>
				<!-- END: ROW_CAT -->
				<!-- BEGIN: ROW_ERROR_EXT -->
				<tr>
					<td>{ADMIN_EXTENSIONS_X_ERR}</td>
					<td colspan="5">{ADMIN_EXTENSIONS_ERROR_MSG}</td>
				</tr>
				<!-- END: ROW_ERROR_EXT -->
				<tr>
					<td class="start">
						<figure>
							{ADMIN_EXTENSIONS_ICON}
						</figure>
						<div>
							<a href="{ADMIN_EXTENSIONS_DETAILS_URL}">{ADMIN_EXTENSIONS_NAME}</a>
							<p>{ADMIN_EXTENSIONS_DESCRIPTION|cot_cutstring($this,60)}</p>
						</div>
					</td>
					<td class="centerall">
						{ADMIN_EXTENSIONS_CODE_X}
					</td>
					<td class="centerall">
						<!-- IF {PHP.part_status} != 3 AND {ADMIN_EXTENSIONS_VERSION_COMPARE} > 0 -->
						<span class="highlight_red">{ADMIN_EXTENSIONS_VERSION_INSTALLED}</span> /
						<span class="highlight_green">{ADMIN_EXTENSIONS_VERSION}</span>
						<!-- ELSE -->
						{ADMIN_EXTENSIONS_VERSION}
						<!-- ENDIF -->
					</td>
					<td class="centerall">
						{ADMIN_EXTENSIONS_PARTSCOUNT}
					</td>
					<td class="centerall">
						{ADMIN_EXTENSIONS_STATUS}
					</td>
					<td class="action">
						<!-- IF {ADMIN_EXTENSIONS_TOTALCONFIG} -->
						<a title="{PHP.L.Configuration}" href="{ADMIN_EXTENSIONS_EDIT_URL}" class="button">{PHP.L.Config}</a>
						<!-- ENDIF -->
						<!-- IF {PHP.ifstruct} -->
						<a title="{PHP.L.Structure}" href="{ADMIN_EXTENSIONS_JUMPTO_URL_STRUCT}" class="button">{PHP.L.Structure}</a>
						<!-- ENDIF -->
						<!-- IF {PHP.totalinstalled} -->
						<a title="{PHP.L.Rights}" href="{ADMIN_EXTENSIONS_RIGHTS_URL}" class="button">{PHP.L.Rights}</a>
						<!-- ENDIF -->
						<!-- IF {PHP.ifthistools} -->
						<a title="{PHP.L.Administration}" href="{ADMIN_EXTENSIONS_JUMPTO_URL_TOOLS}" class="button special">{PHP.L.Admin}</a>
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
			</tbody>
		</table>
	</div>
</div>
<!-- END: SECTION -->
<!-- END: DEFAULT -->

<!-- BEGIN: DETAILS -->
<div class="block">
	<h2>{ADMIN_EXTENSIONS_TYPE} {ADMIN_EXTENSIONS_NAME}:</h2>
	<div class="wrapper">
		<!-- IF {PHP.isinstalled} AND {PHP.exists} -->
		<div class="button-toolbar">
			<!-- IF {ADMIN_EXTENSIONS_JUMPTO_URL} -->
			<a title="{PHP.L.Open}" href="{ADMIN_EXTENSIONS_JUMPTO_URL}" class="button special large">{PHP.L.Open}</a>
			<!-- ENDIF -->
			<!-- IF {ADMIN_EXTENSIONS_JUMPTO_URL_TOOLS} -->
			<a title="{PHP.L.Administration}" href="{ADMIN_EXTENSIONS_JUMPTO_URL_TOOLS}" class="button special large">{PHP.L.Administration}</a>
			<!-- ENDIF -->
			<!-- IF {ADMIN_EXTENSIONS_TOTALCONFIG} > 0 -->
			<a title="{PHP.L.Configuration}" href="{ADMIN_EXTENSIONS_CONFIG_URL}" class="button large">{PHP.L.Configuration} ({ADMIN_EXTENSIONS_TOTALCONFIG})</a>
			<!-- ENDIF -->
			<a title="{PHP.L.Rights}" href="{ADMIN_EXTENSIONS_RIGHTS}" class="button large">{PHP.L.Rights}</a>
			<!-- IF {ADMIN_EXTENSIONS_JUMPTO_URL_STRUCT} -->
			<a title="{PHP.L.Structure}" href="{ADMIN_EXTENSIONS_JUMPTO_URL_STRUCT}" class="button large">{PHP.L.Structure}</a>
			<!-- ENDIF -->
		</div>
		<!-- ENDIF -->
		<table class="cells">
			<tbody>
				<tr>
					<td class="w-25">{PHP.L.Code}:</td>
					<td class="w-75">{ADMIN_EXTENSIONS_CODE}</td>
				</tr>
				<tr>
					<td>{PHP.L.Description}:</td>
					<td>{ADMIN_EXTENSIONS_DESCRIPTION}</td>
				</tr>
				<tr>
					<td>{PHP.L.Version}:</td>
					<td>
						<!-- IF {PHP.isinstalled} AND {ADMIN_EXTENSIONS_VERSION_COMPARE} > 0 -->
						<span class="highlight_red">{ADMIN_EXTENSIONS_VERSION_INSTALLED}</span> / <span class="highlight_green">{ADMIN_EXTENSIONS_VERSION}</span>
						<!-- ELSE -->
						{ADMIN_EXTENSIONS_VERSION}
						<!-- ENDIF -->
					</td>
				</tr>
				<tr>
					<td>{PHP.L.Date}:</td>
					<td>{ADMIN_EXTENSIONS_DATE}</td>
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
				<!-- BEGIN: DEPENDENCIES -->
				<tr>
					<td>{ADMIN_EXTENSIONS_DEPENDENCIES_TITLE}:</td>
					<td>
						<ul>
							<!-- BEGIN: DEPENDENCIES_ROW -->
							<li>
								<!-- IF {ADMIN_EXTENSIONS_DEPENDENCIES_ROW_URL} != '' -->
								<a href="{ADMIN_EXTENSIONS_DEPENDENCIES_ROW_URL}" class="{ADMIN_EXTENSIONS_DEPENDENCIES_ROW_CLASS}">{ADMIN_EXTENSIONS_DEPENDENCIES_ROW_NAME}</a>
								<!-- ELSE -->
								<span class="{ADMIN_EXTENSIONS_DEPENDENCIES_ROW_CLASS}">{ADMIN_EXTENSIONS_DEPENDENCIES_ROW_NAME}</span>
								<!-- ENDIF -->
							</li>
							<!-- END: DEPENDENCIES_ROW -->
						</ul>
					</td>
				</tr>
				<!-- END: DEPENDENCIES -->
				<tr>
					<td colspan="2" class="action">
							<!-- IF !{PHP.isinstalled} AND {PHP.dependencies_satisfied} -->
							<a title="{PHP.L.adm_opt_install_explain}" href="{ADMIN_EXTENSIONS_INSTALL_URL}" class="button special large">{PHP.L.adm_opt_install}</a>
							<!-- ENDIF -->
							<!-- IF {PHP.isinstalled} -->
							<!-- IF {PHP.exists} -->
							<a title="{PHP.L.adm_opt_install_explain}" href="{ADMIN_EXTENSIONS_UPDATE_URL}" class="button special large">{PHP.L.adm_opt_update}</a>
							<!-- ENDIF -->

							<a title="{PHP.L.adm_opt_uninstall_explain}" href="{ADMIN_EXTENSIONS_UNINSTALL_URL}" class="ajax button large">{PHP.L.adm_opt_uninstall}</a>
							<a title="{PHP.L.adm_opt_pauseall_explain}" href="{ADMIN_EXTENSIONS_PAUSE_URL}" class="button large">{PHP.L.adm_opt_pauseall}</a>

							<!-- IF {PHP.exists} -->
							<a title="{PHP.L.adm_opt_unpauseall_explain}" href="{ADMIN_EXTENSIONS_UNPAUSE_URL}" class="button large">{PHP.L.adm_opt_unpauseall}</a>
							<!-- ENDIF -->
							<!-- ENDIF -->
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<div class="block">
	<h2>{PHP.L.Parts}:</h2>
	<table class="cells">
		<thead>
			<tr>
				<th class="w-5">#</th>
				<th class="w-15">{PHP.L.Part}</th>
				<th class="w-20">{PHP.L.File}</th>
				<th class="w-20">{PHP.L.Hooks}</th>
				<th class="w-10">{PHP.L.Order}</th>
				<th class="w-15">{PHP.L.Status}</th>
				<th class="w-15">{PHP.L.Action}</th>
			</tr>
		</thead>
		<tbody>
			<!-- BEGIN: ROW_ERROR_PART -->
			<tr>
				<td class="centerall">{ADMIN_EXTENSIONS_DETAILS_ROW_I_1}</td>
				<td class="centerall">{ADMIN_EXTENSIONS_DETAILS_ROW_PART}</td>
				<td class="centerall">{ADMIN_EXTENSIONS_DETAILS_ROW_FILE}</td>
				<td class="centerall">{ADMIN_EXTENSIONS_DETAILS_ROW_HOOKS}</td>
				<td colspan="3">{ADMIN_EXTENSIONS_DETAILS_ROW_ERROR}</td>
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
		</tbody>
	</table>
</div>

<div class="block">
	<h2>{PHP.L.Tags}:</h2>
	<div class="wrapper">
		<table class="cells">
			<thead>
				<tr>
					<th class="w-5">#</th>
					<th class="w-25">{PHP.L.Part}</th>
					<th class="w-70">{PHP.L.Files} / {PHP.L.Tags}</th>
				</tr>
			</thead>
			<tbody>
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
			</tbody>
		</table>
	</div>
</div>
<!-- END: DETAILS -->

<!-- BEGIN: HOOKS -->
<div class="block">
	<h2>{PHP.L.Hooks} ({ADMIN_EXTENSIONS_CNT_HOOK}):</h2>
	<div class="wrapper">
		<table class="cells">
			<thead>
				<tr>
					<th class="w-40">{PHP.L.Hooks}</th>
					<th class="w-20">{PHP.L.Plugin}</th>
					<th class="w-20">{PHP.L.Order}</th>
					<th class="w-20">{PHP.L.Active}</th>
				</tr>
			</thead>
			<tbody>
				<!-- BEGIN: HOOKS_ROW -->
				<tr>
					<td>{ADMIN_EXTENSIONS_HOOK}</td>
					<td>{ADMIN_EXTENSIONS_CODE}</td>
					<td class="centerall">{ADMIN_EXTENSIONS_ORDER}</td>
					<td class="centerall">{ADMIN_EXTENSIONS_ACTIVE}</td>
				</tr>
				<!-- END: HOOKS_ROW -->
			</tbody>
		</table>
	</div>
</div>
<!-- END: HOOKS -->

<!-- END: MAIN -->
