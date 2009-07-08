<!-- BEGIN: PAGE_STRUCTURE -->
		<div id="{ADMIN_PAGE_STRUCTURE_AJAX_OPENDIVID}">
<!-- IF {PHP.is_adminwarnings} -->
			<div class="error">{ADMIN_PAGE_STRUCTURE_ADMINWARNINGS}</div>
<!-- ENDIF -->
<!-- BEGIN: OPTIONS -->
			<form name="savestructure" id="savestructure" action="{ADMIN_PAGE_STRUCTURE_UPDATE_FORM_URL}" method="post">
				<table class="cells">
				<tr>
					<td>{PHP.L.Code} :</td>
					<td><input type="text" class="text" name="rcode" value="{ADMIN_PAGE_STRUCTURE_CODE}" size="16" maxlength="255" /></td>
				</tr>
				<tr>
					<td>{PHP.L.Path} :</td>
					<td><input type="text" class="text" name="rpath" value="{ADMIN_PAGE_STRUCTURE_PATH}" size="16" maxlength="16" /></td>
				</tr>
				<tr>
					<td>{PHP.L.Title} :</td>
					<td><input type="text" class="text" name="rtitle" value="{ADMIN_PAGE_STRUCTURE_TITLE}" size="64" maxlength="100" /></td>
				</tr>
				<tr>
					<td>{PHP.L.Description} :</td>
					<td><input type="text" class="text" name="rdesc" value="{ADMIN_PAGE_STRUCTURE_DESC}" size="64" maxlength="255" /></td>
				</tr>
				<tr>
					<td>{PHP.L.Icon} :</td>
					<td><input type="text" class="text" name="ricon" value="{ADMIN_PAGE_STRUCTURE_ICON}" size="64" maxlength="128" /></td>
				</tr>
				<tr>
					<td>{PHP.L.Group} :</td>
					<td><input type="checkbox" class="checkbox" name="rgroup"{ADMIN_PAGE_STRUCTURE_CHECK} /></td>
				</tr>
				<tr>
					<td>{PHP.L.adm_tpl_mode} :</td>
					<td>
						<input type="radio" class="radio" name="rtplmode" value="1"{ADMIN_PAGE_STRUCTURE_CHECK1} /> {PHP.L.adm_tpl_empty}<br/>
						<input type="radio" class="radio" name="rtplmode" value="2"{ADMIN_PAGE_STRUCTURE_CHECK2} /> {PHP.L.adm_tpl_forced}
						<select name="rtplforced" size="1">
<!-- BEGIN: SELECT -->
							<option value="{ADMIN_PAGE_STRUCTURE_OPTION_I}"{ADMIN_PAGE_STRUCTURE_OPTION_SELECTED}> {ADMIN_PAGE_STRUCTURE_OPTION_TPATH}</option>
<!-- END: SELECT -->
						</select>
						<br/>
						<input type="radio" class="radio" name="rtplmode" value="3"{ADMIN_PAGE_STRUCTURE_CHECK3} /> {PHP.L.adm_tpl_parent}
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
					<td><a href="{ADMIN_PAGE_STRUCTURE_RESYNC}"{ADMIN_PAGE_STRUCTURE_RESYNC_AJAX}>{PHP.L.Resync}</a></td>
				</tr>
				<tr>
					<td colspan="2"><input type="submit" class="submit" value="{PHP.L.Update}" /></td>
				</tr>
				</table>
			</form>
<!-- END: OPTIONS -->
<!-- BEGIN: DEFULT -->
			<h4>{PHP.L.editdeleteentries} :</h4>
			<form name="savestructure" id="savestructure" action="{ADMIN_PAGE_STRUCTURE_UPDATE_FORM_URL}" method="post"{ADMIN_PAGE_STRUCTURE_UPDATE_FORM_URL_AJAX}>
				<table class="cells">
				<tr>
					<td class="coltop">{PHP.L.Delete}</td>
					<td class="coltop">{PHP.L.Code}</td>
					<td class="coltop">{PHP.L.Path}</td>
					<td class="coltop">{PHP.L.TPL}</td>
					<td class="coltop">{PHP.L.Title}</td>
					<td class="coltop">{PHP.L.Group}</td>
					<td class="coltop">{PHP.L.Pages}</td>
					<td class="coltop">{PHP.L.Rights}</td>
					<td class="coltop">{PHP.L.Options} {PHP.L.adm_clicktoedit}</td>
				</tr>
