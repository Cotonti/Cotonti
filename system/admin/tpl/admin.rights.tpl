<!-- BEGIN: MAIN -->
	<div id="ajaxBlock">
		<h2>{PHP.L.Rights}</h2>
		<!-- IF {PHP.is_adminwarnings} --><div class="error">
			<h4>{PHP.L.Message}</h4>
			<p>{ADMIN_RIGHTS_ADMINWARNINGS}</p>
		</div><!-- ENDIF -->
		<form name="saverights" id="saverights" action="{ADMIN_RIGHTS_FORM_URL}" method="post" class="ajax">
			<!-- IF {PHP.g} > 5 --><table class="cells">
				<tr>
					<td><input type="checkbox" class="checkbox" name="ncopyrightsconf" />{PHP.L.adm_copyrightsfrom}: {ADMIN_RIGHTS_SELECTBOX_GROUPS} &nbsp; <input type="submit" class="submit" value="{PHP.L.Update}" /></td>
				</tr>
			</table><!-- ENDIF -->
			<h3>{PHP.L.Core}:</h3>
			<table class="cells">
				<tr>
					<td class="coltop width5" rowspan="2"></td>
					<td class="coltop width25" rowspan="2">{PHP.L.Section}</td>
					<td class="coltop width40" colspan="{ADMIN_RIGHTS_ADV_COLUMNS}">{PHP.L.Rights}</td>
					<td class="coltop width15" rowspan="2">{PHP.L.adm_rightspergroup}</td>
					<td class="coltop width15" rowspan="2">{PHP.L.adm_setby}</td>
				</tr>
				<tr>
					<td class="coltop">{PHP.R.admin_icon_auth_r}</td>
					<td class="coltop">{PHP.R.admin_icon_auth_w}</td>
					<td class="coltop">{PHP.R.admin_icon_auth_1}</td>
					<!-- IF {PHP.advanced} --><td class="coltop">{PHP.R.admin_icon_auth_2}</td>
					<td class="coltop">{PHP.R.admin_icon_auth_3}</td>
					<td class="coltop">{PHP.R.admin_icon_auth_4}</td>
					<td class="coltop">{PHP.R.admin_icon_auth_5}</td>
<!-- ENDIF -->
					<td class="coltop">{PHP.R.admin_icon_auth_a}</td>
				</tr>
<!-- BEGIN: RIGHTS_ROW_CORE -->
				<tr>
					<td class="centerall"><img src="images/icons/default/{ADMIN_RIGHTS_ROW_AUTH_CODE}.png" alt="" /></td>
					<td> <a href="{ADMIN_RIGHTS_ROW_LINK}">{ADMIN_RIGHTS_ROW_TITLE}</a></td>
<!-- BEGIN: ROW_CORE_ITEMS -->
					<td class="centerall">
						<!-- IF {PHP.out.tpl_rights_parseline_locked} AND {PHP.out.tpl_rights_parseline_state} --><input type="hidden" name="{ADMIN_RIGHTS_ROW_ITEMS_NAME}" value="1" />
						{PHP.R.admin_icon_discheck1}<!-- ENDIF -->
						<!-- IF {PHP.out.tpl_rights_parseline_locked} AND !{PHP.out.tpl_rights_parseline_state} -->{PHP.R.admin_icon_discheck0}<!-- ENDIF -->
						<!-- IF !{PHP.out.tpl_rights_parseline_locked} --><input type="checkbox" class="checkbox" name="{ADMIN_RIGHTS_ROW_ITEMS_NAME}"{ADMIN_RIGHTS_ROW_ITEMS_CHECKED}{ADMIN_RIGHTS_ROW_ITEMS_DISABLED} /><!-- ENDIF -->
					</td>
