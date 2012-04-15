<!-- BEGIN: MAIN -->
		<h2>{PHP.L.Rights}</h2>
		{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}
		<div class="block">
			<form name="saverightsbyitem" id="saverightsbyitem" action="{ADMIN_RIGHTSBYITEM_FORM_URL}" method="post" class="ajax">
				<table class="cells">
					<tr>
						<td class="coltop width5" rowspan="2"></td>
						<td class="coltop width25" rowspan="2">{PHP.L.Groups}</td>
						<td class="coltop width40" colspan="{ADMIN_RIGHTSBYITEM_ADV_COLUMNS}">{PHP.L.Rights}</td>
						<td class="coltop width15" rowspan="2">{PHP.L.Open}</td>
						<td class="coltop width15" rowspan="2">{PHP.L.adm_setby}</td>
					</tr>
					<tr>
						<td class="coltop">{PHP.R.admin_icon_auth_r}</td>
						<td class="coltop">{PHP.R.admin_icon_auth_w}</td>
						<!-- IF {PHP.advanced} OR {PHP.ic} == 'page' --><td class="coltop">{PHP.R.admin_icon_auth_1}</td><!-- ENDIF -->
						<!-- IF {PHP.advanced} --><td class="coltop">{PHP.R.admin_icon_auth_2}</td>
						<td class="coltop">{PHP.R.admin_icon_auth_3}</td>
						<td class="coltop">{PHP.R.admin_icon_auth_4}</td>
						<td class="coltop">{PHP.R.admin_icon_auth_5}</td><!-- ENDIF -->
						<td class="coltop">{PHP.R.admin_icon_auth_a}</td>
					</tr>
<!-- BEGIN: RIGHTSBYITEM_ROW -->
					<tr>
						<td class="centerall"><img src="{PHP.cfg.system_dir}/admin/img/users.png"/></td>
						<td><a href="{ADMIN_RIGHTSBYITEM_ROW_LINK}">{ADMIN_RIGHTSBYITEM_ROW_TITLE}</a></td>
<!-- BEGIN: ROW_ITEMS -->
						<td class="centerall">
							<!-- IF {PHP.out.tpl_rights_parseline_locked} AND {PHP.out.tpl_rights_parseline_state} --><input type="hidden" name="{ADMIN_RIGHTSBYITEM_ROW_ITEMS_NAME}" value="1" />
							{PHP.R.admin_icon_discheck1}<!-- ENDIF -->
							<!-- IF {PHP.out.tpl_rights_parseline_locked} AND !{PHP.out.tpl_rights_parseline_state} -->{PHP.R.admin_icon_discheck0}<!-- ENDIF -->
							<!-- IF !{PHP.out.tpl_rights_parseline_locked} --><input type="checkbox" class="checkbox" name="{ADMIN_RIGHTSBYITEM_ROW_ITEMS_NAME}"{ADMIN_RIGHTSBYITEM_ROW_ITEMS_CHECKED}{ADMIN_RIGHTSBYITEM_ROW_ITEMS_DISABLED} /><!-- ENDIF -->
						</td>
<!-- END: ROW_ITEMS -->
						<td class="centerall"><a title="{PHP.L.Open}" href="{ADMIN_RIGHTSBYITEM_ROW_JUMPTO}" class="button special">{PHP.L.Open}</a><a title="{PHP.L.Open}" href="{ADMIN_RIGHTSBYITEM_ROW_LINK}" class="button">{PHP.L.Rights}</a> </td>
						<td class="textcenter">{ADMIN_RIGHTSBYITEM_ROW_USER}{ADMIN_RIGHTSBYITEM_ROW_PRESERVE}</td>
						
					</tr>
<!-- END: RIGHTSBYITEM_ROW -->
					<tr>
						<td class="textcenter" colspan="{ADMIN_RIGHTSBYITEM_4ADV_COLUMNS}">
							<a href="{ADMIN_RIGHTSBYITEM_ADVANCED_URL}">{PHP.L.More}</a>
						</td>
					</tr>
					<tr>
						<td class="valid" colspan="{ADMIN_RIGHTSBYITEM_4ADV_COLUMNS}">
							<input type="submit" class="submit" value="{PHP.L.Update}" />
						</td>
					</tr>
				</table>
			</form>
		</div>
<!-- END: MAIN -->

<!-- BEGIN: RIGHTSBYITEM_HELP -->
		<p>{PHP.R.admin_icon_auth_r}&nbsp; {PHP.L.Read}</p>
		<p>{PHP.R.admin_icon_auth_w}&nbsp; {PHP.L.Write}</p>
		<!-- IF {PHP.advanced} OR {PHP.ic} == 'page' --><p>{PHP.R.admin_icon_auth_1}&nbsp; {PHP.l_custom1}</p><!-- ENDIF -->
		<!-- IF {PHP.advanced} --><p>{PHP.R.admin_icon_auth_2}&nbsp; {PHP.L.Custom} #2</p>
		<p>{PHP.R.admin_icon_auth_3}&nbsp; {PHP.L.Custom} #3</p>
		<p>{PHP.R.admin_icon_auth_4}&nbsp; {PHP.L.Custom} #4</p>
		<p>{PHP.R.admin_icon_auth_5}&nbsp; {PHP.L.Custom} #5</p><!-- ENDIF -->
		<p>{PHP.R.admin_icon_auth_a}&nbsp; {PHP.L.Administration}</p>
<!-- END: RIGHTSBYITEM_HELP -->