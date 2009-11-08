<!-- BEGIN: STRUCTURE -->
		<div id="{ADMIN_STRUCTURE_AJAX_OPENDIVID}">
			<h2>{PHP.L.Structure}</h2>
			<ul class="follow">
				<li>
					<a title="{PHP.L.Configuration}" href="{ADMIN_STRUCTURE_URL_CONFIG}">{PHP.L.Configuration}: {PHP.R.admin_icon_config}</a>
				</li>
				<li>
<!-- IF {PHP.lincif_conf} -->
					<a href="{ADMIN_STRUCTURE_URL_EXTRAFIELDS}">{PHP.L.adm_extrafields_desc}</a>
<!-- ELSE -->
					{PHP.L.adm_extrafields_desc}
<!-- ENDIF -->
				</li>
			</ul>
<!-- IF {PHP.is_adminwarnings} -->
			<div class="error">{ADMIN_STRUCTURE_ADMINWARNINGS}</div>
<!-- ENDIF -->
<!-- BEGIN: OPTIONS -->
			<form name="savestructure" id="savestructure" action="{ADMIN_STRUCTURE_UPDATE_FORM_URL}" method="post">
				<table class="cells">
				<tr>
					<td>{PHP.L.Path}:</td>
					<td><input type="text" class="text" name="rpath" value="{ADMIN_STRUCTURE_PATH}" size="16" maxlength="16" /></td>
				</tr>
				<tr>
					<td>{PHP.L.Code}:</td>
					<td><input type="text" class="text" name="rcode" value="{ADMIN_STRUCTURE_CODE}" size="16" maxlength="255" /></td>
				</tr>
				<tr>
					<td>{PHP.L.Title}:</td>
					<td><input type="text" class="text" name="rtitle" value="{ADMIN_STRUCTURE_TITLE}" size="64" maxlength="100" /></td>
				</tr>
				<tr>
					<td>{PHP.L.Description}:</td>
					<td><input type="text" class="text" name="rdesc" value="{ADMIN_STRUCTURE_DESC}" size="64" maxlength="255" /></td>
				</tr>
				<tr>
					<td>{PHP.L.Icon}:</td>
					<td><input type="text" class="text" name="ricon" value="{ADMIN_STRUCTURE_ICON}" size="64" maxlength="128" /></td>
				</tr>
				<tr>
					<td>{PHP.L.Group}:</td>
					<td><input type="checkbox" class="checkbox" name="rgroup"{ADMIN_STRUCTURE_CHECK} /></td>
				</tr>
				<tr>
					<td>{PHP.L.adm_tpl_mode}:</td>
					<td>
						<input type="radio" class="radio" name="rtplmode" value="1"{ADMIN_STRUCTURE_CHECK1} /> {PHP.L.adm_tpl_empty}<br/>
						<input type="radio" class="radio" name="rtplmode" value="2"{ADMIN_STRUCTURE_CHECK2} /> {PHP.L.adm_tpl_forced}
						<select name="rtplforced" size="1">
<!-- BEGIN: SELECT -->
							<option value="{ADMIN_STRUCTURE_OPTION_I}"{ADMIN_STRUCTURE_OPTION_SELECTED}> {ADMIN_STRUCTURE_OPTION_TPATH}</option>
<!-- END: SELECT -->
						</select>
						<br/>
						<input type="radio" class="radio" name="rtplmode" value="3"{ADMIN_STRUCTURE_CHECK3} /> {PHP.L.adm_tpl_parent}
					</td>
				</tr>
				<tr>
				<td>{PHP.L.adm_sortingorder}:</td>
				<td class="{ADMIN_STRUCTURE_ODDEVEN}">
					<select name="rorder" size="1">
<!-- BEGIN: STRUCTURE_CATORDER_SELECT_SORT -->
						<option value="{ADMIN_STRUCTURE_CATORDER_SELECT_SORT_VALUE}"{ADMIN_STRUCTURE_CATORDER_SELECT_SORT_SELECTED}>{ADMIN_STRUCTURE_CATORDER_SELECT_SORT_NAME}</option>
<!-- END: STRUCTURE_CATORDER_SELECT_SORT -->
					</select>
					&nbsp;
					<select name="rway" size="1">
<!-- BEGIN: STRUCTURE_CATORDER_SELECT_WAY -->
						<option value="{ADMIN_STRUCTURE_CATORDER_SELECT_WAY_VALUE}"{ADMIN_STRUCTURE_CATORDER_SELECT_WAY_SELECTED}>{ADMIN_STRUCTURE_CATORDER_SELECT_WAY_NAME}</option>
<!-- END: STRUCTURE_CATORDER_SELECT_WAY -->
					</select>
				</td>
				</tr>
				<tr>
					<td>{PHP.L.adm_enablecomments} :</td>