<!-- END: ROW_CORE_ITEMS -->
					<td class="centerall"><a title="{PHP.L.Rights}" href="{ADMIN_RIGHTS_ROW_RIGHTSBYITEM}">{PHP.R.admin_icon_rights2}</a></td>
					<td class="textcenter">{ADMIN_RIGHTS_ROW_USER}{ADMIN_RIGHTS_ROW_PRESERVE}</td>
				</tr>
<!-- END: RIGHTS_ROW_CORE -->
			</table>
			<h3>{PHP.L.Forums}:</h3>
			<table class="cells">
				<tr>
					<td class="coltop width5" rowspan="2"></td>
					<td class="coltop width25" rowspan="2">{PHP.L.Section}</td>
					<td class="coltop width40" colspan="{ADMIN_RIGHTS_ADV_COLUMNS}">{PHP.L.Rights}</td>
					<td class="coltop width15" rowspan="2">{PHP.L.adm_rightspergroup}</td>
					<td class="coltop width15" rowspan="2">{PHP.L.adm_setby}</td>
				</tr>
				<tr>
					<td class="coltop">{PHP.R.admin_icon_auth_r}</td>
					<td class="coltop">{PHP.R.admin_icon_auth_w}</td>
					<td class="coltop">{PHP.R.admin_icon_auth_1}</td>
					<!-- IF {PHP.advanced} --><td class="coltop">{PHP.R.admin_icon_auth_2}</td>
					<td class="coltop">{PHP.R.admin_icon_auth_3}</td>
					<td class="coltop">{PHP.R.admin_icon_auth_4}</td>
					<td class="coltop">{PHP.R.admin_icon_auth_5}</td><!-- ENDIF -->
					<td class="coltop">{PHP.R.admin_icon_auth_a}</td>
				</tr>
<!-- BEGIN: RIGHTS_ROW_FORUMS -->
				<tr>
					<td class="centerall"><img src="images/icons/default/{ADMIN_RIGHTS_ROW_AUTH_CODE}.png" alt="" /></td>
					<td><a href="{ADMIN_RIGHTS_ROW_LINK}">{ADMIN_RIGHTS_ROW_TITLE}</a></td>
<!-- BEGIN: ROW_FORUMS_ITEMS -->
					<td class="centerall">
						<!-- IF {PHP.out.tpl_rights_parseline_locked} AND {PHP.out.tpl_rights_parseline_state} --><input type="hidden" name="{ADMIN_RIGHTS_ROW_ITEMS_NAME}" value="1" />
						{PHP.R.admin_icon_discheck1}<!-- ENDIF -->
						<!-- IF {PHP.out.tpl_rights_parseline_locked} AND !{PHP.out.tpl_rights_parseline_state} -->{PHP.R.admin_icon_discheck0}<!-- ENDIF -->
						<!-- IF !{PHP.out.tpl_rights_parseline_locked} --><input type="checkbox" class="checkbox" name="{ADMIN_RIGHTS_ROW_ITEMS_NAME}"{ADMIN_RIGHTS_ROW_ITEMS_CHECKED}{ADMIN_RIGHTS_ROW_ITEMS_DISABLED} /><!-- ENDIF -->
					</td>
<!-- END: ROW_FORUMS_ITEMS -->
					<td class="centerall"><a title="{PHP.L.Rights}" href="{ADMIN_RIGHTS_ROW_RIGHTSBYITEM}">{PHP.R.admin_icon_rights2}</a></td>
					<td class="centerall">{ADMIN_RIGHTS_ROW_USER}{ADMIN_RIGHTS_ROW_PRESERVE}</td>
				</tr>
