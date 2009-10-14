<!-- BEGIN: RIGHTS -->
		<div id="{ADMIN_RIGHTS_AJAX_OPENDIVID}">
<!-- IF {PHP.is_adminwarnings} -->
			<div class="error">{ADMIN_RIGHTS_ADMINWARNINGS}</div>
<!-- ENDIF -->
			<form name="saverights" id="saverights" action="{ADMIN_RIGHTS_FORM_URL}" method="post"{ADMIN_RIGHTS_FORM_URL_AJAX}>
<!-- IF {PHP.g} > 5 -->
				<table class="cells">
					<tr>
						<td style="text-align:right;"><input type="checkbox" class="checkbox" name="ncopyrightsconf" />{PHP.L.adm_copyrightsfrom} : {ADMIN_RIGHTS_SELECTBOX_GROUPS}&nbsp; <input type="submit" class="submit" value="{PHP.L.Update}" /></td>
					</tr>
				</table>
<!-- ENDIF -->
				<h4><img src="images/admin/admin.gif" alt="" /> {PHP.L.Core} :</h4>
				<table class="cells">
				<tr>
					<td class="coltop" rowspan="2">{PHP.L.Section}</td>
					<td class="coltop" style="width:128px;" rowspan="2">{PHP.L.adm_rightspergroup}</td>
					<td class="coltop" style="width:80px;" colspan="{ADMIN_RIGHTS_ADV_COLUMNS}">{PHP.L.Rights}</td>
					<td class="coltop" style="width:80px;" rowspan="2">{PHP.L.adm_setby}</td>
				</tr>
				<tr>
					<td style="width:24px;" class="coltop"><img src="images/admin/auth_r.gif" alt="" /></td>
					<td style="width:24px;" class="coltop"><img src="images/admin/auth_w.gif" alt="" /></td>
					<td style="width:24px;" class="coltop"><img src="images/admin/auth_1.gif" alt="" /></td>
<!-- IF {PHP.advanced} -->
					<td style="width:24px;" class="coltop"><img src="images/admin/auth_2.gif" alt="" /></td>
					<td style="width:24px;" class="coltop"><img src="images/admin/auth_3.gif" alt="" /></td>
					<td style="width:24px;" class="coltop"><img src="images/admin/auth_4.gif" alt="" /></td>
					<td style="width:24px;" class="coltop"><img src="images/admin/auth_5.gif" alt="" /></td>
<!-- ENDIF -->
					<td style="width:24px;" class="coltop"><img src="images/admin/auth_a.gif" alt="" /></td>
				</tr>
<!-- BEGIN: RIGHTS_ROW_CORE -->
				<tr>
					<td style="padding:1px;"><img src="images/admin/{ADMIN_RIGHTS_ROW_AUTH_CODE}.gif" alt="" /> <a href="{ADMIN_RIGHTS_ROW_LINK}">{ADMIN_RIGHTS_ROW_TITLE}</a></td>
					<td style="text-align:center; padding:2px;"><a href="{ADMIN_RIGHTS_ROW_RIGHTSBYITEM}"><img src="images/admin/rights2.gif" alt="" /></a></td>
<!-- BEGIN: ROW_CORE_ITEMS -->
					<td style="text-align:center; padding:2px;">
<!-- IF {PHP.out.tpl_rights_parseline_locked} AND {PHP.out.tpl_rights_parseline_state} -->
						<input type="hidden" name="{ADMIN_RIGHTS_ROW_ITEMS_NAME}" value="1" />
						<img src="images/admin/discheck1.gif" alt="" />
<!-- ENDIF -->
<!-- IF {PHP.out.tpl_rights_parseline_locked} AND !{PHP.out.tpl_rights_parseline_state} -->
						<img src="images/admin/discheck0.gif" alt="" />
<!-- ENDIF -->
<!-- IF !{PHP.out.tpl_rights_parseline_locked} -->
						<input type="checkbox" class="checkbox" name="{ADMIN_RIGHTS_ROW_ITEMS_NAME}"{ADMIN_RIGHTS_ROW_ITEMS_CHECKED}{ADMIN_RIGHTS_ROW_ITEMS_DISABLED} />
