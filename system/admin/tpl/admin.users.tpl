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
				<td class="width60">{ADMIN_USERS_NGRP_TITLE}{PHP.L.adm_required}</td>
			</tr>
			<tr>
					<td>{PHP.L.Description}:</td>
					<td>{ADMIN_USERS_NGRP_DESC}</td>
				</tr>
				<tr>
					<td>{PHP.L.Icon}:</td>
					<td>{ADMIN_USERS_NGRP_ICON}</td>
				</tr>
				<tr>
					<td>{PHP.L.Alias}:</td>
					<td>{ADMIN_USERS_NGRP_ALIAS}</td>
				</tr>
				<tr>
					<td>{PHP.L.adm_maxsizesingle}:</td>
					<td>{ADMIN_USERS_NGRP_PFS_MAXFILE}</td>
				</tr>
				<tr>
					<td>{PHP.L.adm_maxsizeallpfs}:</td>
					<td>{ADMIN_USERS_NGRP_PFS_MAXTOTAL}</td>
				</tr>
				<tr>
					<td>{PHP.L.adm_copyrightsfrom}:</td>
					<td>{ADMIN_USERS_FORM_SELECTBOX_GROUPS} {PHP.L.adm_required}</td>
				</tr>
				<tr>
					<td>{PHP.L.Level}:</td>
					<td>{ADMIN_USERS_NGRP_RLEVEL}</td>
				</tr>
				<tr>
					<td>{PHP.L.Disabled}:</td>
					<td>{ADMIN_USERS_NGRP_DISABLED}</td>
				</tr>
				<tr>
					<td>{PHP.L.Hidden}:</td>
					<td>{ADMIN_USERS_NGRP_HIDDEN}</td>
				</tr>
				<tr>
					<td>{PHP.L.adm_rights_maintenance}:</td>
					<td>{ADMIN_USERS_NGRP_MAINTENANCE}</td>
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
						<td class="width60">{ADMIN_USERS_EDITFORM_GRP_TITLE} {PHP.L.adm_required}</td>
					</tr>
					<tr>
						<td>{PHP.L.Description}:</td>
						<td>{ADMIN_USERS_EDITFORM_GRP_DESC}</td>
					</tr>
					<tr>
						<td>{PHP.L.Icon}:</td>
						<td>{ADMIN_USERS_EDITFORM_GRP_ICON}</td>
					</tr>
					<tr>
						<td>{PHP.L.Alias}:</td>
						<td>{ADMIN_USERS_EDITFORM_GRP_ALIAS}</td>
					</tr>
					<tr>
						<td>{PHP.L.adm_maxsizesingle}:</td>
						<td>{ADMIN_USERS_EDITFORM_GRP_PFS_MAXFILE}</td>
					</tr>
					<tr>
						<td>{PHP.L.adm_maxsizeallpfs}:</td>
						<td>{ADMIN_USERS_EDITFORM_GRP_PFS_MAXTOTAL}</td>
					</tr>
					<tr>
						<td>{PHP.L.Disabled}:</td>
						<td>{ADMIN_USERS_EDITFORM_GRP_DISABLED}</td>
					</tr>
					<tr>
						<td>{PHP.L.Hidden}:</td>
						<td>{ADMIN_USERS_EDITFORM_GRP_HIDDEN}</td>
					</tr>
					<tr>
						<td>{PHP.L.Level}:</td>
						<td>{ADMIN_USERS_EDITFORM_GRP_RLEVEL}</td>
					</tr>
					<tr>
						<td>{PHP.L.Members}:</td>
						<td><a href="{ADMIN_USERS_EDITFORM_GRP_MEMBERSCOUNT_URL}">{ADMIN_USERS_EDITFORM_GRP_MEMBERSCOUNT}</a></td>
					</tr>
					<tr>
						<td>{PHP.L.adm_rights_maintenance}:</td>
						<td>{ADMIN_USERS_EDITFORM_GRP_MAINTENANCE}</td>
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