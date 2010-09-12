<!-- BEGIN: MAIN -->
	<div id="ajaxBlock">
		<h2>{PHP.L.Forums}</h2>
<!-- BEGIN: MESSAGE -->
		<div class="error">
			<h4>{PHP.L.Message}</h4>
			<p>{MESSAGE_TEXT}</p>
		</div>
<!-- END: MESSAGE -->
		<ul class="follow">
			<li><a title="{PHP.L.Configuration}" href="{ADMIN_FORUMS_CONF_URL}">{PHP.L.Configuration}</a></li>
<!-- IF {PHP.lincif_conf} -->
			<li><a href="{ADMIN_FORUMS_CONF_STRUCTURE_URL}">{PHP.L.adm_forum_structure}</a></li>
<!-- ELSE -->
			<li>{PHP.L.adm_forum_structure}</li>
<!-- ENDIF -->
		</ul>
<!-- BEGIN: OPTIONS -->
			<form name="savestructure" id="savestructure" action="{ADMIN_FORUMS_STRUCTURE_OPTIONS_FORM_URL}" method="post">
			<table class="cells">
				<tr>
					<td class="width20">{PHP.L.Code}:</td>
					<td class="width80">{ADMIN_FORUMS_STRUCTURE_OPTIONS_FN_CODE}</td>
				</tr>
				<tr>
					<td>{PHP.L.Path}:</td>
					<td>{ADMIN_FORUMS_STRUCTURE_OPTIONS_FN_PATH}</td>
				</tr>
				<tr>
					<td>{PHP.L.Title}:</td>
					<td>{ADMIN_FORUMS_STRUCTURE_OPTIONS_FN_TITLE}</td>
				</tr>
				<tr>
					<td>{PHP.L.Description}:</td>
					<td>{ADMIN_FORUMS_STRUCTURE_OPTIONS_FN_DESC}</td>
				</tr>
				<tr>
					<td>{PHP.L.Icon}:</td>
					<td>{ADMIN_FORUMS_STRUCTURE_OPTIONS_FN_ICON}</td>
				</tr>
				<tr>
					<td>{PHP.L.adm_defstate}:</td>
					<td>{ADMIN_FORUMS_STRUCTURE_OPTIONS_SELECT}</td>
				</tr>
				<tr>
					<td>{PHP.L.adm_tpl_mode}:</td>
					<td>{ADMIN_FORUMS_STRUCTURE_OPTIONS_CHECK}</td>
				</tr>
				<tr>
					<td class="valid" colspan="2"><input type="submit" class="submit" value="{PHP.L.Update}" /></td>
				</tr>
				</table>
			</form>
<!-- END: OPTIONS -->
<!-- BEGIN: DEFULT -->
			<h3>{PHP.L.editdeleteentries}:</h3>
			<form name="savestructure" id="savestructure" action="{ADMIN_FORUMS_STRUCTURE_FORM_URL}" method="post" class="ajax">
			<table class="cells">
				<tr>
					<td class="coltop width25">{PHP.L.Title}</td>
					<td class="coltop width10">{PHP.L.Code}</td>
					<td class="coltop width10">{PHP.L.Path}</td>
					<td class="coltop width15">{PHP.L.adm_defstate}</td>
					<td class="coltop width10">{PHP.L.TPL}</td>
					<td class="coltop width10">{PHP.L.Sections}</td>
					<td class="coltop width20">{PHP.L.Action}</td>
				</tr>
<!-- BEGIN: ROW -->
				<tr>
					<td class="centerall">{FORUMS_STRUCTURE_ROW_FN_TITLE}</td>
					<td class="centerall">{FORUMS_STRUCTURE_ROW_FN_CODE}</td>
					<td class="centerall">
<!-- IF {PHP.pathfieldimg} -->
						{PHP.R.admin_icon_join2}
<!-- ENDIF -->
						{FORUMS_STRUCTURE_ROW_FN_PATH}
					</td>
					<td class="centerall">{FORUMS_STRUCTURE_ROW_SELECT}</td>
					<td class="centerall">{FORUMS_STRUCTURE_ROW_FN_TPL_SYM}</td>
					<td class="centerall">{FORUMS_STRUCTURE_ROW_SECTIONCOUNT}</td>
					<td class="actions centerall">
						<a href="{FORUMS_STRUCTURE_ROW_OPTIONS_URL}"{FORUMS_STRUCTURE_ROW_OPTIONS_URL_AJAX} title="{PHP.L.Edit}">{PHP.R.admin_icon_config}</a>
						<a href="{FORUMS_STRUCTURE_ROW_JUMPTO_URL}"title="{PHP.L.Open}">{PHP.R.admin_icon_jumpto}</a>
<!-- IF {PHP.del_url} -->
						<a title="{PHP.L.Delete}" href="{FORUMS_STRUCTURE_ROW_DEL_URL}"{FORUMS_STRUCTURE_ROW_DEL_URL_AJAX}>{PHP.R.admin_icon_delete}</a>
<!-- ENDIF -->
					</td>
				</tr>
<!-- END: ROW -->
				<tr>
					<td class="valid" colspan="7">
						<input type="submit" class="submit" value="{PHP.L.Update}" />
					</td>
				</tr>
			</table>
			</form>
			<p class="paging">{ADMIN_FORUMS_STRUCTURE_PAGINATION_PREV}{ADMIN_FORUMS_STRUCTURE_PAGNAV}{ADMIN_FORUMS_STRUCTURE_PAGINATION_NEXT}<span class="a1">{PHP.L.Total}: {ADMIN_FORUMS_STRUCTURE_TOTALITEMS}, {PHP.L.adm_polls_on_page}: {ADMIN_FORUMS_STRUCTURE_COUNTER_ROW}</span></p>
			<h3>{PHP.L.addnewentry}:</h3>
			<form name="addstructure" id="addstructure" action="{ADMIN_FORUMS_STRUCTURE_INC_URLFORMADD}" method="post" class="ajax">
			<table class="cells">
				<tr>
					<td class="width20">{PHP.L.Code}:</td>
					<td class="width80">{ADMIN_FORUMS_STRUCTURE_CODE} {PHP.L.adm_required}</td>
				</tr>
				<tr>
					<td>{PHP.L.Path}:</td>
					<td>{ADMIN_FORUMS_STRUCTURE_PATH} {PHP.L.adm_required}</td>
				</tr>
				<tr>
					<td>{PHP.L.adm_defstate}:</td>
					<td>{ADMIN_FORUMS_STRUCTURE_SELECT}</td>
				</tr>
				<tr>
					<td>{PHP.L.Title}:</td>
					<td>{ADMIN_FORUMS_STRUCTURE_TITLE} {PHP.L.adm_required}</td>
				</tr>
				<tr>
					<td>{PHP.L.Description}:</td>
					<td>{ADMIN_FORUMS_STRUCTURE_DESC}</td>
				</tr>
				<tr>
					<td>{PHP.L.Icon}:</td>
					<td>{ADMIN_FORUMS_STRUCTURE_ICON}</td>
				</tr>
				<tr>
					<td class="valid" colspan="2"><input type="submit" class="submit" value="{PHP.L.Add}" /></td>
				</tr>
			</table>
			</form>
<!-- END: DEFULT -->
	</div>
<!-- END: MAIN -->