<!-- BEGIN: ROW -->
				<tr>
					<td style="text-align:center;">
<!-- IF {PHP.dozvil} -->
						[<a href="{ADMIN_PAGE_STRUCTURE_UPDATE_DEL_URL}"{ADMIN_PAGE_STRUCTURE_UPDATE_DEL_URL_AJAX}>x</a>]
<!-- ENDIF -->
					</td>
					<td><input type="text" class="text" name="s[{ADMIN_PAGE_STRUCTURE_ID}][rcode]" value="{ADMIN_PAGE_STRUCTURE_CODE}" size="8" maxlength="255" /></td>
					<td>{ADMIN_PAGE_STRUCTURE_PATHFIELDIMG}<input type="text" class="text" name="s[{ADMIN_PAGE_STRUCTURE_ID}][rpath]" value="{ADMIN_PAGE_STRUCTURE_PATH}" size="{ADMIN_PAGE_STRUCTURE_PATHFIELDLEN}" maxlength="24" /></td>
					<td style="text-align:center;">{ADMIN_PAGE_STRUCTURE_TPL_SYM}</td>
					<td><input type="text" class="text" name="s[{ADMIN_PAGE_STRUCTURE_ID}][rtitle]" value="{ADMIN_PAGE_STRUCTURE_TITLE}" size="24" maxlength="100" /></td>
					<td style="text-align:center;"><input type="checkbox" class="checkbox" name="s[{ADMIN_PAGE_STRUCTURE_ID}][rgroup]"{ADMIN_PAGE_STRUCTURE_CHECKED} /></td>
					<td style="text-align:right;">{ADMIN_PAGE_STRUCTURE_PAGECOUNT} <a href="{ADMIN_PAGE_STRUCTURE_JUMPTO_URL}"><img src="images/admin/jumpto.gif" alt="" /></a></td>
					<td style="text-align:center;"><a href="{ADMIN_PAGE_STRUCTURE_RIGHTS_URL}"><img src="images/admin/rights2.gif" alt="" /></a></td>
					<td style="text-align:center;"><a href="{ADMIN_PAGE_STRUCTURE_OPTIONS_URL}"{ADMIN_PAGE_STRUCTURE_OPTIONS_URL_AJAX}>{PHP.L.Options}</a></td>
				</tr>
<!-- END: ROW -->
				<tr>
					<td colspan="9"><div class="pagnav">{ADMIN_PAGE_STRUCTURE_PAGINATION_PREV} {ADMIN_PAGE_STRUCTURE_PAGNAV} {ADMIN_PAGE_STRUCTURE_PAGINATION_NEXT}</div></td>
				</tr>
				<tr>
					<td colspan="9">{PHP.L.Total} : {ADMIN_PAGE_STRUCTURE_TOTALITEMS}, {PHP.L.adm_polls_on_page}: {ADMIN_PAGE_STRUCTURE_COUNTER_ROW}</td>
				</tr>
				<tr>
					<td colspan="9"><input type="submit" class="submit" value="{PHP.L.Update}" /></td>
				</tr>
				</table>
			</form>
			<h4>{PHP.L.addnewentry} :</h4>
			<form name="addstructure" id="addstructure" action="{ADMIN_PAGE_STRUCTURE_URL_FORM_ADD}" method="post"{ADMIN_PAGE_STRUCTURE_URL_FORM_ADD_AJAX}>
				<table class="cells">
				<tr>
					<td style="width:160px;">{PHP.L.Code} :</td>
					<td><input type="text" class="text" name="ncode" value="" size="16" maxlength="255" /> {PHP.L.adm_required}</td>
				</tr>
				<tr>
					<td>{PHP.L.Path} :</td>
					<td><input type="text" class="text" name="npath" value="" size="16" maxlength="16" /> {PHP.L.adm_required}</td>
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
					<td colspan="2"><input type="submit" class="submit" value="{PHP.L.Add}" /></td>
				</tr>
				</table>
			</form>
<!-- END: DEFULT -->
		</div>
<!-- END: PAGE_STRUCTURE -->