<!-- ENDIF -->
					</td>
<!-- END: ROW_CORE_ITEMS -->
					<td style="text-align:center; padding:2px;">{ADMIN_RIGHTS_ROW_USER}{ADMIN_RIGHTS_ROW_PRESERVE}</td>
				</tr>
<!-- END: RIGHTS_ROW_CORE -->
				</table>
				<h4><img src="images/admin/forums.gif" alt="" /> {PHP.L.Forums} :</h4>
				<table class="cells">
				<tr>
					<td class="coltop" rowspan="2">{PHP.L.Section}</td>
					<td class="coltop" style="width:128px;" rowspan="2">{PHP.L.adm_rightspergroup}</td>
					<td class="coltop" style="width:80px;" colspan="{ADMIN_RIGHTS_ADV_COLUMNS}">{PHP.L.Rights}</td>
					<td class="coltop" style="width:80px;" rowspan="2">{PHP.L.adm_setby}</td>
				</tr>
				<tr>
					<td style="width:24px;" class="coltop"><img src="images/admin/auth_r.gif" alt="" /></td>
					<td style="width:24px;" class="coltop"><img src="images/admin/auth_w.gif" alt="" /></td>
					<td style="width:24px;" class="coltop"><img src="images/admin/auth_1.gif" alt="" /></td>
<!-- IF {PHP.advanced} -->
					<td style="width:24px;" class="coltop"><img src="images/admin/auth_2.gif" alt="" /></td>
					<td style="width:24px;" class="coltop"><img src="images/admin/auth_3.gif" alt="" /></td>
					<td style="width:24px;" class="coltop"><img src="images/admin/auth_4.gif" alt="" /></td>
					<td style="width:24px;" class="coltop"><img src="images/admin/auth_5.gif" alt="" /></td>
<!-- ENDIF -->
					<td style="width:24px;" class="coltop"><img src="images/admin/auth_a.gif" alt="" /></td>
				</tr>
<!-- BEGIN: RIGHTS_ROW_FORUMS -->
				<tr>
					<td style="padding:1px;"><img src="images/admin/{ADMIN_RIGHTS_ROW_AUTH_CODE}.gif" alt="" /> <a href="{ADMIN_RIGHTS_ROW_LINK}">{ADMIN_RIGHTS_ROW_TITLE}</a></td>
					<td style="text-align:center; padding:2px;"><a href="{ADMIN_RIGHTS_ROW_RIGHTSBYITEM}"><img src="images/admin/rights2.gif" alt="" /></a></td>
<!-- BEGIN: ROW_FORUMS_ITEMS -->
					<td style="text-align:center; padding:2px;">
<!-- IF {PHP.out.tpl_rights_parseline_locked} AND {PHP.out.tpl_rights_parseline_state} -->
						<input type="hidden" name="{ADMIN_RIGHTS_ROW_ITEMS_NAME}" value="1" />
						<img src="images/admin/discheck1.gif" alt="" />
<!-- ENDIF -->
<!-- IF {PHP.out.tpl_rights_parseline_locked} AND !{PHP.out.tpl_rights_parseline_state} -->
						<img src="images/admin/discheck0.gif" alt="" />
<!-- ENDIF -->
<!-- IF !{PHP.out.tpl_rights_parseline_locked} -->
						<input type="checkbox" class="checkbox" name="{ADMIN_RIGHTS_ROW_ITEMS_NAME}"{ADMIN_RIGHTS_ROW_ITEMS_CHECKED}{ADMIN_RIGHTS_ROW_ITEMS_DISABLED} />
<!-- ENDIF -->
					</td>
<!-- END: ROW_FORUMS_ITEMS -->
					<td style="text-align:center; padding:2px;">{ADMIN_RIGHTS_ROW_USER}{ADMIN_RIGHTS_ROW_PRESERVE}</td>
				</tr>
