<!-- BEGIN: RIGHTSBYITEM -->
		<div id="{ADMIN_RIGHTSBYITEM_AJAX_OPENDIVID}">
<!-- IF {PHP.is_adminwarnings} -->
			<div class="error">{ADMIN_RIGHTSBYITEM_ADMINWARNINGS}</div>
<!-- ENDIF -->
			<form name="saverightsbyitem" id="saverightsbyitem" action="{ADMIN_RIGHTSBYITEM_FORM_URL}" method="post"{ADMIN_RIGHTSBYITEM_FORM_URL_AJAX}>
				<table class="cells">
				<tr>
					<td class="coltop" rowspan="2">{PHP.L.Groups}</td>
					<td class="coltop" colspan="{ADMIN_RIGHTSBYITEM_ADV_COLUMNS}">{PHP.L.Rights}</td>
					<td class="coltop" rowspan="2" style="width:128px;">{PHP.L.adm_setby}</td>
					<td class="coltop" rowspan="2" style="width:64px;">{PHP.L.Open}</td>
				</tr>
				<tr>
					<td style="width:24px;" class="coltop"><img src="images/admin/auth_r.gif" alt="" /></td>
					<td style="width:24px;" class="coltop"><img src="images/admin/auth_w.gif" alt="" /></td>
<!-- IF {PHP.advanced} OR {PHP.ic} == 'page' -->
					<td style="width:24px;" class="coltop"><img src="images/admin/auth_1.gif" alt="" /></td>
<!-- ENDIF -->
<!-- IF {PHP.advanced} -->
					<td style="width:24px;" class="coltop"><img src="images/admin/auth_2.gif" alt="" /></td>
					<td style="width:24px;" class="coltop"><img src="images/admin/auth_3.gif" alt="" /></td>
					<td style="width:24px;" class="coltop"><img src="images/admin/auth_4.gif" alt="" /></td>
					<td style="width:24px;" class="coltop"><img src="images/admin/auth_5.gif" alt="" /></td>
<!-- ENDIF -->
					<td style="width:24px;" class="coltop"><img src="images/admin/auth_a.gif" alt="" /></td>
				</tr>
<!-- BEGIN: RIGHTSBYITEM_ROW -->
				<tr>
					<td style="padding:1px;"><img src="images/admin/groups.gif" alt="" /> <a href="{ADMIN_RIGHTSBYITEM_ROW_LINK}">{ADMIN_RIGHTSBYITEM_ROW_TITLE}</a></td>
<!-- BEGIN: ROW_ITEMS -->
					<td style="text-align:center; padding:2px;">
<!-- IF {PHP.out.tpl_rights_parseline_locked} AND {PHP.out.tpl_rights_parseline_state} -->
						<input type="hidden" name="{ADMIN_RIGHTSBYITEM_ROW_ITEMS_NAME}" value="1" />
						<img src="images/admin/discheck1.gif" alt="" />
<!-- ENDIF -->
<!-- IF {PHP.out.tpl_rights_parseline_locked} AND !{PHP.out.tpl_rights_parseline_state} -->
						<img src="images/admin/discheck0.gif" alt="" />
<!-- ENDIF -->
<!-- IF !{PHP.out.tpl_rights_parseline_locked} -->
						<input type="checkbox" class="checkbox" name="{ADMIN_RIGHTSBYITEM_ROW_ITEMS_NAME}"{ADMIN_RIGHTSBYITEM_ROW_ITEMS_CHECKED}{ADMIN_RIGHTSBYITEM_ROW_ITEMS_DISABLED} />
<!-- ENDIF -->
					</td>
<!-- END: ROW_ITEMS -->
					<td style="text-align:center;padding:2px;">{ADMIN_RIGHTSBYITEM_ROW_USER}</td>
					<td style="text-align:center;"><a href="{ADMIN_RIGHTSBYITEM_ROW_JUMPTO}"><img src="images/admin/jumpto.gif" alt="" /></a></td>
				</tr>
<!-- END: RIGHTSBYITEM_ROW -->
				<tr>
					<td colspan="{ADMIN_RIGHTSBYITEM_3ADV_COLUMNS}" style="text-align:center;"><input type="submit" class="submit" value="{PHP.L.Update}" /></td>
				</tr>
				</table>
			</form>
			<a href="{ADMIN_RIGHTSBYITEM_ADVANCED_URL}">{PHP.L.More}</a>
		</div>
<!-- END: RIGHTSBYITEM -->

<!-- BEGIN: RIGHTSBYITEM_HELP -->
<img src="images/admin/auth_r.gif" alt="" /> : {PHP.L.Read}<br />
<img src="images/admin/auth_w.gif" alt="" /> : {PHP.L.Write}<br />
<!-- IF {PHP.advanced} OR {PHP.ic} == 'page' -->
<img src="images/admin/auth_1.gif" alt="" /> : {PHP.l_custom1}<br />
<!-- ENDIF -->
<!-- IF {PHP.advanced} -->
<img src="images/admin/auth_2.gif" alt="" /> : {PHP.L.Custom} #2<br />
<img src="images/admin/auth_3.gif" alt="" /> : {PHP.L.Custom} #3<br />
<img src="images/admin/auth_4.gif" alt="" /> : {PHP.L.Custom} #4<br />
<img src="images/admin/auth_5.gif" alt="" /> : {PHP.L.Custom} #5<br />
<!-- ENDIF -->
<img src="images/admin/auth_a.gif" alt="" /> : {PHP.L.Administration}
<!-- END: RIGHTSBYITEM_HELP -->