<!-- IF {PHP.structure_comments} -->
					<td><input type="radio" class="radio" name="rallowcomments" value="1" checked="checked" />{PHP.L.Yes} <input type="radio" class="radio" name="rallowcomments" value="0" />{PHP.L.No}</td>
<!-- ELSE -->
					<td><input type="radio" class="radio" name="rallowcomments" value="1" />{PHP.L.Yes} <input type="radio" class="radio" name="rallowcomments" value="0" checked="checked" />{PHP.L.No}</td>
<!-- ENDIF -->
				</tr>
				<tr>
					<td>{PHP.L.adm_enableratings} :</td>
<!-- IF {PHP.structure_ratings} -->
					<td><input type="radio" class="radio" name="rallowratings" value="1" checked="checked" />{PHP.L.Yes} <input type="radio" class="radio" name="rallowratings" value="0" />{PHP.L.No}</td>
<!-- ELSE -->
					<td><input type="radio" class="radio" name="rallowratings" value="1" />{PHP.L.Yes} <input type="radio" class="radio" name="rallowratings" value="0" checked="checked" />{PHP.L.No}</td>
<!-- ENDIF -->
				</tr>
				<tr>
					<td>{PHP.L.adm_postcounters} :</td>
					<td><a href="{ADMIN_STRUCTURE_RESYNC}"{ADMIN_STRUCTURE_RESYNC_AJAX}>{PHP.L.Resync}</a></td>
				</tr>
				<tr>
					<td colspan="2"><input type="submit" class="submit" value="{PHP.L.Update}" /></td>
				</tr>
				</table>
			</form>
<!-- END: OPTIONS -->
<!-- BEGIN: DEFULT -->
			<h4>{PHP.L.editdeleteentries} :</h4>
			<form name="savestructure" id="savestructure" action="{ADMIN_STRUCTURE_UPDATE_FORM_URL}" method="post"{ADMIN_STRUCTURE_UPDATE_FORM_URL_AJAX}>
				<table class="cells">
				<tr>
					<td class="coltop">{PHP.L.Path}</td>
					<td class="coltop">{PHP.L.Code}</td>
					<td class="coltop">{PHP.L.Title}</td>
					<td class="coltop">{PHP.L.TPL}</td>
					<td class="coltop">{PHP.L.Group}</td>
					<td class="coltop">{PHP.L.Order}</td>
					<td class="coltop">{PHP.L.Pages}</td>
					<td class="coltop" colspan="2">{PHP.L.Rights}<br />{PHP.L.Options}</td>
					<td class="coltop">{PHP.L.Delete}</td>
				</tr>
<!-- BEGIN: ROW -->
				<tr>
					<td class="{ADMIN_STRUCTURE_ODDEVEN}" style="vertical-align:middle;">{ADMIN_STRUCTURE_PATHFIELDIMG}<input type="text" class="text" name="s[{ADMIN_STRUCTURE_ID}][rpath]" value="{ADMIN_STRUCTURE_PATH}" size="{ADMIN_STRUCTURE_PATHFIELDLEN}" maxlength="24" /></td>
					<td class="{ADMIN_STRUCTURE_ODDEVEN}" style="text-align:center;vertical-align:middle;"><input type="text" class="text" name="s[{ADMIN_STRUCTURE_ID}][rcode]" value="{ADMIN_STRUCTURE_CODE}" size="8" maxlength="255" /></td>
					<td class="{ADMIN_STRUCTURE_ODDEVEN}" style="text-align:center;vertical-align:middle;"><input type="text" class="text" name="s[{ADMIN_STRUCTURE_ID}][rtitle]" value="{ADMIN_STRUCTURE_TITLE}" size="24" maxlength="100" /></td>
					<td style="text-align:center;vertical-align:middle;" class="{ADMIN_STRUCTURE_ODDEVEN}">{ADMIN_STRUCTURE_TPL_SYM}</td>
					<td style="text-align:center;vertical-align:middle;" class="{ADMIN_STRUCTURE_ODDEVEN}"><input type="checkbox" class="checkbox" name="s[{ADMIN_STRUCTURE_ID}][rgroup]"{ADMIN_STRUCTURE_CHECKED} /></td>
					<td style="text-align:center;" class="{ADMIN_STRUCTURE_ODDEVEN}">
						<select name="s[{ADMIN_STRUCTURE_ID}][rorder]" size="1" style="width:85px;">
<!-- BEGIN: STRUCTURE_CATORDER_SELECT_SORT -->
							<option value="{ADMIN_STRUCTURE_CATORDER_SELECT_SORT_VALUE}"{ADMIN_STRUCTURE_CATORDER_SELECT_SORT_SELECTED}>{ADMIN_STRUCTURE_CATORDER_SELECT_SORT_NAME}</option>
<!-- END: STRUCTURE_CATORDER_SELECT_SORT -->
						</select>
						<br />
						<select name="s[{ADMIN_STRUCTURE_ID}][rway]" size="1" style="width:85px;">