<!-- END: RIGHTS_ROW_FORUMS -->
				</table>
				<h4><img src="images/admin/page.gif" alt="" /> {PHP.L.Pages} :</h4>
				<table class="cells">
				<tr>
					<td class="coltop" rowspan="2">{PHP.L.Section}</td>
					<td class="coltop" style="width:128px;" rowspan="2">{PHP.L.adm_rightspergroup}</td>
					<td class="coltop" style="width:80px;" colspan="{ADMIN_RIGHTS_ADV_COLUMNS}">{PHP.L.Rights}</td>
					<td class="coltop" style="width:80px;" rowspan="2">{PHP.L.adm_setby}</td>
				</tr>
				<tr>
					<td style="width:24px;" class="coltop"><img src="images/admin/auth_r.gif" alt="" /></td>
					<td style="width:24px;" class="coltop"><img src="images/admin/auth_w.gif" alt="" /></td>
					<td style="width:24px;" class="coltop"><img src="images/admin/auth_1.gif" alt="" /></td>
<!-- IF {PHP.advanced} -->
					<td style="width:24px;" class="coltop"><img src="images/admin/auth_2.gif" alt="" /></td>
					<td style="width:24px;" class="coltop"><img src="images/admin/auth_3.gif" alt="" /></td>
					<td style="width:24px;" class="coltop"><img src="images/admin/auth_4.gif" alt="" /></td>
					<td style="width:24px;" class="coltop"><img src="images/admin/auth_5.gif" alt="" /></td>
<!-- ENDIF -->
					<td style="width:24px;" class="coltop"><img src="images/admin/auth_a.gif" alt="" /></td>
				</tr>
<!-- BEGIN: RIGHTS_ROW_PAGES -->
				<tr>
					<td style="padding:1px;"><img src="images/admin/{ADMIN_RIGHTS_ROW_AUTH_CODE}.gif" alt="" /> <a href="{ADMIN_RIGHTS_ROW_LINK}">{ADMIN_RIGHTS_ROW_TITLE}</a></td>
					<td style="text-align:center; padding:2px;"><a href="{ADMIN_RIGHTS_ROW_RIGHTSBYITEM}"><img src="images/admin/rights2.gif" alt="" /></a></td>
<!-- BEGIN: ROW_PAGES_ITEMS -->
					<td style="text-align:center; padding:2px;">
<!-- IF {PHP.out.tpl_rights_parseline_locked} AND {PHP.out.tpl_rights_parseline_state} -->
						<input type="hidden" name="{ADMIN_RIGHTS_ROW_ITEMS_NAME}" value="1" />
						<img src="images/admin/discheck1.gif" alt="" />
<!-- ENDIF -->
<!-- IF {PHP.out.tpl_rights_parseline_locked} AND !{PHP.out.tpl_rights_parseline_state} -->
						<img src="images/admin/discheck0.gif" alt="" />
<!-- ENDIF -->
<!-- IF !{PHP.out.tpl_rights_parseline_locked} -->
						<input type="checkbox" class="checkbox" name="{ADMIN_RIGHTS_ROW_ITEMS_NAME}"{ADMIN_RIGHTS_ROW_ITEMS_CHECKED}{ADMIN_RIGHTS_ROW_ITEMS_DISABLED} />
<!-- ENDIF -->
					</td>
<!-- END: ROW_PAGES_ITEMS -->
					<td style="text-align:center; padding:2px;">{ADMIN_RIGHTS_ROW_USER}{ADMIN_RIGHTS_ROW_PRESERVE}</td>
				</tr>
<!-- END: RIGHTS_ROW_PAGES -->
				</table>
				<h4><img src="images/admin/plug.gif" alt="" /> {PHP.L.Plugins} :</h4>
				<table class="cells">
				<tr>
					<td class="coltop" rowspan="2">{PHP.L.Section}</td>
					<td class="coltop" style="width:128px;" rowspan="2">{PHP.L.adm_rightspergroup}</td>
					<td class="coltop" style="width:80px;" colspan="{ADMIN_RIGHTS_ADV_COLUMNS}">{PHP.L.Rights}</td>
					<td class="coltop" style="width:80px;" rowspan="2">{PHP.L.adm_setby}</td>
				</tr>
				<tr>
					<td style="width:24px;" class="coltop"><img src="images/admin/auth_r.gif" alt="" /></td>
					<td style="width:24px;" class="coltop"><img src="images/admin/auth_w.gif" alt="" /></td>
					<td style="width:24px;" class="coltop"><img src="images/admin/auth_1.gif" alt="" /></td>
