<!-- BEGIN: MAIN -->
	<div id="ajaxBlock">
		<h2>{PHP.L.Users}</h2>
<!-- IF {PHP.is_adminwarnings} -->
			<div class="error">
				<h4>{PHP.L.Message}</h4>
				<p>{ADMIN_USERS_ADMINWARNINGS}</p>
			</div>
<!-- ENDIF -->
		<ul class="follow">
			<li><a title="{PHP.L.Configuration}" href="{ADMIN_USERS_URL}">{PHP.L.Configuration}</a></li>
<!-- IF {PHP.lincif_extfld} -->
			<li><a href="{ADMIN_USERS_EXTRAFIELDS_URL}">{PHP.L.adm_extrafields_desc}</a></li>
<!-- ELSE -->
			<li>{PHP.L.adm_extrafields_desc}</li>
<!-- ENDIF -->
		</ul>
<!-- BEGIN: ADMIN_USERS_DEFAULT -->
		<table class="cells">
			<tr>
				<td class="coltop width5">&nbsp;</td>
				<td class="coltop width35">{PHP.L.Groups} {PHP.L.adm_clicktoedit}</td>
				<td class="coltop width15">{PHP.L.Members}</td>
				<td class="coltop width15">{PHP.L.Enabled}</td>
				<td class="coltop width15">{PHP.L.Hidden}</td>
				<td class="coltop width15">{PHP.L.Action}</td>
			</tr>
<!-- BEGIN: USERS_ROW -->
			<tr>
				<td class="centerall">{ADMIN_USERS_ROW_GRP_ID}</td>
				<td><a href="{ADMIN_USERS_ROW_GRP_TITLE_URL}" class="ajax">{ADMIN_USERS_ROW_GRP_TITLE}</a></td>
				<td class="centerall">{ADMIN_USERS_ROW_GRP_COUNT_MEMBERS}</td>
				<td class="centerall">{ADMIN_USERS_ROW_GRP_DISABLED}</td>
				<td class="centerall">{ADMIN_USERS_ROW_GRP_HIDDEN}</td>
				<td class="centerall action">
					<a title="{PHP.L.Rights}" href="{ADMIN_USERS_ROW_GRP_RIGHTS_URL}">{PHP.R.admin_icon_rights}</a>
					<a title="{PHP.L.Open}" href="{ADMIN_USERS_ROW_GRP_JUMPTO_URL}">{PHP.R.admin_icon_jumpto}</a>
				</td>
			</tr>
<!-- END: USERS_ROW -->
		</table>
		<h3>{PHP.L.addnewentry}:</h3>
		<form name="addlevel" id="addlevel" action="{ADMIN_USERS_FORM_URL}" method="post" class="ajax">
		<table class="cells">
			<tr>
				<td class="width40">{PHP.L.Group}:</td>
				<td class="width60"><input type="text" class="text" name="ntitle" value="" size="40" maxlength="64" /> {PHP.L.adm_required}</td>
			</tr>
			<tr>
					<td>{PHP.L.Description}:</td>
					<td><input type="text" class="text" name="ndesc" value="" size="40" maxlength="64" /></td>
				</tr>
				<tr>
					<td>{PHP.L.Icon}:</td>
					<td><input type="text" class="text" name="nicon" value="" size="40" maxlength="128" /></td>
				</tr>
				<tr>
					<td>{PHP.L.Alias}:</td>
					<td><input type="text" class="text" name="nalias" value="" size="16" maxlength="24" /></td>
				</tr>
				<tr>
					<td>{PHP.L.adm_maxsizesingle}:</td>
					<td><input type="text" class="text" name="nmaxsingle" value="0" size="16" maxlength="16" /></td>
				</tr>
				<tr>
					<td>{PHP.L.adm_maxsizeallpfs}:</td>
					<td><input type="text" class="text" name="nmaxtotal" value="0" size="16" maxlength="16" /></td>
				</tr>
				<tr>
					<td>{PHP.L.adm_copyrightsfrom}:</td>
					<td>{ADMIN_USERS_FORM_SELECTBOX_GROUPS} {PHP.L.adm_required}</td>
				</tr>
				<tr>
					<td>{PHP.L.Level}:</td>
					<td>
						<select name="nlevel" size="1">
<!-- BEGIN: USERS_FORM_SELECT_NLEVEL -->
							<option value="{ADMIN_USERS_FORM_SELECT_VALUE}">{ADMIN_USERS_FORM_SELECT_VALUE}</option>
