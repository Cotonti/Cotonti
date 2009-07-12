<!-- BEGIN: USERS -->
		<div id="{ADMIN_USERS_AJAX_OPENDIVID}">
			<ul>
				<li><a href="{ADMIN_USERS_URL}">{PHP.L.Configuration}: <img src="images/admin/config.gif" alt="" /></a></li>
<!-- IF {PHP.lincif_extfld} -->
				<li><a href="{ADMIN_USERS_EXTRAFIELDS_URL}">{PHP.L.adm_extrafields_desc}</a></li>
<!-- ELSE -->
				<li>{PHP.L.adm_extrafields_desc}</li>
<!-- ENDIF -->
			</ul>
<!-- IF {PHP.is_adminwarnings} -->
			<div class="error">{ADMIN_USERS_ADMINWARNINGS}</div>
<!-- ENDIF -->
<!-- BEGIN: ADMIN_USERS_DEFAULT -->
			<table class="cells">
			<tr>
				<td  class="coltop">{PHP.L.Groups} {PHP.L.adm_clicktoedit}</td>
				<td class="coltop">{PHP.L.Members}</td>
				<td class="coltop" style="width:96px;">{PHP.L.Enabled}</td>
				<td class="coltop" style="width:96px;">{PHP.L.Hidden}</td>
				<td class="coltop" style="width:80px;">{PHP.L.Rights}</td>
				<td class="coltop" style="width:64px;">{PHP.L.Open}</td>
			</tr>
<!-- BEGIN: USERS_ROW -->
			<tr>
				<td><img src="images/admin/groups.gif" alt="" /><a href="{ADMIN_USERS_ROW_GRP_TITLE_URL}"{ADMIN_USERS_ROW_GRP_TITLE_URL_AJAX}>{ADMIN_USERS_ROW_GRP_TITLE}</a></td>
				<td style="text-align:center;">{ADMIN_USERS_ROW_GRP_ID}</td>
				<td style="text-align:center;">{ADMIN_USERS_ROW_GRP_DISABLED}</td>
				<td style="text-align:center;">{ADMIN_USERS_ROW_GRP_HIDDEN}</td>
				<td style="text-align:center;"><a href="{ADMIN_USERS_ROW_GRP_RIGHTS_URL}"><img src="images/admin/rights.gif" alt="" /></a></td>
				<td style="text-align:center;"><a href="{ADMIN_USERS_ROW_GRP_JUMPTO_URL}"><img src="images/admin/jumpto.gif" alt="" /></a></td>
			</tr>
<!-- END: USERS_ROW -->
			</table>
			<h4>{PHP.L.addnewentry} :</h4>
			<form name="addlevel" id="addlevel" action="{ADMIN_USERS_FORM_URL}" method="post"{ADMIN_USERS_FORM_URL_AJAX}>
				<table class="cells">
				<tr>
					<td>{PHP.L.Group} :</td>
					<td><input type="text" class="text" name="ntitle" value="" size="40" maxlength="64" /> {PHP.L.adm_required}</td>
				</tr>
				<tr>
					<td>{PHP.L.Description} :</td>
					<td><input type="text" class="text" name="ndesc" value="" size="40" maxlength="64" /></td>
				</tr>
				<tr>
					<td>{PHP.L.Icon} :</td>
					<td><input type="text" class="text" name="nicon" value="" size="40" maxlength="128" /></td>
				</tr>
				<tr>
					<td>{PHP.L.Alias} :</td>
					<td><input type="text" class="text" name="nalias" value="" size="16" maxlength="24" /></td>
				</tr>
				<tr>
					<td>{PHP.L.adm_maxsizesingle} :</td>
					<td><input type="text" class="text" name="nmaxsingle" value="0" size="16" maxlength="16" /></td>
				</tr>
				<tr>
					<td>{PHP.L.adm_maxsizeallpfs} :</td>
					<td><input type="text" class="text" name="nmaxtotal" value="0" size="16" maxlength="16" /></td>
				</tr>
				<tr>
					<td>{PHP.L.adm_copyrightsfrom} :</td>
					<td>{ADMIN_USERS_FORM_SELECTBOX_GROUPS} {PHP.L.adm_required}</td>
				</tr>
				<tr>
					<td>{PHP.L.Level} :</td>
					<td>
						<select name="nlevel" size="1">
