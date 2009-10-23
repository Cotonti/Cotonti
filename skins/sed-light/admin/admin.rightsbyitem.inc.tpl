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
					<td style="width:24px;" class="coltop">{PHP.R.admin_icon_auth_r}</td>
					<td style="width:24px;" class="coltop">{PHP.R.admin_icon_auth_w}</td>
<!-- IF {PHP.advanced} OR {PHP.ic} == 'page' -->
					<td style="width:24px;" class="coltop">{PHP.R.admin_icon_auth_1}</td>
<!-- ENDIF -->
<!-- IF {PHP.advanced} -->
					<td style="width:24px;" class="coltop">{PHP.R.admin_icon_auth_2}</td>
					<td style="width:24px;" class="coltop">{PHP.R.admin_icon_auth_3}</td>
					<td style="width:24px;" class="coltop">{PHP.R.admin_icon_auth_4}</td>
					<td style="width:24px;" class="coltop">{PHP.R.admin_icon_auth_5}</td>
<!-- ENDIF -->
					<td style="width:24px;" class="coltop">{PHP.R.admin_icon_auth_a}</td>
				</tr>
<!-- BEGIN: RIGHTSBYITEM_ROW -->
				<tr>
					<td style="padding:1px;">{PHP.R.admin_icon_groups} <a href="{ADMIN_RIGHTSBYITEM_ROW_LINK}">{ADMIN_RIGHTSBYITEM_ROW_TITLE}</a></td>
<!-- BEGIN: ROW_ITEMS -->
					<td style="text-align:center; padding:2px;">
<!-- IF {PHP.out.tpl_rights_parseline_locked} AND {PHP.out.tpl_rights_parseline_state} -->
						<input type="hidden" name="{ADMIN_RIGHTSBYITEM_ROW_ITEMS_NAME}" value="1" />
						{PHP.R.admin_icon_discheck1}
<!-- ENDIF -->
<!-- IF {PHP.out.tpl_rights_parseline_locked} AND !{PHP.out.tpl_rights_parseline_state} -->
						{PHP.R.admin_icon_discheck0}
<!-- ENDIF -->
<!-- IF !{PHP.out.tpl_rights_parseline_locked} -->
						<input type="checkbox" class="checkbox" name="{ADMIN_RIGHTSBYITEM_ROW_ITEMS_NAME}"{ADMIN_RIGHTSBYITEM_ROW_ITEMS_CHECKED}{ADMIN_RIGHTSBYITEM_ROW_ITEMS_DISABLED} />
<!-- ENDIF -->
					</td>
<!-- END: ROW_ITEMS -->
					<td style="text-align:center;padding:2px;">{ADMIN_RIGHTSBYITEM_ROW_USER}{ADMIN_RIGHTSBYITEM_ROW_PRESERVE}</td>
					<td style="text-align:center;"><a title="{PHP.L.Open}" href="{ADMIN_RIGHTSBYITEM_ROW_JUMPTO}">{PHP.R.admin_icon_jumpto}</a></td>
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
{PHP.R.admin_icon_auth_r} : {PHP.L.Read}<br />
{PHP.R.admin_icon_auth_w} : {PHP.L.Write}<br />
<!-- IF {PHP.advanced} OR {PHP.ic} == 'page' -->
{PHP.R.admin_icon_auth_1} : {PHP.l_custom1}<br />
<!-- ENDIF -->
<!-- IF {PHP.advanced} -->
{PHP.R.admin_icon_auth_2} : {PHP.L.Custom} #2<br />
{PHP.R.admin_icon_auth_3} : {PHP.L.Custom} #3<br />
{PHP.R.admin_icon_auth_4} : {PHP.L.Custom} #4<br />
{PHP.R.admin_icon_auth_5} : {PHP.L.Custom} #5<br />
<!-- ENDIF -->
{PHP.R.admin_icon_auth_a} : {PHP.L.Administration}
<!-- END: RIGHTSBYITEM_HELP -->