<!-- END: USERS_FORM_SELECT_NLEVEL -->
						</select>
					</td>
				</tr>
				<tr>
					<td>{PHP.L.Enabled}:</td>
					<td>
						<input type="radio" class="radio" name="ndisabled" value="0" checked="checked" /> {PHP.L.Yes}
						<input type="radio" class="radio" name="ndisabled" value="1" /> {PHP.L.No}
					</td>
				</tr>
				<tr>
					<td>{PHP.L.Hidden}:</td>
					<td>
						<input type="radio" class="radio" name="nhidden" value="1" /> {PHP.L.Yes}
						<input type="radio" class="radio" name="nhidden" value="0" checked="checked" /> {PHP.L.No}
					</td>
				</tr>
				<tr>
					<td>{PHP.L.adm_rights_maintenance}:</td>
					<td>
						<input type="radio" class="radio" name="nmtmode" value="1" /> {PHP.L.Yes}
						<input type="radio" class="radio" name="nmtmode" value="0" checked="checked" /> {PHP.L.No}
					</td>
				</tr>
				<tr>
					<td class="valid" colspan="2"><input type="submit" class="submit" value="{PHP.L.Add}" /></td>
				</tr>
				</table>
			</form>
<!-- END: ADMIN_USERS_DEFAULT -->
<!-- BEGIN: ADMIN_USERS_EDIT -->
			<form name="editlevel" id="editlevel" action="{ADMIN_USERS_EDITFORM_URL}" method="post" class="ajax">
				<table class="cells">
					<tr>
						<td class="width40">{PHP.L.Group}:</td>
						<td class="width60"><input type="text" class="text" name="rtitle" value="{ADMIN_USERS_EDITFORM_GRP_TITLE}" size="40" maxlength="64" /> {PHP.L.adm_required}</td>
					</tr>
					<tr>
						<td>{PHP.L.Description}:</td>
						<td><input type="text" class="text" name="rdesc" value="{ADMIN_USERS_EDITFORM_GRP_DESC}" size="40" maxlength="64" /></td>
					</tr>
					<tr>
						<td>{PHP.L.Icon}:</td>
						<td><input type="text" class="text" name="ricon" value="{ADMIN_USERS_EDITFORM_GRP_ICON}" size="40" maxlength="128" /></td>
					</tr>
					<tr>
						<td>{PHP.L.Alias}:</td>
						<td><input type="text" class="text" name="ralias" value="{ADMIN_USERS_EDITFORM_GRP_ALIAS}" size="16" maxlength="24" /></td>
					</tr>
					<tr>
						<td>{PHP.L.adm_maxsizesingle}:</td>
						<td><input type="text" class="text" name="rmaxfile" value="{ADMIN_USERS_EDITFORM_GRP_PFS_MAXFILE}" size="16" maxlength="16" /></td>
					</tr>
					<tr>
						<td>{PHP.L.adm_maxsizeallpfs}:</td>
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
						<td>{PHP.L.Level}:</td>
						<td>
							<select name="rlevel" size="1">
<!-- BEGIN: SELECT_RLEVEL -->
								<option value="{ADMIN_USERS_EDITFORM_RLEVEL_ITEM}"{ADMIN_USERS_EDITFORM_RLEVEL_ITEM_SELECTED}>{ADMIN_USERS_EDITFORM_RLEVEL_ITEM}</option>
<!-- END: SELECT_RLEVEL -->
							</select>
						</td>
					</tr>
					<tr>
						<td>{PHP.L.Members}:</td>
						<td><a href="{ADMIN_USERS_EDITFORM_GRP_PFS_MEMBERSCOUNT_URL}">{ADMIN_USERS_EDITFORM_GRP_PFS_MEMBERSCOUNT}</a></td>
					</tr>
					<tr>
						<td>{PHP.L.adm_rights_maintenance}:</td>
						<td>
<!-- IF {PHP.row.grp_maintenance} -->
							<input type="radio" class="radio" name="rmtmode" value="1" checked="checked" />{PHP.L.Yes} <input type="radio" class="radio" name="rmtmode" value="0" />{PHP.L.No}
<!-- ELSE -->
							<input type="radio" class="radio" name="rmtmode" value="1" />{PHP.L.Yes} <input type="radio" class="radio" name="rmtmode" value="0" checked="checked" />{PHP.L.No}
<!-- ENDIF -->
						</td>
					</tr>
					<tr>
						<td>{PHP.L.Rights}:</td>
						<td><a href="{ADMIN_USERS_EDITFORM_RIGHT_URL}">{PHP.R.admin_icon_rights}</a></td>
					</tr>
<!-- IF {PHP.g} > 5 -->
					<tr>
						<td>{PHP.L.Delete}:</td>
						<td><a href="{ADMIN_USERS_EDITFORM_DEL_URL}" class="ajax">{PHP.R.admin_icon_delete}</a></td>
					</tr>
<!-- ENDIF -->
					<tr>
						<td class="valid" colspan="2"><input type="submit" class="submit" value="{PHP.L.Update}" /></td>
					</tr>
				</table>
			</form>
<!-- END: ADMIN_USERS_EDIT -->
		</div>
<!-- END: MAIN -->