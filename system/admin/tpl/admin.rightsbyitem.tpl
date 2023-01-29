<!-- BEGIN: MAIN -->
{FILE "{PHP.cfg.system_dir}/admin/tpl/warnings.tpl"}
<div class="block">
	<h2>{ADMIN_RIGHTSBYITEM_TITLE}</h2>
	<div class="wrapper">
		<form name="saverightsbyitem" id="saverightsbyitem" action="{ADMIN_RIGHTSBYITEM_FORM_URL}" method="post" class="ajax">
			{ADMIN_RIGHTSBYITEM_FORM_ITEMS}
			<table class="cells">
				<thead>
					<tr>
						<th class="" rowspan="2"></th>
						<th class="" rowspan="2">{PHP.L.Groups}</th>
						<th class="" colspan="{ADMIN_RIGHTSBYITEM_ADV_COLUMNS}">{PHP.L.Rights}</th>
						<th class="" rowspan="2">{PHP.L.Open}</th>
						<th class="" rowspan="2">{PHP.L.adm_setby}</th>
					</tr>
					<tr>
						<th class="">{PHP.R.admin_icon_auth_r}</th>
						<th class="">{PHP.R.admin_icon_auth_w}</th>
						<!-- IF {PHP.advanced} OR {PHP.ic} == 'page' -->
						<th class="">{PHP.R.admin_icon_auth_1}</th>
						<!-- ENDIF -->
						<!-- IF {PHP.advanced} -->
						<th class="">{PHP.R.admin_icon_auth_2}</th>
							<th class="">{PHP.R.admin_icon_auth_3}</th>
							<th class="">{PHP.R.admin_icon_auth_4}</th>
							<th class="">{PHP.R.admin_icon_auth_5}</th>
						<!-- ENDIF -->
						<th class="coltop">{PHP.R.admin_icon_auth_a}</th>
					</tr>
				</thead>
				<!-- BEGIN: RIGHTSBYITEM_ROW -->
				<tr>
					<td class="centerall">
						<img src="{PHP.cfg.icons_dir}/default/modules/users.png"/>
					</td>
					<td>
						<a href="{ADMIN_RIGHTSBYITEM_ROW_LINK}">{ADMIN_RIGHTSBYITEM_ROW_TITLE}</a>
					</td>
					<!-- BEGIN: ROW_ITEMS -->
					<td class="centerall">
						<!-- IF {ADMIN_RIGHTSBYITEM_ROW_ITEMS_LOCKED} AND {ADMIN_RIGHTSBYITEM_ROW_ITEMS_STATE} -->
							<input type="hidden" name="{ADMIN_RIGHTSBYITEM_ROW_ITEMS_NAME}" value="1" />
							{PHP.R.admin_icon_discheck1}
						<!-- ENDIF -->
						<!-- IF {ADMIN_RIGHTSBYITEM_ROW_ITEMS_LOCKED} AND !{ADMIN_RIGHTSBYITEM_ROW_ITEMS_STATE} -->
							{PHP.R.admin_icon_discheck0}
						<!-- ENDIF -->
						<!-- IF !{ADMIN_RIGHTSBYITEM_ROW_ITEMS_LOCKED} -->
							<input type="checkbox" class="checkbox" name="{ADMIN_RIGHTSBYITEM_ROW_ITEMS_NAME}"{ADMIN_RIGHTSBYITEM_ROW_ITEMS_CHECKED}{ADMIN_RIGHTSBYITEM_ROW_ITEMS_DISABLED} />
						<!-- ENDIF -->
					</td>
					<!-- END: ROW_ITEMS -->
					<td class="action">
						<a title="{PHP.L.Open}" href="{ADMIN_RIGHTSBYITEM_ROW_JUMPTO}" class="button special">{PHP.L.Open}</a>
						<a title="{PHP.L.Open}" href="{ADMIN_RIGHTSBYITEM_ROW_LINK}" class="button">{PHP.L.Rights}</a>
					</td>
					<td class="textcenter">
						{ADMIN_RIGHTSBYITEM_ROW_USER}{ADMIN_RIGHTSBYITEM_ROW_PRESERVE}
					</td>
				</tr>
				<!-- END: RIGHTSBYITEM_ROW -->
				<tr>
					<td class="action" colspan="{ADMIN_RIGHTSBYITEM_4ADV_COLUMNS}">
						<a href="{ADMIN_RIGHTSBYITEM_ADVANCED_URL}" class="button">{PHP.L.More}</a>
						<input type="submit" class="submit" value="{PHP.L.Update}" />
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>
<!-- END: MAIN -->

<!-- BEGIN: RIGHTSBYITEM_HELP -->
<p>{PHP.R.admin_icon_auth_r}&nbsp; {PHP.L.Read}</p>
<p>{PHP.R.admin_icon_auth_w}&nbsp; {PHP.L.Write}</p>
<!-- IF {PHP.advanced} OR {PHP.ic} == 'page' -->
<p>{PHP.R.admin_icon_auth_1}&nbsp; {PHP.l_custom1}</p>
<!-- ENDIF -->
<!-- IF {PHP.advanced} -->
<p>{PHP.R.admin_icon_auth_2}&nbsp; {PHP.L.Custom} #2</p>
<p>{PHP.R.admin_icon_auth_3}&nbsp; {PHP.L.Custom} #3</p>
<p>{PHP.R.admin_icon_auth_4}&nbsp; {PHP.L.Custom} #4</p>
<p>{PHP.R.admin_icon_auth_5}&nbsp; {PHP.L.Custom} #5</p>
<!-- ENDIF -->
<p>{PHP.R.admin_icon_auth_a}&nbsp; {PHP.L.Administration}</p>
<!-- END: RIGHTSBYITEM_HELP -->