<!-- END: RIGHTS_ROW_FORUMS -->
			</table>
			<h3>{PHP.L.Pages}:</h3>
			<table class="cells">
				<tr>
					<td class="coltop" style="width:5%;" rowspan="2"></td>
					<td class="coltop" style="width:25%;" rowspan="2">{PHP.L.Section}</td>
					<td class="coltop" style="width:40%;" colspan="{ADMIN_RIGHTS_ADV_COLUMNS}">{PHP.L.Rights}</td>
					<td class="coltop" style="width:15%;" rowspan="2">{PHP.L.adm_rightspergroup}</td>
					<td class="coltop" style="width:15%;" rowspan="2">{PHP.L.adm_setby}</td>
				</tr>
				<tr>
					<td class="coltop">{PHP.R.admin_icon_auth_r}</td>
					<td class="coltop">{PHP.R.admin_icon_auth_w}</td>
					<td class="coltop">{PHP.R.admin_icon_auth_1}</td>
					<!-- IF {PHP.advanced} --><td class="coltop">{PHP.R.admin_icon_auth_2}</td>
					<td class="coltop">{PHP.R.admin_icon_auth_3}</td>
					<td class="coltop">{PHP.R.admin_icon_auth_4}</td>
					<td class="coltop">{PHP.R.admin_icon_auth_5}</td><!-- ENDIF -->
					<td class="coltop">{PHP.R.admin_icon_auth_a}</td>
				</tr>
<!-- BEGIN: RIGHTS_ROW_PAGES -->
				<tr>
					<td class="centerall"><img src="images/icons/default/{ADMIN_RIGHTS_ROW_AUTH_CODE}.png" alt="" /></td>
					<td><a href="{ADMIN_RIGHTS_ROW_LINK}">{ADMIN_RIGHTS_ROW_TITLE}</a></td>
<!-- BEGIN: ROW_PAGES_ITEMS -->
					<td class="centerall">
						<!-- IF {PHP.out.tpl_rights_parseline_locked} AND {PHP.out.tpl_rights_parseline_state} --><input type="hidden" name="{ADMIN_RIGHTS_ROW_ITEMS_NAME}" value="1" />
						{PHP.R.admin_icon_discheck1}<!-- ENDIF -->
						<!-- IF {PHP.out.tpl_rights_parseline_locked} AND !{PHP.out.tpl_rights_parseline_state} -->{PHP.R.admin_icon_discheck0}<!-- ENDIF -->
						<!-- IF !{PHP.out.tpl_rights_parseline_locked} --><input type="checkbox" class="checkbox" name="{ADMIN_RIGHTS_ROW_ITEMS_NAME}"{ADMIN_RIGHTS_ROW_ITEMS_CHECKED}{ADMIN_RIGHTS_ROW_ITEMS_DISABLED} /><!-- ENDIF -->
					</td>
<!-- END: ROW_PAGES_ITEMS -->
					<td class="centerall"><a title="{PHP.L.Rights}" href="{ADMIN_RIGHTS_ROW_RIGHTSBYITEM}">{PHP.R.admin_icon_rights2}</a></td>
					<td class="centerall">{ADMIN_RIGHTS_ROW_USER}{ADMIN_RIGHTS_ROW_PRESERVE}</td>
				</tr>
<!-- END: RIGHTS_ROW_PAGES -->
			</table>
			<h3>{PHP.L.Plugins}:</h3>
			<table class="cells">
				<tr>
					<td class="coltop" style="width:5%;" rowspan="2"></td>
					<td class="coltop" style="width:25%;" rowspan="2">{PHP.L.Section}</td>
					<td class="coltop" style="width:40%;" colspan="{ADMIN_RIGHTS_ADV_COLUMNS}">{PHP.L.Rights}</td>
					<td class="coltop" style="width:15%;" rowspan="2">{PHP.L.adm_rightspergroup}</td>
					<td class="coltop" style="width:15%;" rowspan="2">{PHP.L.adm_setby}</td>
				</tr>
				<tr>
					<td class="coltop">{PHP.R.admin_icon_auth_r}</td>
					<td class="coltop">{PHP.R.admin_icon_auth_w}</td>
					<td class="coltop">{PHP.R.admin_icon_auth_1}</td>
					<!-- IF {PHP.advanced} --><td class="coltop">{PHP.R.admin_icon_auth_2}</td>
					<td class="coltop">{PHP.R.admin_icon_auth_3}</td>
					<td class="coltop">{PHP.R.admin_icon_auth_4}</td>
					<td class="coltop">{PHP.R.admin_icon_auth_5}</td><!-- ENDIF -->
					<td class="coltop">{PHP.R.admin_icon_auth_a}</td>
				</tr>
