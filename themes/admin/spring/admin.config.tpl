<!-- BEGIN: MAIN -->
		<h2>{PHP.L.Configuration}</h2>
		{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}

<!-- BEGIN: EDIT -->
			<form name="saveconfig" id="saveconfig" action="{ADMIN_CONFIG_FORM_URL}" method="post" class="ajax">
            <table class="cells">
				<tr>
					<td class="coltop width30">{PHP.L.Parameter}</td>
					<td class="coltop width60">{PHP.L.Value}</td>
					<td class="coltop width10">{PHP.L.Reset}</td>
				</tr>
<!-- BEGIN: ADMIN_CONFIG_ROW -->
<!-- BEGIN: ADMIN_CONFIG_FIELDSET_BEGIN -->
				<tr>
					<td class="group_begin" colspan="3">
						<h4>{ADMIN_CONFIG_FIELDSET_TITLE}</h4>
					</td>
				</tr>
<!-- END: ADMIN_CONFIG_FIELDSET_BEGIN -->
<!-- BEGIN: ADMIN_CONFIG_ROW_OPTION -->
				<tr>
					<td>{ADMIN_CONFIG_ROW_CONFIG_TITLE}:</td>
					<td>
						{ADMIN_CONFIG_ROW_CONFIG}
						<div class="adminconfigmore">{ADMIN_CONFIG_ROW_CONFIG_MORE}</div>
					</td>
					<td class="centerall">
						<a href="{ADMIN_CONFIG_ROW_CONFIG_MORE_URL}" class="ajax">
							{PHP.R.admin_icon_reset}
						</a>
					</td>
				</tr>
<!-- END: ADMIN_CONFIG_ROW_OPTION -->
<!-- BEGIN: ADMIN_CONFIG_FIELDSET_END -->
				<tr>
					<td class="group_end" colspan="3"></td>
				</tr>
<!-- END: ADMIN_CONFIG_FIELDSET_END -->
<!-- END: ADMIN_CONFIG_ROW -->
				<tr>
					<td class="valid" colspan="3">
						<input type="submit" class="submit" value="{PHP.L.Update}" />
					</td>
				</tr>
			</table>
			</form>
<!-- END: EDIT -->
<!-- BEGIN: DEFAULT -->
			<div class="col first">
            <ul class="icons">
                <li>
                    <a href="admin.php?m=config&n=edit&o=core&p=main"><img src="themes/admin/spring/img/big_icons/wrench.png"><br>{PHP.L.core_main}</a>
                </li>
                <li>
                    <a href="admin.php?m=config&n=edit&o=core&p=title"><img src="themes/admin/spring/img/big_icons/layout_header.png"><br>{PHP.L.core_title}</a>
                </li>
                <li>
                    <a href="admin.php?m=config&n=edit&o=core&p=menus"><img src="themes/admin/spring/img/big_icons/application_side_tree.png"><br>{PHP.L.core_menus}</a>
                </li>
                <li>
                    <a href="admin.php?m=config&n=edit&o=core&p=locale"><img src="themes/admin/spring/img/big_icons/world.png"><br>{PHP.L.core_locale}</a>
                </li>
                <li>
                    <a href="admin.php?m=config&n=edit&o=core&p=theme"><img src="themes/admin/spring/img/big_icons/html.png"><br>{PHP.L.core_theme}</a>
                </li>
                <li>
                    <a href="admin.php?m=config&n=edit&o=core&p=performance"><img src="themes/admin/spring/img/big_icons/speedometer.png"><br>{PHP.L.core_performance}</a>
                </li>
                <li>
                    <a href="admin.php?m=config&n=edit&o=core&p=users"><img src="themes/admin/spring/img/big_icons/group.png"><br>{PHP.L.core_users}</a>
                </li>
			</ul>
            </div>

            <div class="col first">
            <h2 class="margintop10">{PHP.L.adm_extrafields}</h2>
            <ul class="icons">
                <li>
                    <a href="admin.php?m=extrafields&n=cot_pages"><img src="themes/admin/spring/img/big_icons/page_gear.png">{PHP.L.adm_extrafields_icon_pages}</a>
                </li>
                <li>
                    <a href="admin.php?m=extrafields&n=cot_structure"><img src="themes/admin/spring/img/big_icons/table_gear.png">{PHP.L.adm_extrafields_icon_structure}</a>
                </li>
                <li>
                    <a href="admin.php?m=extrafields&n=cot_users"><img src="themes/admin/spring/img/big_icons/group_gear.png">{PHP.L.adm_extrafields_icon_users}</a>
                </li>
                <li>
                    <a href="admin.php?m=extrafields&alltables=1"><img src="themes/admin/spring/img/big_icons/gear_in.png">{PHP.L.adm_extrafields_icon_extra}</a>
                </li>
            </ul>
            </div>

<!-- END: DEFAULT -->

<!-- END: MAIN -->