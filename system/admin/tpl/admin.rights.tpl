

<!-- BEGIN: MAIN -->
{FILE "{PHP.cfg.system_dir}/admin/tpl/warnings.tpl"}

<form name="saverights" id="saverights" action="{ADMIN_RIGHTS_FORM_URL}" method="post" class="ajax">
	{ADMIN_RIGHTS_FORM_ITEMS}

	<!-- IF {PHP.g} > 5 -->
	<table class="cells">
		<tr>
			<td>
				<input type="checkbox" class="checkbox" name="ncopyrightsconf" />
				{PHP.L.adm_copyrightsfrom}: {ADMIN_RIGHTS_SELECTBOX_GROUPS} &nbsp;
				<input type="submit" class="submit" value="{PHP.L.Update}" />
			</td>
		</tr>
	</table>
	<!-- ENDIF -->

	<!-- BEGIN: RIGHTS_SECTION -->
	<div class="block">
		<h2>{RIGHTS_SECTION_TITLE}:</h2>
		<div class="wrapper">
			<table class="cells">
				<thead>
					<tr>
						<th class="" rowspan="2"></th>
						<th class="" rowspan="2">{PHP.L.Section}</th>
						<th class="" colspan="{ADMIN_RIGHTS_ADV_COLUMNS}">{PHP.L.Section}</th>
						<th class="" rowspan="2">{PHP.L.adm_rightspergroup}</th>
						<th class="" rowspan="2">{PHP.L.adm_setby}</th>
					</tr>
					<tr>
						<th class="">{PHP.R.admin_icon_auth_r}</th>
						<th class="">{PHP.R.admin_icon_auth_w}</th>
						<th class="">{PHP.R.admin_icon_auth_1}</th>
						<!-- IF {PHP.advanced} -->
						<th class="">{PHP.R.admin_icon_auth_2}</th>
						<th class="">{PHP.R.admin_icon_auth_3}</th>
						<th class="">{PHP.R.admin_icon_auth_4}</th>
						<th class="">{PHP.R.admin_icon_auth_5}</th>
						<!-- ENDIF -->
						<th class="">{PHP.R.admin_icon_auth_a}</th>
					</tr>
				</thead>
				<tbody>
					<!-- BEGIN: RIGHTS_ROW -->
					<tr>
						<td class="centerall">
							<!-- IF {ADMIN_RIGHTS_ROW_ICON} -->
							{ADMIN_RIGHTS_ROW_ICON}
							<!-- ELSE -->
							<img src="{PHP.cfg.icons_dir}/default/default.png" alt="" />
							<!-- ENDIF -->
						</td>
						<td>
							<a href="{ADMIN_RIGHTS_ROW_LINK}">{ADMIN_RIGHTS_ROW_TITLE}</a>
						</td>
						<!-- BEGIN: RIGHTS_ROW_ITEMS -->
						<td class="centerall">
							<!-- IF {ADMIN_RIGHTS_ROW_ITEMS_LOCKED} AND {ADMIN_RIGHTS_ROW_ITEMS_STATE} -->
							<input type="hidden" name="{ADMIN_RIGHTS_ROW_ITEMS_NAME}" value="1" />
							{PHP.R.admin_icon_discheck1}
							<!-- ENDIF -->
							<!-- IF {ADMIN_RIGHTS_ROW_ITEMS_LOCKED} AND !{ADMIN_RIGHTS_ROW_ITEMS_STATE} -->
							{PHP.R.admin_icon_discheck0}
							<!-- ENDIF -->
							<!-- IF !{ADMIN_RIGHTS_ROW_ITEMS_LOCKED} -->
							<input type="checkbox" class="checkbox" name="{ADMIN_RIGHTS_ROW_ITEMS_NAME}"{ADMIN_RIGHTS_ROW_ITEMS_CHECKED}{ADMIN_RIGHTS_ROW_ITEMS_DISABLED} />
							<!-- ENDIF -->
						</td>
						<!-- END: RIGHTS_ROW_ITEMS -->
						<td class="centerall">
							<a title="{PHP.L.Rights}" href="{ADMIN_RIGHTS_ROW_RIGHTSBYITEM}" class="button">{PHP.L.Rights}</a>
							<a title="{PHP.L.Rights}" href="{ADMIN_RIGHTS_ROW_LINK}" class="button special">{PHP.L.Open}</a>
						</td>
						<td class="textcenter">{ADMIN_RIGHTS_ROW_USER}{ADMIN_RIGHTS_ROW_PRESERVE}</td>
					</tr>
					<!-- END: RIGHTS_ROW -->
				</tbody>
			</table>
		</div>
	</div>
	<!-- END: RIGHTS_SECTION -->

	<div class="button-toolbar">
		<a href="{ADMIN_RIGHTS_ADVANCED_URL}" class="button">{PHP.L.More}</a>
		<input type="submit" value="{PHP.L.Update}" />
	</div>

</form>
<!-- END: MAIN -->

<!-- BEGIN: RIGHTS_HELP -->
<p>{PHP.R.admin_icon_auth_r}&nbsp; {PHP.L.Read}</p>
<p>{PHP.R.admin_icon_auth_w}&nbsp; {PHP.L.Write}</p>
<p>{PHP.R.admin_icon_auth_1}&nbsp; {PHP.L.Custom} #1</p>
<!-- IF {PHP.advanced} -->
<p>{PHP.R.admin_icon_auth_2}&nbsp; {PHP.L.Custom} #2</p>
<p>{PHP.R.admin_icon_auth_3}&nbsp; {PHP.L.Custom} #3</p>
<p>{PHP.R.admin_icon_auth_4}&nbsp; {PHP.L.Custom} #4</p>
<p>{PHP.R.admin_icon_auth_5}&nbsp; {PHP.L.Custom} #5</p>
<!-- ENDIF -->
<p>{PHP.R.admin_icon_auth_a}&nbsp; {PHP.L.Administration}</p>
<!-- END: RIGHTS_HELP -->