<!-- BEGIN: RIGHTS_ROW_PLUGINS -->
				<tr>
					<td class="centerall"><img src="images/icons/default/{ADMIN_RIGHTS_ROW_AUTH_CODE}.png" alt="" /></td>
					<td><a href="{ADMIN_RIGHTS_ROW_LINK}">{ADMIN_RIGHTS_ROW_TITLE}</a></td>
<!-- BEGIN: ROW_PLUGINS_ITEMS -->
					<td class="centerall">
						<!-- IF {PHP.out.tpl_rights_parseline_locked} AND {PHP.out.tpl_rights_parseline_state} --><input type="hidden" name="{ADMIN_RIGHTS_ROW_ITEMS_NAME}" value="1" />
						{PHP.R.admin_icon_discheck1}<!-- ENDIF -->
						<!-- IF {PHP.out.tpl_rights_parseline_locked} AND !{PHP.out.tpl_rights_parseline_state} -->{PHP.R.admin_icon_discheck0}<!-- ENDIF -->
						<!-- IF !{PHP.out.tpl_rights_parseline_locked} --><input type="checkbox" class="checkbox" name="{ADMIN_RIGHTS_ROW_ITEMS_NAME}"{ADMIN_RIGHTS_ROW_ITEMS_CHECKED}{ADMIN_RIGHTS_ROW_ITEMS_DISABLED} /><!-- ENDIF -->
					</td>
<!-- END: ROW_PLUGINS_ITEMS -->
					<td class="centerall"><a title="{PHP.L.Rights}" href="{ADMIN_RIGHTS_ROW_RIGHTSBYITEM}">{PHP.R.admin_icon_rights2}</a></td>
					<td class="centerall">{ADMIN_RIGHTS_ROW_USER}{ADMIN_RIGHTS_ROW_PRESERVE}</td>
				</tr>
<!-- END: RIGHTS_ROW_PLUGINS -->
				<tr>
					<td class="textcenter" colspan="{ADMIN_RIGHTS_4ADV_COLUMNS}"><a href="{ADMIN_RIGHTS_ADVANCED_URL}">{PHP.L.More}</a></td>
				</tr>
				<tr>
					<td class="valid" colspan="{ADMIN_RIGHTS_4ADV_COLUMNS}" style="text-align:center;"><input type="submit" class="submit" value="{PHP.L.Update}" /></td>
				</tr>
			</table>
			</form>
	</div>
<!-- END: MAIN -->

<!-- BEGIN: RIGHTS_HELP -->
		<p>{PHP.R.admin_icon_auth_r}&nbsp; {PHP.L.Read}</p>
		<p>{PHP.R.admin_icon_auth_w}&nbsp; {PHP.L.Write}</p>
		<p>{PHP.R.admin_icon_auth_1}&nbsp; {PHP.L.Custom} #1</p>
		<!-- IF {PHP.advanced} --><p>{PHP.R.admin_icon_auth_2}&nbsp; {PHP.L.Custom} #2</p>
		<p>{PHP.R.admin_icon_auth_3}&nbsp; {PHP.L.Custom} #3</p>
		<p>{PHP.R.admin_icon_auth_4}&nbsp; {PHP.L.Custom} #4</p>
		<p>{PHP.R.admin_icon_auth_5}&nbsp; {PHP.L.Custom} #5</p><!-- ENDIF -->
		<p>{PHP.R.admin_icon_auth_a}&nbsp; {PHP.L.Administration}</p>
<!-- END: RIGHTS_HELP -->