<!-- IF {PHP.advanced} -->
					<td style="width:24px;" class="coltop"><img src="images/admin/auth_2.gif" alt="" /></td>
					<td style="width:24px;" class="coltop"><img src="images/admin/auth_3.gif" alt="" /></td>
					<td style="width:24px;" class="coltop"><img src="images/admin/auth_4.gif" alt="" /></td>
					<td style="width:24px;" class="coltop"><img src="images/admin/auth_5.gif" alt="" /></td>
<!-- ENDIF -->
					<td style="width:24px;" class="coltop"><img src="images/admin/auth_a.gif" alt="" /></td>
				</tr>
<!-- BEGIN: RIGHTS_ROW_PLUGINS -->
				<tr>
					<td style="padding:1px;"><img src="images/admin/{ADMIN_RIGHTS_ROW_AUTH_CODE}.gif" alt="" /> <a href="{ADMIN_RIGHTS_ROW_LINK}">{ADMIN_RIGHTS_ROW_TITLE}</a></td>
					<td style="text-align:center; padding:2px;"><a href="{ADMIN_RIGHTS_ROW_RIGHTSBYITEM}"><img src="images/admin/rights2.gif" alt="" /></a></td>
<!-- BEGIN: ROW_PLUGINS_ITEMS -->
					<td style="text-align:center; padding:2px;">
<!-- IF {PHP.out.tpl_rights_parseline_locked} AND {PHP.out.tpl_rights_parseline_state} -->
						<input type="hidden" name="{ADMIN_RIGHTS_ROW_ITEMS_NAME}" value="1" />
						<img src="images/admin/discheck1.gif" alt="" />
<!-- ENDIF -->
<!-- IF {PHP.out.tpl_rights_parseline_locked} AND !{PHP.out.tpl_rights_parseline_state} -->
						<img src="images/admin/discheck0.gif" alt="" />
<!-- ENDIF -->
<!-- IF !{PHP.out.tpl_rights_parseline_locked} -->
						<input type="checkbox" class="checkbox" name="{ADMIN_RIGHTS_ROW_ITEMS_NAME}"{ADMIN_RIGHTS_ROW_ITEMS_CHECKED}{ADMIN_RIGHTS_ROW_ITEMS_DISABLED} />
<!-- ENDIF -->
					</td>
<!-- END: ROW_PLUGINS_ITEMS -->
					<td style="text-align:center; padding:2px;">{ADMIN_RIGHTS_ROW_USER}{ADMIN_RIGHTS_ROW_PRESERVE}</td>
				</tr>
<!-- END: RIGHTS_ROW_PLUGINS -->
				<tr>
					<td colspan="{ADMIN_RIGHTS_3ADV_COLUMNS}" style="text-align:center;"><input type="submit" class="submit" value="{PHP.L.Update}" /></td>
				</tr>
				</table>
			</form>
			<a href="{ADMIN_RIGHTS_ADVANCED_URL}">{PHP.L.More}</a>
		</div>
<!-- END: RIGHTS -->

<!-- BEGIN: RIGHTS_HELP -->
<img src="images/admin/auth_r.gif" alt="" /> : {PHP.L.Read}<br />
<img src="images/admin/auth_w.gif" alt="" /> : {PHP.L.Write}<br />
<img src="images/admin/auth_1.gif" alt="" /> : {PHP.L.Download}<br />
<!-- IF {PHP.advanced} -->
<img src="images/admin/auth_2.gif" alt="" /> : {PHP.L.Custom} #2<br />
<img src="images/admin/auth_3.gif" alt="" /> : {PHP.L.Custom} #3<br />
<img src="images/admin/auth_4.gif" alt="" /> : {PHP.L.Custom} #4<br />
<img src="images/admin/auth_5.gif" alt="" /> : {PHP.L.Custom} #5<br />
<!-- ENDIF -->
<img src="images/admin/auth_a.gif" alt="" /> : {PHP.L.Administration}
<!-- END: RIGHTS_HELP -->