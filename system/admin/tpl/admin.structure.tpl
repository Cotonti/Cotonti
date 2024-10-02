<!-- BEGIN: LIST -->
<div class="block">
	<table class="cells">
		<tbody>
			<!-- BEGIN: ADMIN_STRUCTURE_EXT -->
			<tr>
				<td class="start">
					<figure>{ADMIN_STRUCTURE_EXT_ICON}</figure>
					<div>
						<a href="{ADMIN_STRUCTURE_EXT_URL}">{ADMIN_STRUCTURE_EXT_NAME}</a>
						<!-- IF {ADMIN_STRUCTURE_EXT_DESC} --><p>{ADMIN_STRUCTURE_EXT_DESC}</p><!-- ENDIF -->
					</div>
				</td>
			</tr>
			<!-- END: ADMIN_STRUCTURE_EXT -->
			<!-- BEGIN: ADMIN_STRUCTURE_EMPTY -->
			<tr>
				<td colspan="2">{PHP.L.adm_listisempty}</td>
			</tr>
			<!-- END: ADMIN_STRUCTURE_EMPTY -->
		</tbody>
	</table>
</div>
<!-- END: LIST -->

<!-- BEGIN: MAIN -->
<div class="button-toolbar">
	<a href="{ADMIN_STRUCTURE_URL_EXTRAFIELDS}" class="button">{PHP.L.adm_extrafields}</a>
	<a href="{ADMIN_PAGE_STRUCTURE_RESYNCALL}" class="button ajax special" title="{PHP.L.adm_tpl_resyncalltitle}">{PHP.L.Resync}</a>
	<!-- IF {ADMIN_STRUCTURE_I18N_URL} -->
	<a href="{ADMIN_STRUCTURE_I18N_URL}" class="button">{PHP.L.i18n_structure}</a>
	<!-- ENDIF -->
</div>

{FILE "{PHP.cfg.system_dir}/admin/tpl/warnings.tpl"}

<!-- BEGIN: DEFAULT -->
<!-- IF {ADMIN_STRUCTURE_TOTALITEMS} > 0 -->
<div class="block">
	<form name="savestructure" id="savestructure" action="{ADMIN_STRUCTURE_UPDATE_FORM_URL}" method="post" class="ajax" enctype="multipart/form-data" >
		<table class="cells">
			<tr>
				<td class="coltop">{PHP.L.Path}</td>
				<td class="coltop w-10">{PHP.L.Code}</td>
				<td class="coltop w-25">{PHP.L.Title}</td>
				<td class="coltop">{PHP.L.TPL}</td>
				<td class="coltop w-5">{PHP.L.Pages}</td>
				<td class="coltop w-25">{PHP.L.Action}</td>
			</tr>
			<!-- BEGIN: ROW -->
			<tr>
				<td class="{ADMIN_STRUCTURE_ODDEVEN}">{ADMIN_STRUCTURE_PATH}</td>
				<td class="{ADMIN_STRUCTURE_ODDEVEN}">{ADMIN_STRUCTURE_CODE}</td>
				<td class="{ADMIN_STRUCTURE_ODDEVEN}">{ADMIN_STRUCTURE_TITLE}</td>
				<td class="{ADMIN_STRUCTURE_ODDEVEN}">{ADMIN_STRUCTURE_TPL_CODE}</td>
				<td class="{ADMIN_STRUCTURE_ODDEVEN} text-center">{ADMIN_STRUCTURE_COUNT}</td>
				<td class="action {ADMIN_STRUCTURE_ODDEVEN}">
					<a href="{ADMIN_STRUCTURE_OPTIONS_URL}" class="button ajax" title="{PHP.L.Options}">{PHP.L.Config}</a>
					<!-- IF {ADMIN_STRUCTURE_RIGHTS_URL} -->
					<a href="{ADMIN_STRUCTURE_RIGHTS_URL}" class="button">{PHP.L.Rights}</a>
					<!-- ENDIF -->
					<!-- IF {ADMIN_STRUCTURE_CAN_DELETE} -->
					<a href="{ADMIN_STRUCTURE_DELETE_CONFIRM_URL}" class="button confirmLink">{PHP.L.Delete}</a>
					<!-- ENDIF -->
					<a href="{ADMIN_STRUCTURE_JUMPTO_URL}" class="button special" title="{PHP.L.Pages}">{PHP.L.Open}</a>
				</td>
			</tr>
			<!-- END: ROW -->
			<tr>
				<td colspan="8">
					<input type="submit" class="submit" value="{PHP.L.Update}" />
				</td>
			</tr>
			</table>
		</form>
		<!-- IF {ADMIN_STRUCTURE_TOTALITEMS} -->
		<p class="paging">
			{ADMIN_STRUCTURE_PAGINATION_PREV}{ADMIN_STRUCTURE_PAGNAV}{ADMIN_STRUCTURE_PAGINATION_NEXT}
			<span>{PHP.L.Total}: {ADMIN_STRUCTURE_TOTALITEMS}, {PHP.L.Onpage}: {ADMIN_STRUCTURE_COUNTER_ROW}</span>
		</p>
		<!-- ENDIF -->
</div>
<!-- ENDIF -->
<!-- END: DEFAULT -->

