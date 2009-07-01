<!-- BEGIN: FORUMS_STRUCTURE -->
		<div id="{ADMIN_FORUMS_STRUCTURE_AJAX_OPENDIVID}">
<!-- IF {PHP.is_adminwarnings} -->
			<div class="error">{ADMIN_FORUMS_STRUCTURE_ADMINWARNINGS}</div>
<!-- ENDIF -->
<!-- BEGIN: OPTIONS -->
			<form name="savestructure" id="savestructure" action="{ADMIN_FORUMS_STRUCTURE_OPTIONS_FORM_URL}" method="post">
				<table class="cells">
				<tr>
					<td>{PHP.L.Code} :</td>
					<td>{ADMIN_FORUMS_STRUCTURE_OPTIONS_FN_CODE}</td>
				</tr>
				<tr>
					<td>{PHP.L.Path} :</td>
					<td><input type="text" class="text" name="rpath" value="{ADMIN_FORUMS_STRUCTURE_OPTIONS_FN_PATH}" size="16" maxlength="16" /></td>
				</tr>
				<tr>
					<td>{PHP.L.Title} :</td>
					<td><input type="text" class="text" name="rtitle" value="{ADMIN_FORUMS_STRUCTURE_OPTIONS_FN_TITLE}" size="64" maxlength="100" /></td>
				</tr>
				<tr>
					<td>{PHP.L.Description} :</td>
					<td><input type="text" class="text" name="rdesc" value="{ADMIN_FORUMS_STRUCTURE_OPTIONS_FN_DESC}" size="64" maxlength="255" /></td>
				</tr>
				<tr>
					<td>{PHP.L.Icon} :</td>
					<td><input type="text" class="text" name="ricon" value="{ADMIN_FORUMS_STRUCTURE_OPTIONS_FN_ICON}" size="64" maxlength="128" /></td>
				</tr>
				<tr>
					<td>{PHP.L.adm_defstate} :</td>
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
					<td>{PHP.L.adm_tpl_mode} :</td>
					<td>
						<input type="radio" class="radio" name="rtplmode" value="1"{ADMIN_FORUMS_STRUCTURE_OPTIONS_CHECK1} /> {PHP.L.adm_tpl_empty}<br/>
						<input type="radio" class="radio" name="rtplmode" value="3"{ADMIN_FORUMS_STRUCTURE_OPTIONS_CHECK3} /> {PHP.L.adm_tpl_parent}
					</td>
				</tr>
				<tr>
					<td colspan="2"><input type="submit" class="submit" value="{PHP.L.Update}" /></td>
				</tr>
				</table>
			</form>
<!-- END: OPTIONS -->
<!-- BEGIN: DEFULT -->
			<h4>{PHP.L.editdeleteentries} :</h4>
			<form name="savestructure" id="savestructure" action="{ADMIN_FORUMS_STRUCTURE_FORM_URL}" method="post"{ADMIN_FORUMS_STRUCTURE_FORM_URL_AJAX}>
				<table class="cells">
				<tr>
					<td class="coltop">{PHP.L.Delete}</td>
					<td class="coltop">{PHP.L.Code}</td>
					<td class="coltop">{PHP.L.Path}</td>
					<td class="coltop">{PHP.L.adm_defstate}</td>
					<td class="coltop">{PHP.L.TPL}</td>
					<td class="coltop">{PHP.L.Title}</td>
					<td class="coltop">{PHP.L.Sections}</td>
					<td class="coltop">{PHP.L.Options} {PHP.L.adm_clicktoedit}</td>
				</tr>
<!-- BEGIN: ROW -->
				<tr>
					<td style="text-align:center;">
<!-- IF {PHP.del_url} -->
						[<a href="{FORUMS_STRUCTURE_ROW_DEL_URL}"{FORUMS_STRUCTURE_ROW_DEL_URL_AJAX}>x</a>]
<!-- ENDIF -->
					</td>
					<td>{FORUMS_STRUCTURE_ROW_FN_CODE}</td>
					<td>