<!-- BEGIN: USERS_FORM_SELECT_NLEVEL -->
							<option value="{ADMIN_USERS_FORM_SELECT_VALUE}">{ADMIN_USERS_FORM_SELECT_VALUE}</option>
<!-- END: USERS_FORM_SELECT_NLEVEL -->
						</select>
					</td>
				</tr>
				<tr>
					<td>{PHP.L.Enabled} :</td>
					<td>
						<input type="radio" class="radio" name="ndisabled" value="0" checked="checked" /> {PHP.L.Yes}
						<input type="radio" class="radio" name="ndisabled" value="1" /> {PHP.L.No}
					</td>
				</tr>
				<tr>
					<td>{PHP.L.Hidden} :</td>
					<td>
						<input type="radio" class="radio" name="nhidden" value="1" /> {PHP.L.Yes}
						<input type="radio" class="radio" name="nhidden" value="0" checked="checked" /> {PHP.L.No}
					</td>
				</tr>
				<tr>
					<td>{PHP.L.adm_rights_maintenance} :</td>
					<td>
						<input type="radio" class="radio" name="nmtmode" value="1" /> {PHP.L.Yes}
						<input type="radio" class="radio" name="nmtmode" value="0" checked="checked" /> {PHP.L.No}
					</td>
				</tr>
				<tr>
					<td colspan="2"><input type="submit" class="submit" value="{PHP.L.Add}" /></td>
				</tr>
				</table>
			</form>
<!-- END: ADMIN_USERS_DEFAULT -->
<!-- BEGIN: ADMIN_USERS_EDIT -->
			<form name="editlevel" id="editlevel" action="{ADMIN_USERS_EDITFORM_URL}" method="post"{ADMIN_USERS_EDITFORM_URL_AJAX}>
				<table class="cells">
				<tr>
					<td>{PHP.L.Group} :</td>
					<td><input type="text" class="text" name="rtitle" value="{ADMIN_USERS_EDITFORM_GRP_TITLE}" size="40" maxlength="64" /> {PHP.L.adm_required}</td>
				</tr>
				<tr>
					<td>{PHP.L.Description} :</td>
					<td><input type="text" class="text" name="rdesc" value="{ADMIN_USERS_EDITFORM_GRP_DESC}" size="40" maxlength="64" /></td>
				</tr>
				<tr>
					<td>{PHP.L.Icon} :</td>
					<td><input type="text" class="text" name="ricon" value="{ADMIN_USERS_EDITFORM_GRP_ICON}" size="40" maxlength="128" /></td>
				</tr>
				<tr>
					<td>{PHP.L.Alias} :</td>
					<td><input type="text" class="text" name="ralias" value="{ADMIN_USERS_EDITFORM_GRP_ALIAS}" size="16" maxlength="24" /></td>
				</tr>
				<tr>
					<td>{PHP.L.adm_maxsizesingle} :</td>
					<td><input type="text" class="text" name="rmaxfile" value="{ADMIN_USERS_EDITFORM_GRP_PFS_MAXFILE}" size="16" maxlength="16" /></td>
				</tr>
				<tr>
					<td>{PHP.L.adm_maxsizeallpfs} :</td>
					<td><input type="text" class="text" name="rmaxtotal" value="{ADMIN_USERS_EDITFORM_GRP_PFS_MAXTOTAL}" size="16" maxlength="16" /></td>
				</tr>
				<tr>
					<td>{PHP.L.Enabled}:</td>
					<td>
