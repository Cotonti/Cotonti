<!-- BEGIN: FORUMS_STRUCTURE -->
	<div id="ajax_tab">
		<h2>{PHP.L.Forums}</h2>
<!-- IF {PHP.is_adminwarnings} -->
			<div class="error">
				<h4>{PHP.L.Message}</h4>
				<p>{ADMIN_FORUMS_STRUCTURE_ADMINWARNINGS}</p>
			</div>
<!-- ENDIF -->
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
					<td style="width:20%;">{PHP.L.Code}:</td>
					<td style="width:80%;">{ADMIN_FORUMS_STRUCTURE_OPTIONS_FN_CODE}</td>
				</tr>
				<tr>
					<td>{PHP.L.Path}:</td>
					<td><input type="text" class="text" name="rpath" value="{ADMIN_FORUMS_STRUCTURE_OPTIONS_FN_PATH}" size="16" maxlength="16" /></td>
				</tr>
				<tr>
					<td>{PHP.L.Title}:</td>
					<td><input type="text" class="text" name="rtitle" value="{ADMIN_FORUMS_STRUCTURE_OPTIONS_FN_TITLE}" size="64" maxlength="100" /></td>
				</tr>
				<tr>
					<td>{PHP.L.Description}:</td>
					<td><input type="text" class="text" name="rdesc" value="{ADMIN_FORUMS_STRUCTURE_OPTIONS_FN_DESC}" size="64" maxlength="255" /></td>
				</tr>
				<tr>
					<td>{PHP.L.Icon}:</td>
					<td><input type="text" class="text" name="ricon" value="{ADMIN_FORUMS_STRUCTURE_OPTIONS_FN_ICON}" size="64" maxlength="128" /></td>
				</tr>
				<tr>
					<td>{PHP.L.adm_defstate}:</td>
					<td>
						<select name="rdefstate" size="1">
<!-- IF {PHP.selected} -->
							<option value="1" selected="selected">{PHP.L.adm_defstate_1}</option>
							<option value="0">{PHP.L.adm_defstate_0}</option>
<!-- ELSE -->
							<option value="1">{PHP.L.adm_defstate_1}</option>
							<option value="0" selected="selected">{PHP.L.adm_defstate_0}</option>
<!-- ENDIF -->
						</select>
					</td>
				</tr>
				<tr>
					<td>{PHP.L.adm_tpl_mode}:</td>
					<td>
						<input type="radio" class="radio" name="rtplmode" value="1"{ADMIN_FORUMS_STRUCTURE_OPTIONS_CHECK1} /> {PHP.L.adm_tpl_empty}<br/>
						<input type="radio" class="radio" name="rtplmode" value="3"{ADMIN_FORUMS_STRUCTURE_OPTIONS_CHECK3} /> {PHP.L.adm_tpl_parent}
					</td>
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
					<td class="coltop" style="width:25%;">{PHP.L.Title}</td>
					<td class="coltop" style="width:10%;">{PHP.L.Code}</td>
					<td class="coltop" style="width:10%;">{PHP.L.Path}</td>
					<td class="coltop" style="width:15%;">{PHP.L.adm_defstate}</td>
					<td class="coltop" style="width:10%;">{PHP.L.TPL}</td>
					<td class="coltop" style="width:10%;">{PHP.L.Sections}</td>
					<td class="coltop" style="width:20%;">{PHP.L.Action}</td>
				</tr>
<!-- BEGIN: ROW -->
				<tr>
					<td class="centerall">
						<input type="text" class="text" name="{FORUMS_STRUCTURE_ROW_INPUT_TITLE_NAME}" value="{FORUMS_STRUCTURE_ROW_FN_TITLE}" size="24" maxlength="100" />
					</td>
					<td class="centerall">{FORUMS_STRUCTURE_ROW_FN_CODE}</td>
					<td class="centerall">
<!-- IF {PHP.pathfieldimg} -->
						{PHP.R.admin_icon_join2}
<!-- ENDIF -->
						<input type="text" class="text" name="{FORUMS_STRUCTURE_ROW_INPUT_PATH_NAME}" value="{FORUMS_STRUCTURE_ROW_FN_PATH}" size="{FORUMS_STRUCTURE_ROW_PATHFIELDLEN}" maxlength="24" />
					</td>
					<td class="centerall">
						<select name="{FORUMS_STRUCTURE_ROW_SELECT_NAME}" size="1">
<!-- IF {PHP.selected} -->
							<option value="1" selected="selected">{PHP.L.adm_defstate_1}</option>
							<option value="0">{PHP.L.adm_defstate_0}</option>
<!-- ELSE -->
							<option value="1">{PHP.L.adm_defstate_1}</option>
							<option value="0" selected="selected">{PHP.L.adm_defstate_0}</option>
<!-- ENDIF -->
						</select>
					</td>
					<td class="centerall">
						{FORUMS_STRUCTURE_ROW_FN_TPL_SYM}
					</td>
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
					<td style="width:20%;">{PHP.L.Code}:</td>
					<td style="width:80%;"><input type="text" class="text" name="ncode" value="" size="16" maxlength="16" /> {PHP.L.adm_required}</td>
				</tr>
				<tr>
					<td>{PHP.L.Path}:</td>
					<td><input type="text" class="text" name="npath" value="" size="16" maxlength="16" /> {PHP.L.adm_required}</td>
				</tr>
				<tr>
					<td>{PHP.L.adm_defstate}:</td>
					<td><input type="radio" class="radio" name="ndefstate" value="1" checked="checked" />{PHP.L.adm_defstate_1} <input type="radio" class="radio" name="ndefstate" value="0" />{PHP.L.adm_defstate_0}</td>
				</tr>
				<tr>
					<td>{PHP.L.Title}:</td>
					<td><input type="text" class="text" name="ntitle" value="" size="48" maxlength="100" /> {PHP.L.adm_required}</td>
				</tr>
				<tr>
					<td>{PHP.L.Description}:</td>
					<td><input type="text" class="text" name="ndesc" value="" size="48" maxlength="255" /></td>
				</tr>
				<tr>
					<td>{PHP.L.Icon}:</td>
					<td><input type="text" class="text" name="nicon" value="" size="48" maxlength="128" /></td>
				</tr>
				<tr>
					<td class="valid" colspan="2"><input type="submit" class="submit" value="{PHP.L.Add}" /></td>
				</tr>
			</table>
			</form>
<!-- END: DEFULT -->
	</div>
<!-- END: FORUMS_STRUCTURE -->