<!-- IF {PHP.pathfieldimg} -->
						<img src="images/admin/join2.gif" alt="" />
<!-- ENDIF -->
						<input type="text" class="text" name="{FORUMS_STRUCTURE_ROW_INPUT_PATH_NAME}" value="{FORUMS_STRUCTURE_ROW_FN_PATH}" size="{FORUMS_STRUCTURE_ROW_PATHFIELDLEN}" maxlength="24" /></td>
					<td style="text-align:center;">
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
					<td style="text-align:center;">{FORUMS_STRUCTURE_ROW_FN_TPL_SYM}</td>
					<td><input type="text" class="text" name="{FORUMS_STRUCTURE_ROW_INPUT_TITLE_NAME}" value="{FORUMS_STRUCTURE_ROW_FN_TITLE}" size="24" maxlength="100" /></td>
					<td style="text-align:right;">{FORUMS_STRUCTURE_ROW_SECTIONCOUNT} <a href="{FORUMS_STRUCTURE_ROW_JUMPTO_URL}"><img src="images/admin/jumpto.gif" alt="" /></a></td>
					<td style="text-align:center;"><a href="{FORUMS_STRUCTURE_ROW_OPTIONS_URL}"}>{PHP.L.Options}</a></td>
				</tr>
<!-- END: ROW -->
				<tr>
					<td colspan="9"><div class="pagnav">{ADMIN_FORUMS_STRUCTURE_PAGINATION_PREV} {ADMIN_FORUMS_STRUCTURE_PAGNAV} {ADMIN_FORUMS_STRUCTURE_PAGINATION_NEXT}</div></td>
				</tr>
				<tr>
					<td colspan="9">{PHP.L.Total} : {ADMIN_FORUMS_STRUCTURE_TOTALITEMS}, {PHP.L.adm_polls_on_page}: {ADMIN_FORUMS_STRUCTURE_COUNTER_ROW}</td>
				</tr>
				<tr>
					<td colspan="9"><input type="submit" class="submit" value="{PHP.L.Update}" /></td>
				</tr>
				</table>
			</form>
			<h4>{PHP.L.addnewentry} :</h4>
			<form name="addstructure" id="addstructure" action="{ADMIN_FORUMS_STRUCTURE_INC_URLFORMADD}" method="post"{ADMIN_FORUMS_STRUCTURE_INC_URLFORMADD_AJAX}>
			<table class="cells">
			<tr>
				<td style="width:160px;">{PHP.L.Code} :</td>
				<td><input type="text" class="text" name="ncode" value="" size="16" maxlength="16" /> {PHP.L.adm_required}</td>
			</tr>
			<tr>
				<td>{PHP.L.Path} :</td>
				<td><input type="text" class="text" name="npath" value="" size="16" maxlength="16" /> {PHP.L.adm_required}</td>
			</tr>
			<tr>
				<td>{PHP.L.adm_defstate} :</td>
				<td><input type="radio" class="radio" name="ndefstate" value="1" checked="checked" />{PHP.L.adm_defstate_1} <input type="radio" class="radio" name="ndefstate" value="0" />{PHP.L.adm_defstate_0}</td>
			</tr>
			<tr>
				<td>{PHP.L.Title} :</td>
				<td><input type="text" class="text" name="ntitle" value="" size="48" maxlength="100" /> {PHP.L.adm_required}</td>
			</tr>
			<tr>
				<td>{PHP.L.Description} :</td>
				<td><input type="text" class="text" name="ndesc" value="" size="48" maxlength="255" /></td>
			</tr>
			<tr>
				<td>{PHP.L.Icon} :</td>
				<td><input type="text" class="text" name="nicon" value="" size="48" maxlength="128" /></td>
			</tr>
			<tr>
				<td colspan="2"><input type="submit" class="submit" value="{PHP.L.Add}" /></td>
			</tr>
			</table>
			</form>
<!-- END: DEFULT -->
		</div>
<!-- END: FORUMS_STRUCTURE -->