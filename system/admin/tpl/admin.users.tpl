<!-- BEGIN: MAIN -->
{FILE "{PHP.cfg.system_dir}/admin/tpl/warnings.tpl"}
<div class="block button-toolbar">
	<a title="{PHP.L.Configuration}" href="{ADMIN_USERS_URL}" class="button">{PHP.L.Configuration}</a>
	<a href="{ADMIN_USERS_EXTRAFIELDS_URL}" class="button">{PHP.L.adm_extrafields}</a>
</div>

<!-- BEGIN: ADMIN_USERS_DEFAULT -->
<div class="block">
	<h2>{PHP.L.Users}</h2>
	<div class="wrapper">
		<table class="cells">
			<thead>
				<tr>
					<th class="w-40">{PHP.L.Groups}</th>
					<th class="w-20">{PHP.L.Members}</th>
					<th class="w-20">{PHP.L.Enabled}</th>
					<th class="w-20">{PHP.L.Action}</th>
				</tr>
			</thead>
			<tbody>
				<!-- BEGIN: USERS_ROW -->
				<tr>
					<td class="start">
						<figure>
							<!-- IF {PHP.row.grp_disabled} -->
							{PHP.R.admin_icon_usergroup0}
							<!-- ELSE -->
							{PHP.R.admin_icon_usergroup1}
							<!-- ENDIF -->
						</figure>
						<div>
							<a href="{ADMIN_USERS_ROW_GRP_TITLE_URL}" class="ajax" title="{PHP.L.Edit}">{ADMIN_USERS_ROW_GRP_NAME} (#{ADMIN_USERS_ROW_GRP_ID})</a>
							<p>
								{ADMIN_USERS_ROW_GRP_DESC}
							</p>
						</div>
					</td>
					<td class="centerall">{ADMIN_USERS_ROW_GRP_COUNT_MEMBERS}</td>
					<td class="centerall">{ADMIN_USERS_ROW_GRP_DISABLED}</td>
					<td class="action">
						<!-- IF !{ADMIN_USERS_ROW_GRP_SKIPRIGHTS} -->
						<a title="{PHP.L.Rights}" href="{ADMIN_USERS_ROW_GRP_RIGHTS_URL}" class="button">{PHP.L.Rights}</a>
						<!-- ENDIF -->
						<a title="{PHP.L.Open}" href="{ADMIN_USERS_ROW_GRP_JUMPTO_URL}" class="button special">{PHP.L.Open}</a>
					</td>
				</tr>
				<!-- END: USERS_ROW -->
			</tbody>
		</table>
	</div>
</div>

<div class="block">
	<h2>{PHP.L.Add}:</h2>
	<div class="wrapper">
		<form name="addlevel" id="addlevel" action="{ADMIN_USERS_FORM_URL}" method="post" class="ajax">
			<table class="cells">
				<tr>
					<td class="w-40">{PHP.L.Name}:</td>
					<td class="w-60">{ADMIN_USERS_NGRP_NAME}{PHP.L.adm_required}</td>
				</tr>
				<tr>
					<td>{PHP.L.Title}:</td>
					<td>{ADMIN_USERS_NGRP_TITLE}{PHP.L.adm_required}</td>
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
				<!-- IF {PHP.pfs_is_active} -->
				<tr>
					<td>{PHP.L.adm_maxsizesingle}:</td>
					<td>{ADMIN_USERS_NGRP_PFS_MAXFILE}</td>
				</tr>
				<tr>
					<td>{PHP.L.adm_maxsizeallpfs}:</td>
					<td>{ADMIN_USERS_NGRP_PFS_MAXTOTAL}</td>
				</tr>
				<!-- ENDIF -->
				<tr>
					<td>{PHP.L.adm_copyrightsfrom}:</td>
					<td>{ADMIN_USERS_FORM_SELECTBOX_GROUPS} {PHP.L.adm_required}</td>
				</tr>
				<tr>
					<td>{PHP.L.adm_skiprights}:</td>
					<td>{ADMIN_USERS_NGRP_SKIPRIGHTS}</td>
				</tr>
				<tr>
					<td>{PHP.L.Level}:</td>
					<td>{ADMIN_USERS_NGRP_RLEVEL}</td>
				</tr>
				<tr>
					<td>{PHP.L.Disabled}:</td>
					<td>{ADMIN_USERS_NGRP_DISABLED}</td>
				</tr>
				<!-- IF {PHP.hidden_groups} -->
				<tr>
					<td>{PHP.L.Hidden}:</td>
					<td>{ADMIN_USERS_NGRP_HIDDEN}</td>
				</tr>
				<!-- ENDIF -->
				<tr>
					<td>{PHP.L.adm_rights_maintenance}:</td>
					<td>{ADMIN_USERS_NGRP_MAINTENANCE}</td>
				</tr>
				<tr>
					<td class="valid" colspan="2"><input type="submit" class="submit" value="{PHP.L.Add}" /></td>
				</tr>
			</table>
		</form>
	</div>
</div>
<!-- END: ADMIN_USERS_DEFAULT -->

<!-- BEGIN: ADMIN_USERS_EDIT -->
<div class="block">
	<h2>{PHP.L.Edit}</h2>
	<div class="wrapper">
		<form name="editlevel" id="editlevel" action="{ADMIN_USERS_EDITFORM_URL}" method="post" class="ajax">
			<table class="cells">
				<tr>
					<td class="w-40">{PHP.L.Name}:</td>
					<td class="w-60">{ADMIN_USERS_EDITFORM_GRP_NAME} {PHP.L.adm_required}</td>
				</tr>
				<tr>
					<td>{PHP.L.Title}:</td>
					<td>{ADMIN_USERS_EDITFORM_GRP_TITLE} {PHP.L.adm_required}</td>
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
				<!-- IF {PHP.pfs_is_active} -->
				<tr>
					<td>{PHP.L.adm_maxsizesingle}:</td>
					<td>{ADMIN_USERS_EDITFORM_GRP_PFS_MAXFILE}</td>
				</tr>
				<tr>
					<td>{PHP.L.adm_maxsizeallpfs}:</td>
					<td>{ADMIN_USERS_EDITFORM_GRP_PFS_MAXTOTAL}</td>
				</tr>
				<!-- ENDIF -->
				<tr>
					<td>{PHP.L.Disabled}:</td>
					<td>{ADMIN_USERS_EDITFORM_GRP_DISABLED}</td>
				</tr>
				<!-- IF {PHP.hidden_groups} -->
				<tr>
					<td>{PHP.L.Hidden}:</td>
					<td>{ADMIN_USERS_EDITFORM_GRP_HIDDEN}</td>
				</tr>
				<!-- ENDIF -->
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
					<td>{PHP.L.adm_skiprights}:</td>
					<td>{ADMIN_USERS_EDITFORM_GRP_SKIPRIGHTS}</td>
				</tr>
				<!-- IF !{ADMIN_USERS_EDITFORM_SKIPRIGHTS} -->
				<tr>
					<td>{PHP.L.Rights}:</td>
					<td><a href="{ADMIN_USERS_EDITFORM_RIGHT_URL}" class="button">{PHP.L.Rights}</a></td>
				</tr>
				<!-- ENDIF -->
				<!-- IF {PHP.g} > 5 -->
				<tr>
					<td>{PHP.L.Delete}:</td>
					<td><a href="{ADMIN_USERS_EDITFORM_DEL_CONFIRM_URL}" class="confirmLink">{PHP.R.admin_icon_delete}</a></td>
				</tr>
				<!-- ENDIF -->
				<tr>
					<td class="valid" colspan="2"><input type="submit" class="submit" value="{PHP.L.Update}" /></td>
				</tr>
			</table>
		</form>
	</div>
</div>
<!-- END: ADMIN_USERS_EDIT -->

<!-- END: MAIN -->