<!-- BEGIN: STRUCTURE_CATORDER_SELECT_WAY -->
							<option value="{ADMIN_STRUCTURE_CATORDER_SELECT_WAY_VALUE}"{ADMIN_STRUCTURE_CATORDER_SELECT_WAY_SELECTED}>{ADMIN_STRUCTURE_CATORDER_SELECT_WAY_NAME}</option>
<!-- END: STRUCTURE_CATORDER_SELECT_WAY -->
						</select>
					</td>
					<td style="text-align:center;vertical-align:middle;" class="{ADMIN_STRUCTURE_ODDEVEN}">{ADMIN_STRUCTURE_PAGECOUNT} <a href="{ADMIN_STRUCTURE_JUMPTO_URL}" title="{PHP.L.Pages}" >{PHP.R.admin_icon_jumpto}</a></td>
					<td style="text-align:center;vertical-align:middle;" class="{ADMIN_STRUCTURE_ODDEVEN}"><a title="{PHP.L.Rights}" href="{ADMIN_STRUCTURE_RIGHTS_URL}">{PHP.R.admin_icon_rights2}</a></td>
					<td style="text-align:center;vertical-align:middle;" class="{ADMIN_STRUCTURE_ODDEVEN}"><a title="{PHP.L.Options}" href="{ADMIN_STRUCTURE_OPTIONS_URL}"{ADMIN_STRUCTURE_OPTIONS_URL_AJAX}>{PHP.R.admin_icon_manual}</a></td>
					<td style="text-align:center;vertical-align:middle;" class="{ADMIN_STRUCTURE_ODDEVEN}">
<!-- IF {PHP.dozvil} -->
						<a title="{PHP.L.Delete}" href="{ADMIN_STRUCTURE_UPDATE_DEL_URL}"{ADMIN_STRUCTURE_UPDATE_DEL_URL_AJAX}>{PHP.R.admin_icon_delete}</a>
<!-- ENDIF -->
					</td>
				</tr>
<!-- END: ROW -->
				<tr>
					<td colspan="10"><div class="pagnav">{ADMIN_STRUCTURE_PAGINATION_PREV} {ADMIN_STRUCTURE_PAGNAV} {ADMIN_STRUCTURE_PAGINATION_NEXT}</div></td>
				</tr>
				<tr>
					<td colspan="10">{PHP.L.Total} : {ADMIN_STRUCTURE_TOTALITEMS}, {PHP.L.adm_polls_on_page}: {ADMIN_STRUCTURE_COUNTER_ROW}</td>
				</tr>
				<tr>
					<td colspan="10"><input type="submit" class="submit" value="{PHP.L.Update}" /></td>
				</tr>
				<tr>
					<td colspan="10"><a href="{ADMIN_PAGE_STRUCTURE_RESYNCALL}"{ADMIN_PAGE_STRUCTURE_RESYNCALL_AJAX}>{PHP.L.Resync}</a></td>
				</tr>
				</table>
			</form>
			<h4>{PHP.L.addnewentry} :</h4>
			<form name="addstructure" id="addstructure" action="{ADMIN_STRUCTURE_URL_FORM_ADD}" method="post"{ADMIN_STRUCTURE_URL_FORM_ADD_AJAX}>
				<table class="cells">
				<tr>
					<td>{PHP.L.Path} :</td>
					<td><input type="text" class="text" name="npath" value="" size="16" maxlength="16" /> {PHP.L.adm_required}</td>
				</tr>
				<tr>
					<td style="width:160px;">{PHP.L.Code} :</td>
					<td><input type="text" class="text" name="ncode" value="" size="16" maxlength="255" /> {PHP.L.adm_required}</td>
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
					<td>{PHP.L.Group} :</td>
					<td><input type="checkbox" class="checkbox" name="ngroup" /></td>
				</tr>
				<tr>
					<td>{PHP.L.Order}</td>
					<td>
						<select name="norder" size="1">
<!-- BEGIN: STRUCTURE_CATORDER_SORT -->
							<option value="{ADMIN_STRUCTURE_CATORDER_SORT_VALUE}"{ADMIN_STRUCTURE_CATORDER_SORT_SELECTED}>{ADMIN_STRUCTURE_CATORDER_SORT_NAME}</option>
<!-- END: STRUCTURE_CATORDER_SORT -->
						</select>
						&nbsp;
						<select name="nway" size="1">
<!-- BEGIN: STRUCTURE_CATORDER_WAY -->
							<option value="{ADMIN_STRUCTURE_CATORDER_WAY_VALUE}"{ADMIN_STRUCTURE_CATORDER_WAY_SELECTED}>{ADMIN_STRUCTURE_CATORDER_WAY_NAME}</option>
<!-- END: STRUCTURE_CATORDER_WAY -->
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2"><input type="submit" class="submit" value="{PHP.L.Add}" /></td>
				</tr>
				</table>
			</form>
<!-- END: DEFULT -->
		</div>
<!-- END: STRUCTURE -->