<!-- IF {PHP.g} > 5 AND !{PHP.row.grp_disabled} -->
						<input type="radio" class="radio" name="rdisabled" value="0" checked="checked" />{PHP.L.Yes} <input type="radio" class="radio" name="rdisabled" value="1" />{PHP.L.No}
<!-- ENDIF -->
<!-- IF {PHP.g} > 5 AND {PHP.row.grp_disabled} -->
						<input type="radio" class="radio" name="rdisabled" value="0" />{PHP.L.Yes} <input type="radio" class="radio" name="rdisabled" value="1" checked="checked" />{PHP.L.No}
<!-- ENDIF -->
<!-- IF {PHP.g} <= 5 -->
					{PHP.L.Yes}
<!-- ENDIF -->
					</td>
				</tr>
				<tr>
					<td>{PHP.L.Hidden}:</td>
					<td>
<!-- IF {PHP.g} != 4 AND {PHP.row.grp_disabled} -->
						<input type="radio" class="radio" name="rhidden" value="1" checked="checked" />{PHP.L.Yes} <input type="radio" class="radio" name="rhidden" value="0" />{PHP.L.No}
<!-- ENDIF -->
<!-- IF {PHP.g} != 4 AND !{PHP.row.grp_disabled} -->
						<input type="radio" class="radio" name="rhidden" value="1" />{PHP.L.Yes} <input type="radio" class="radio" name="rhidden" value="0" checked="checked" />{PHP.L.No}
<!-- ENDIF -->
<!-- IF {PHP.g} == 4 -->
						{PHP.L.No}
<!-- ENDIF -->
					</td>
				</tr>
				<tr>
					<td>{PHP.L.Level} :</td>
					<td>
						<select name="rlevel" size="1">
<!-- BEGIN: SELECT_RLEVEL -->
							<option value="{ADMIN_USERS_EDITFORM_RLEVEL_ITEM}"{ADMIN_USERS_EDITFORM_RLEVEL_ITEM_SELECTED}>{ADMIN_USERS_EDITFORM_RLEVEL_ITEM}</option>
<!-- END: SELECT_RLEVEL -->
						</select>
					</td>
				</tr>
				<tr>
					<td>{PHP.L.Members} :</td>
					<td><a href="{ADMIN_USERS_EDITFORM_GRP_PFS_MEMBERSCOUNT_URL}">{ADMIN_USERS_EDITFORM_GRP_PFS_MEMBERSCOUNT}</a></td>
				</tr>
				<tr>
					<td>{PHP.L.adm_rights_maintenance} :</td>
					<td>
<!-- IF {PHP.row.grp_maintenance} -->
						<input type="radio" class="radio" name="rmtmode" value="1" checked="checked" />{PHP.L.Yes} <input type="radio" class="radio" name="rmtmode" value="0" />{PHP.L.No}
<!-- ELSE -->
						<input type="radio" class="radio" name="rmtmode" value="1" />{PHP.L.Yes} <input type="radio" class="radio" name="rmtmode" value="0" checked="checked" />{PHP.L.No}
<!-- ENDIF -->
					</td>
				</tr>
				<tr>
					<td>{PHP.L.Rights} :</td>
					<td><a href="{ADMIN_USERS_EDITFORM_RIGHT_URL}"><img src="images/admin/rights.gif" alt="" /></a></td>
				</tr>
<!-- IF {PHP.g} > 5 -->
				<tr>
					<td>{PHP.L.Delete} :</td>
					<td>[<a href="{ADMIN_USERS_EDITFORM_DEL_URL}"{ADMIN_USERS_EDITFORM_DEL_URL_AJAX}>x</a>]</td>
				</tr>
<!-- ENDIF -->
				<tr>
					<td colspan="2"><input type="submit" class="submit" value="{PHP.L.Update}" /></td>
				</tr>
				</table>
			</form>
<!-- END: ADMIN_USERS_EDIT -->
		</div>
<!-- END: USERS -->