<!-- BEGIN: OPTIONS -->
<form name="savestructure" id="savestructure" action="{ADMIN_STRUCTURE_UPDATE_FORM_URL}" method="post" enctype="multipart/form-data">
	<div class="block">
		<h2>{PHP.L.Configuration}</h2>
		<div class="wrapper">
			<table class="cells">
				<tr>
					<td class="w-20">{PHP.L.Path}:</td>
					<td class="w-80">{ADMIN_STRUCTURE_PATH}</td>
				</tr>
				<tr>
					<td>{PHP.L.Code}:</td>
					<td>{ADMIN_STRUCTURE_CODE}</td>
				</tr>
				<tr>
					<td>{PHP.L.Title}:</td>
					<td>{ADMIN_STRUCTURE_TITLE}</td>
				</tr>
				<tr>
					<td>{PHP.L.Description}:</td>
					<td>{ADMIN_STRUCTURE_DESC}</td>
				</tr>
				<tr>
					<td>{PHP.L.Icon}:</td>
					<td>{ADMIN_STRUCTURE_ICON}</td>
				</tr>
				<tr>
					<td>{PHP.L.Locked}:</td>
					<td>{ADMIN_STRUCTURE_LOCKED}</td>
				</tr>
				<tr>
					<td>{PHP.L.adm_tpl_mode}:</td>
					<td>{ADMIN_STRUCTURE_TPL}</td>
				</tr>
				<!-- BEGIN: EXTRAFLD -->
				<tr>
					<td>{ADMIN_STRUCTURE_EXTRAFLD_TITLE}:</td>
					<td class="{ADMIN_STRUCTURE_ODDEVEN}">{ADMIN_STRUCTURE_EXTRAFLD}</td>
				</tr>
				<!-- END: EXTRAFLD -->
			</table>
		</div>
	</div>

	<!-- BEGIN: CONFIG -->
	<div class="block">
		<h2>{PHP.L.Options}</h2>
		{CONFIG_HIDDEN}
		{ADMIN_CONFIG_EDIT_CUSTOM}
		<div class="wrapper">
			<table class="cells">
				<tr>
					<td class="w-35">{PHP.L.Parameter}</td>
					<td class="w-60">{PHP.L.Value}</td>
					<td class="w-5">{PHP.L.Reset}</td>
				</tr>
				<!-- BEGIN: ADMIN_CONFIG_ROW -->
				<!-- BEGIN: ADMIN_CONFIG_FIELDSET_BEGIN -->
				<tr>
					<td class="group_begin" colspan="3">
						<h4>{ADMIN_CONFIG_FIELDSET_TITLE}</h4>
					</td>
				</tr>
				<!-- END: ADMIN_CONFIG_FIELDSET_BEGIN -->
				<!-- BEGIN: ADMIN_CONFIG_ROW_OPTION -->
				<tr>
					<td>{ADMIN_CONFIG_ROW_CONFIG_TITLE}:</td>
					<td>
						{ADMIN_CONFIG_ROW_CONFIG}
						<div class="adminconfigmore">{ADMIN_CONFIG_ROW_CONFIG_MORE}</div>
					</td>
					<td class="centerall">
						<a href="{ADMIN_CONFIG_ROW_CONFIG_MORE_URL}" class="ajax button">{PHP.L.Reset}</a>
					</td>
				</tr>
				<!-- END: ADMIN_CONFIG_ROW_OPTION -->
				<!-- END: ADMIN_CONFIG_ROW -->
			</table>
		</div>
	</div>
	<!-- END: CONFIG -->

	<div class="button-toolbar">
		<input type="submit" class="submit" value="{PHP.L.Update}" />
	</div>
</form>
<!-- END: OPTIONS -->

<!-- BEGIN: NEWCAT -->
<div class="block">
	<h2>{PHP.L.Add}:</h2>
	<div class="wrapper">
		<form name="addstructure" id="addstructure" action="{ADMIN_STRUCTURE_URL_FORM_ADD}" method="post" class="ajax" enctype="multipart/form-data">
			<table class="cells">
				<tfoot>
					<tr>
						<td colspan="2">
							<input type="submit" class="submit" value="{PHP.L.Add}" />
						</td>
					</tr>
				</tfoot>
				<tbody>
					<tr>
						<td class="w-20">{PHP.L.Path}:</td>
						<td class="w-80">{ADMIN_STRUCTURE_PATH} {PHP.L.adm_required}</td>
					</tr>
					<tr>
						<td>{PHP.L.Code}:</td>
						<td>{ADMIN_STRUCTURE_CODE} {PHP.L.adm_required}</td>
					</tr>
					<tr>
						<td>{PHP.L.Title}:</td>
						<td>{ADMIN_STRUCTURE_TITLE} {PHP.L.adm_required}</td>
					</tr>
					<tr>
						<td>{PHP.L.Description}:</td>
						<td>{ADMIN_STRUCTURE_DESC}</td>
					</tr>
					<tr>
						<td>{PHP.L.Icon}:</td>
						<td>{ADMIN_STRUCTURE_ICON}</td>
					</tr>
					<!-- IF {ADMIN_STRUCTURE_TPL} -->
					<tr>
						<td>{PHP.L.adm_tpl_mode}:</td>
						<td>{ADMIN_STRUCTURE_TPL}</td>
					</tr>
					<!-- ENDIF -->
					<tr>
						<td>{PHP.L.Locked}:</td>
						<td>{ADMIN_STRUCTURE_LOCKED}</td>
					</tr>
					<!-- BEGIN: EXTRAFLD -->
					<tr>
						<td>{ADMIN_STRUCTURE_EXTRAFLD_TITLE}:</td>
						<td>{ADMIN_STRUCTURE_EXTRAFLD}</td>
					</tr>
					<!-- END: EXTRAFLD -->
				</tbody>
			</table>
		</form>
	</div>
</div>
<!-- END: NEWCAT -->
<!-- END: MAIN -->