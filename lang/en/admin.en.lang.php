<?php

/**
 * English Language File for the Admin Module (admin.en.lang.php)
 *
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Common words
 */
$L['Extension'] = 'Extension';
$L['Extensions'] = 'Extensions';
$L['Parameter'] = 'Parameter';
$L['Structure'] = 'Structure';

/**
 * Home Section
 */
$L['home_installable_error'] = 'Please remove install.php until next update or at least protect config.php from being writable';

$L['home_newusers'] = 'New members';
$L['home_newpages'] = 'New pages';
$L['home_newtopics'] = 'New topics';
$L['home_newposts'] = 'New posts';
$L['home_newpms'] = 'New private messages';

$L['home_db_rows'] = 'SQL database, number of rows';
$L['home_db_indexsize'] = 'SQL database, index size (KB)';
$L['home_db_datassize'] = 'SQL database, datas size (KB)';
$L['home_db_totalsize'] = 'SQL database, total size (KB)';

$L['home_ql_b1_title'] = 'Site properties';
$L['home_ql_b1_1'] = 'Basic config';
$L['home_ql_b1_2'] = 'Titles';
$L['home_ql_b1_3'] = 'Theme and charset';
$L['home_ql_b1_4'] = 'Menu slots in tpl-files';
$L['home_ql_b1_5'] = 'Language';
$L['home_ql_b1_6'] = 'Time and date';

$L['home_ql_b2_1'] = 'Structure of the pages (categories)';
$L['home_ql_b2_2'] = 'Extra fields for pages';
$L['home_ql_b2_3'] = 'Extra fields for categories';
$L['home_ql_b2_4'] = 'Parser config';

$L['home_ql_b3_1'] = 'Basic config';
$L['home_ql_b3_2'] = 'Extra fields for users';
$L['home_ql_b3_4'] = 'User rights';

$L['home_update_notice'] = 'Update available';
$L['home_update_revision'] = 'Current version: <span style="color:#C00;font-weight:bold;">%1$s</span><br />New version: <span style="color:#4E9A06;font-weight:bold;">%2$s</span>'; // %1/%2 Current version/revision %3/%4 Updated version/revision

/**
 * Config Section
 */
$L['core_forums'] = &$L['Forums'];
$L['core_locale'] = &$L['Locale'];
$L['core_locale_desc'] = 'Default language and time zone settings';
$L['core_main'] = 'Main Settings';
$L['core_main_desc'] = 'Website configuration, global list settings';
$L['core_menus'] = &$L['Menus'];
$L['core_menus_desc'] = 'Slots for posting plain text information';
$L['core_page'] = &$L['Pages'];
$L['core_parser'] = &$L['Parser'];
$L['core_performance'] = 'Performance';
$L['core_performance_desc'] = 'Gzip compression, resource consolidation, enable Ajax and jQuery';
$L['core_pfs'] = &$L['PFS'];
$L['core_plug'] = &$L['Plugins'];
$L['core_pm'] = &$L['Private_Messages'];
$L['core_polls'] = &$L['Polls'];
$L['core_rss'] = &$L['RSS_Feeds'];
$L['core_security'] = &$L['Security'];
$L['core_security_desc'] = 'Protection, CAPTCHA, debug and maintenance modes';
$L['core_sessions'] = 'Sessions';
$L['core_sessions_desc'] = 'Setup cookies and remember authorization, login/logout redirects';
$L['core_structure'] = &$L['Categories'];
$L['core_theme'] = &$L['Themes'];
$L['core_theme_desc'] = 'Manage theme defaults and markup elements';
$L['core_time'] = 'Time and Date';
$L['core_title'] = 'Titles and Metas';
$L['core_title_desc'] = 'Setup META Title for the homepage and internals';

$L['cfg_struct_defaults'] = 'Structure Defaults';

/**
 * Shortcuts
 */
$L['short_admin'] = 'Admin';
$L['short_config'] = 'Config';
$L['short_delete'] = 'Delete';
$L['short_open'] = 'Open';
$L['short_options'] = 'Options';
$L['short_rights'] = 'Rights';
$L['short_struct'] = 'Struct';

/**
 * Config Section
 * Locale Subsection
 */
$L['cfg_forcedefaultlang'] = 'Force the default language for all users';
$L['cfg_forcedefaultlang_hint'] = '';
$L['cfg_defaulttimezone'] = 'Default time zone';
$L['cfg_defaulttimezone_hint'] = 'For guests and new members';

/**
 * Config Section
 * Main Subsection
 */
$L['cfg_adminemail'] = 'Administrator\'s email';
$L['cfg_adminemail_hint'] = 'Required';
$L['cfg_clustermode'] = 'Cluster of servers';
$L['cfg_clustermode_hint'] = 'Set to yes if it\'s a load balanced setup.';
$L['cfg_confirmlinks'] = 'Confirm potentially dangerous actions';
$L['cfg_default_show_installed'] = 'Show only installed extensions by default';
$L['cfg_easypagenav'] = 'User friendly pagination';
$L['cfg_easypagenav_hint'] = 'Uses page numbers in URLs instead of DB offsets';
$L['cfg_hostip'] = 'Server IP';
$L['cfg_hostip_hint'] = 'The IP of the server, optional.';
$L['cfg_maxrowsperpage'] = 'Max. items per page';
$L['cfg_maxrowsperpage_hint'] = 'Default item limit for pagination';
$L['cfg_parser'] = 'Markup parser';
$L['cfg_parser_hint'] = 'Default is: plain text';

/**
 * Config Section
 * Menus Subsection
 */
$L['cfg_banner'] = 'Banner<br />{HEADER_BANNER} in header.tpl';
$L['cfg_banner_hint'] = '';
$L['cfg_bottomline'] = 'Bottom line<br />{FOOTER_BOTTOMLINE} in footer.tpl';
$L['cfg_bottomline_hint'] = '';
$L['cfg_topline'] = 'Top line<br />{HEADER_TOPLINE} in header.tpl';
$L['cfg_topline_hint'] = '';

$L['cfg_freetext1'] = 'Freetext Slot #1<br />{PHP.cfg.freetext1} in all tpl files';
$L['cfg_freetext1_hint'] = '';
$L['cfg_freetext2'] = 'Freetext Slot #2<br />{PHP.cfg.freetext2} in all tpl files';
$L['cfg_freetext2_hint'] = '';
$L['cfg_freetext3'] = 'Freetext Slot #3<br />{PHP.cfg.freetext3} in all tpl files';
$L['cfg_freetext3_hint'] = '';
$L['cfg_freetext4'] = 'Freetext Slot #4<br />{PHP.cfg.freetext4} in all tpl files';
$L['cfg_freetext4_hint'] = '';
$L['cfg_freetext5'] = 'Freetext Slot #5<br />{PHP.cfg.freetext5} in all tpl files';
$L['cfg_freetext5_hint'] = '';
$L['cfg_freetext6'] = 'Freetext Slot #6<br />{PHP.cfg.freetext6} in all tpl files';
$L['cfg_freetext6_hint'] = '';
$L['cfg_freetext7'] = 'Freetext Slot #7<br />{PHP.cfg.freetext7} in all tpl files';
$L['cfg_freetext7_hint'] = '';
$L['cfg_freetext8'] = 'Freetext Slot #8<br />{PHP.cfg.freetext8} in all tpl files';
$L['cfg_freetext8_hint'] = '';
$L['cfg_freetext9'] = 'Freetext Slot #9<br />{PHP.cfg.freetext9} in all tpl files';
$L['cfg_freetext9_hint'] = '';

$L['cfg_menu1'] = 'Menu slot #1<br />{PHP.cfg.menu1} in all tpl files';
$L['cfg_menu1_hint'] = '';
$L['cfg_menu2'] = 'Menu slot #2<br />{PHP.cfg.menu2} in all tpl files';
$L['cfg_menu2_hint'] = '';
$L['cfg_menu3'] = 'Menu slot #3<br />{PHP.cfg.menu3} in all tpl files';
$L['cfg_menu3_hint'] = '';
$L['cfg_menu4'] = 'Menu slot #4<br />{PHP.cfg.menu4} in all tpl files';
$L['cfg_menu4_hint'] = '';
$L['cfg_menu5'] = 'Menu slot #5<br />{PHP.cfg.menu5} in all tpl files';
$L['cfg_menu5_hint'] = '';
$L['cfg_menu6'] = 'Menu slot #6<br />{PHP.cfg.menu6} in all tpl files';
$L['cfg_menu6_hint'] = '';
$L['cfg_menu7'] = 'Menu slot #7<br />{PHP.cfg.menu7} in all tpl files';
$L['cfg_menu7_hint'] = '';
$L['cfg_menu8'] = 'Menu slot #8<br />{PHP.cfg.menu8} in all tpl files';
$L['cfg_menu8_hint'] = '';
$L['cfg_menu9'] = 'Menu slot #9<br />{PHP.cfg.menu9} in all tpl files';
$L['cfg_menu9_hint'] = '';

/**
 * Config Section
 * Performance Subsection
 */
$L['cfg_gzip'] = 'Gzip';
$L['cfg_gzip_hint'] = 'Gzip compression of the HTML output. Do not enable it if your server already applies Gzip to your pages. Use this tool to test if Gzip is already enabled on your site: <a target="_blank" href="http://www.whatsmyip.org/http-compression-test/">HTTP Compression Test</a>';
$L['cfg_headrc_consolidate'] = 'Consolidate header and footer resources (JS/CSS)';
$L['cfg_headrc_minify'] = 'Minify consolidated JS/CSS';
$L['cfg_jquery_cdn'] = 'Use jQuery from this CDN URL';
$L['cfg_jquery_cdn_hint'] = 'Example: https://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js';
$L['cfg_jquery'] = 'Enable jQuery';
$L['cfg_jquery_hint'] = '';
$L['cfg_turnajax'] = 'Enable Ajax';
$L['cfg_turnajax_hint'] = 'Works only if jQuery is enabled';

/**
 * Config Section
 * Security Subsection
 */
$L['cfg_captchamain'] = 'Main captcha';
$L['cfg_captcharandom'] = 'Random captcha';
$L['cfg_hashfunc'] = 'Default hash function';
$L['cfg_hashfunc_hint'] = 'Used to hash passwords';
$L['cfg_referercheck'] = 'Referer check for forms';
$L['cfg_referercheck_hint'] = 'Prevents from cross-domain posting';
$L['cfg_shieldenabled'] = 'Enable the Shield';
$L['cfg_shieldenabled_hint'] = 'Anti-spamming and anti-hammering';
$L['cfg_shieldtadjust'] = 'Adjust Shield timers (in %)';
$L['cfg_shieldtadjust_hint'] = 'The higher, the harder to spam';
$L['cfg_shieldzhammer'] = 'Anti-hammer after * fast hits';
$L['cfg_shieldzhammer_hint'] = 'The smaller, the faster the auto-ban 3 minutes happens';
$L['cfg_devmode'] = 'Debugging mode';
$L['cfg_devmode_hint'] = 'Don\'t let this enabled on live sites';
$L['cfg_maintenance'] = 'Maintenance mode';
$L['cfg_maintenance_hint'] = 'Let only authorized personel access to site';
$L['cfg_maintenancereason'] = 'Maintenance reason';
$L['cfg_maintenancereason_hint'] = 'Optional, should better be short';

/**
 * Config Section
 * Sessions Subsection
 */
$L['cfg_cookiedomain'] = 'Domain for cookies';
$L['cfg_cookiedomain_hint'] = 'Default: empty';
$L['cfg_cookielifetime'] = 'Maximum cookie lifetime';
$L['cfg_cookielifetime_hint'] = 'In seconds';
$L['cfg_cookiepath'] = 'Path for cookies';
$L['cfg_cookiepath_hint'] = 'Default: empty';
$L['cfg_forcerememberme'] = 'Force &quot;remember me&quot;';
$L['cfg_forcerememberme_hint'] = 'Use it on multi-domain sites or if there are sudden logouts';
$L['cfg_timedout'] = 'Idle delay, in seconds';
$L['cfg_timedout_hint'] = 'After this delay, user is away';
$L['cfg_redirbkonlogin'] = 'Redirect back on login';
$L['cfg_redirbkonlogin_hint'] = 'Redirect back to page user viewed before login';
$L['cfg_redirbkonlogout'] = 'Redirect back on logout';
$L['cfg_redirbkonlogout_hint'] = 'Redirect back to page user viewed before logout';

/**
 * Config Section
 * Themes Subsection
 */
$L['cfg_charset'] = 'HTML charset';
$L['cfg_charset_hint'] = '';
$L['cfg_disablesysinfos'] = 'Turn off page creation time';
$L['cfg_disablesysinfos_hint'] = '(used in footer.tpl)';
$L['cfg_doctypeid'] = 'Document Type';
$L['cfg_doctypeid_hint'] = '&lt;!DOCTYPE&gt; of the HTML layout';
$L['cfg_forcedefaulttheme'] = 'Force the default theme for all users';
$L['cfg_forcedefaulttheme_hint'] = '';
$L['cfg_homebreadcrumb'] = 'Show Home in breadcrumb';
$L['cfg_homebreadcrumb_hint'] = 'Put the link to the main page in the beginning of breadcrumb';
$L['cfg_keepcrbottom'] = 'Keep the copyright notice in the tag {FOOTER_BOTTOMLINE}';
$L['cfg_keepcrbottom_hint'] = '(used in footer.tpl)';
$L['cfg_metakeywords'] = 'HTML Meta keywords';
$L['cfg_metakeywords_hint'] = '(comma separated)';
$L['cfg_msg_separate'] = 'Display messages separately for each source';
$L['cfg_msg_separate_hint'] = '';
$L['cfg_separator'] = 'Generic separator';
$L['cfg_separator_hint'] = '(used in breadcrumbs etc)';
$L['cfg_showsqlstats'] = 'Show SQL queries statistics';
$L['cfg_showsqlstats_hint'] = '(used in footer.tpl)';

/**
 * Config Section
 * Title Subsection
 */
$L['cfg_maintitle'] = 'Site title';
$L['cfg_maintitle_hint'] = 'Main title for the website, required';
$L['cfg_subtitle'] = 'Description';
$L['cfg_subtitle_hint'] = 'Optional, will be displayed after the title of the site';
$L['cfg_title_header'] = 'Header title';
$L['cfg_title_header_hint'] = 'Options: {MAINTITLE}, {DESCRIPTION}, {SUBTITLE}';
$L['cfg_title_header_index'] = 'Header Index title';
$L['cfg_title_header_index_hint'] = 'Options: {MAINTITLE}, {DESCRIPTION}, {SUBTITLE}';
$L['cfg_title_users_details'] = 'Users Details title';
$L['cfg_title_users_details_hint'] = 'Options: {USER}, {NAME}';
$L['cfg_subject_mail'] = 'Email subject';
$L['cfg_subject_mail_hint'] = 'Options: {SITE_TITLE}, {SITE_DESCRIPTION}, {MAIL_SUBJECT}';
$L['cfg_body_mail'] = 'Email title';
$L['cfg_body_mail_hint'] = 'Options: {SITE_TITLE}, {SITE_DESCRIPTION}, {SITE_URL}, {ADMIN_EMAIL}, {MAIL_BODY}, {MAIL_SUBJECT}';

/**
 * Config Section
 * Common strings
 */
$L['cfg_css'] = 'Enable module/plugin CSS';
$L['cfg_editor'] = 'Rich text editor';
$L['cfg_editor_hint'] = '';
$L['cfg_markup'] = 'Enable markup';
$L['cfg_markup_hint'] = 'Enables HTML/BBcode or other parsing which is installed in your system';

/**
 * Extension management
 */
$L['ext_already_installed'] = 'This extension is already installed: {$name}';
$L['ext_auth_installed'] = 'Installed authorization defaults';
$L['ext_auth_locks_updated'] = 'Updated authorization locks';
$L['ext_auth_uninstalled'] = 'Removed authorization options';
$L['ext_bindings_installed'] = 'Installed {$cnt} hook bindings';
$L['ext_bindings_uninstalled'] = 'Removed {$cnt} hook bindings';
$L['ext_config_error'] = 'Configuration setup failed';
$L['ext_config_installed'] = 'Installed configuration';
$L['ext_config_uninstalled'] = 'Uninstalled configuration';
$L['ext_config_updated'] = 'Updated configuration options';
$L['ext_config_struct_error'] = 'Structure configuration setup failed';
$L['ext_config_struct_installed'] = 'Installed structure configuration';
$L['ext_config_struct_updated'] = 'Updated structure configuration options';
$L['ext_dependency_error'] = '{$dep_type} &quot;{$dep_name}&quot; required by {$type} &quot;{$name}&quot; is neither installed nor selected for installation';
$L['ext_dependency_uninstall_error'] = '{$type} &quot;{$name}&quot; requires this extension and should be uninstalled first';
$L['ext_executed_php'] = 'Executed PHP handler part: {$ret}';
$L['ext_executed_sql'] = 'Executed SQL handler part: {$ret}';
$L['ext_installing'] = 'Installing {$type} &quot;{$name}&quot;';
$L['ext_invalid_format'] = 'This is not a valid Cotonti >= 0.9 extension. Please contact the developer';
$L['ext_old_format'] = 'This is old Genoa/Seditio plugin. It may work incorrectly or not work at all.';
$L['ext_patch_applied'] = 'Applied patch {$f}: {$msg}';
$L['ext_patch_error'] = 'Error applying patch {$f}: {$msg}';
$L['ext_requires_modules'] = 'Requires modules';
$L['ext_requires_plugins'] = 'Requires plugins';
$L['ext_recommends_modules'] = 'Recommends modules';
$L['ext_recommends_plugins'] = 'Recommends plugins';
$L['ext_setup_not_found'] = 'Setup file is not found: {$path}';
$L['ext_uninstall_confirm'] = 'Are you sure you want to uninstall this extension? Any data linked to the extension will be removed and cannot be recovered.<br/><a href="{$url}">Yes, uninstall and remove data.</a>';
$L['ext_uninstalling'] = 'Uninstalling {$type} &quot;{$name}&quot;';
$L['ext_up2date'] = '{$type} &quot;{$name}&quot; is up to date';
$L['ext_update_error'] = 'Failed updating {$type} &quot;{$name}&quot;';
$L['ext_updated'] = '{$type} &quot;{$name}&quot; has been updated to version {$ver}';
$L['ext_updating'] = 'Updating {$type} &quot;{$name}&quot;';

/**
 * Extension categories
 */
$L['ext_cat_administration-management'] = 'Administration &amp; Management';
$L['ext_cat_commerce'] = 'Commerce &amp; Shopping';
$L['ext_cat_community-social'] = 'Community &amp; Social';
$L['ext_cat_customization-i18n'] = 'Customization &amp; I18n';
$L['ext_cat_data-apis'] = 'Data Feeds &amp; APIs';
$L['ext_cat_development-maintenance'] = 'Development &amp; Maintenance';
$L['ext_cat_editor-parser'] = 'Editors &amp; Markup';
$L['ext_cat_files-media'] = 'Files &amp; Media';
$L['ext_cat_forms-feedback'] = 'Forms &amp; Feedback';
$L['ext_cat_gaming-clans'] = 'Gaming &amp; Clans';
$L['ext_cat_intranet-groupware'] = 'Intranet &amp; Groupware';
$L['ext_cat_misc-ext'] = 'Miscellaneous';
$L['ext_cat_mobile-geolocation'] = 'Mobile &amp; Geolocation';
$L['ext_cat_navigation-structure'] = 'Navigation &amp; Structure';
$L['ext_cat_performance-seo'] = 'Performance &amp; SEO';
$L['ext_cat_publications-events'] = 'Publications &amp; Events';
$L['ext_cat_security-authentication'] = 'Security &amp; Authentication';
$L['ext_cat_utilities-tools'] = 'Utilities &amp; Tools';
$L['ext_cat_post-install'] = 'Post-install Scripts';

/**
  * Structure Section
 */
$L['adm_structure_code_reserved'] = "Structure code 'all' is reserved.";
$L['adm_structure_code_required'] = 'Missing required field: Code';
$L['adm_structure_path_required'] = 'Missing required field: Path';
$L['adm_structure_title_required'] = 'Missing required field: Title';
$L['adm_structure_somenotupdated'] = 'Attention! Some values not updated.';
$L['adm_cat_exists'] = 'A category with such code already exists';
$L['adm_tpl_mode'] = 'Template mode';
$L['adm_tpl_empty'] = 'Default';
$L['adm_tpl_forced'] = 'Same as';
$L['adm_tpl_parent'] = 'Same as the parent category';
$L['adm_tpl_quickcat'] = 'Custom category code';
$L['adm_tpl_resyncalltitle'] = 'Resync all page counters';
$L['adm_tpl_resynctitle'] = 'Resync category page counters';
$L['adm_help_structure'] = 'The pages that belong to the category &quot;system&quot; are not displayed in the public listings, it\'s to make standalone pages.';

/**
 * Structure Section
 * Extrafields Subsection
 */
$L['adm_extrafields_desc'] = 'Add/Edit extra fields for custom data';
$L['adm_extrafields_all'] = 'Show all database tables';
$L['adm_extrafields_table'] = 'Table';
$L['adm_extrafields_help_notused'] = 'Not used';
$L['adm_extrafields_help_variants'] = '{variant1},{variant2},{variant3},...';
$L['adm_extrafields_help_range'] = '{min_value},{max_value}';
$L['adm_extrafields_help_data'] = '{min_year},{max_year},{date_format}. If empty {date_format}, used stamp';
$L['adm_extrafields_help_regex'] = 'Regex for checking';
$L['adm_extrafields_help_file'] = 'Upload directory';
$L['adm_extrafields_help_separator'] = 'Values separator';
$L['adm_help_info'] = '<b>Base HTML</b> set automatically if you leave it blank';
$L['adm_help_newtags'] = '<br /><br /><b>New tags in tpl files:</b>';

/**
 * Users Section
 */
$L['adm_rightspergroup'] = 'Rights per group';
$L['adm_maxsizesingle'] = 'Max size for a single file in '.$L['PFS'].' (KiB)';
$L['adm_maxsizeallpfs'] = 'Max size of all files together in '.$L['PFS'].' (KiB)';
$L['adm_copyrightsfrom'] = 'Set the same rights as the group';
$L['adm_rights_maintenance'] = 'Access to site when maintenance mode on';
$L['adm_skiprights'] = 'Omit rights for this group';
$L['adm_group_has_no_rights'] = 'Group has no rights';
$L['adm_groups_name_empty'] = 'Group name must not be empty';
$L['adm_groups_title_empty'] = 'Group member title must not be empty';
$L['users_grp_5_title'] = 'Administrators';
$L['users_grp_5_desc'] = 'Superusers and website administrators with max authority';
$L['users_grp_6_title'] = 'Moderators';
$L['users_grp_6_desc'] = 'Content managers and trusted contributors';
$L['users_grp_4_title'] = 'Members';
$L['users_grp_4_desc'] = 'Registered users with basic rights';
$L['users_grp_3_title'] = 'Banned';
$L['users_grp_3_desc'] = 'User acounts that have been suspended for improper activities';
$L['users_grp_2_title'] = 'Inactive';
$L['users_grp_2_desc'] = 'User accounts for which registration has not been completed';
$L['users_grp_1_title'] = 'Guests';
$L['users_grp_1_desc'] = 'Unregisted visitors or logged out users';

/**
 * Plug Section
 */
$L['adm_defauth_guests'] = 'Default rights for the guests';
$L['adm_deflock_guests'] = 'Lock mask for the guests';
$L['adm_defauth_members'] = 'Default rights for the members';
$L['adm_deflock_members'] = 'Lock mask for the members';

$L['adm_present'] = 'Present';
$L['adm_missing'] = 'Missing';
$L['adm_paused'] = 'Paused';
$L['adm_running'] = 'Running';
$L['adm_partrunning'] = 'Partially running';
$L['adm_partstopped'] = 'Partially stopped';
$L['adm_installed'] = 'Installed';
$L['adm_notinstalled'] = 'Not installed';

$L['adm_plugsetup'] = 'Plugin Setup';
$L['adm_override_guests'] = 'System override, guests and inactive are not allowed to admin';
$L['adm_override_banned'] = 'System override, Banned';
$L['adm_override_admins'] = 'System override, Administrators';

$L['adm_opt_install'] = 'Install';
$L['adm_opt_install_explain'] = 'This will make a new install of this extension';
$L['adm_opt_pause'] = 'Pause';
$L['adm_opt_pauseall'] = 'Pause all';
$L['adm_opt_pauseall_explain'] = 'This will pause (disable) all the plugin parts.';
$L['adm_opt_update'] = 'Update';
$L['adm_opt_update_explain'] = 'This will update extension configuration and data if extension files on disk have been updated already';
$L['adm_opt_uninstall'] = 'Un-install';
$L['adm_opt_uninstall_explain'] = 'This will disable all the parts of the extension and remove all of its data and configuration, but won\'t physically remove the files.';
$L['adm_opt_unpause'] = 'Un-pause';
$L['adm_opt_unpauseall'] = 'Un-pause all';
$L['adm_opt_unpauseall_explain'] = 'This will un-pause (enable) all the plugin parts.';

$L['adm_opt_setup_missing'] = 'Error: setup file missing!';

$L['adm_sort_alphabet'] = 'Alphabetical';
$L['adm_sort_category'] = 'Category View';

$L['adm_only_installed'] = 'Installed';

$L['adm_hook_changed'] = 'Warning! This file is not properly registered in DB or was changed after the installation.<br />';
$L['adm_hook_notregistered'] = ' — Hook(s): <b>{$hooks}</b> not registered<br />';
$L['adm_hook_notfound'] = ' — Hook(s): <b>{$hooks}</b> registered but not found in file<br />';
$L['adm_hook_filenotfound'] = ' — File: <b>{$file}</b> not found!<br />';
$L['adm_hook_updatenote'] = 'Please update plugin with «<b>update</b>» button above.';

/**
 * Tools Section
 */
$L['adm_listisempty'] = 'List is empty';

/**
 * Other Section
 * Cache Subsection
 */
$L['adm_delcacheitem'] = 'Cache item removed';
$L['adm_internalcache'] = 'Internal cache';
$L['adm_purgeall_done'] = 'Cache cleared completely';
$L['adm_diskcache'] = 'Disk cache';
$L['adm_cache_showall'] = 'Show all';

/**
 * Other Section
 * Log Subsection
 */
$L['adm_log'] = 'System log';
$L['adm_log_desc'] = 'Information on the website user activities';
$L['adm_infos'] = 'Information';
$L['adm_infos_desc'] = 'PHP/Zend and OS versions, server time zone info';
$L['adm_versiondclocks'] = 'Versions and clocks';
$L['adm_checkcorethemes'] = 'Check core files and themes';
$L['adm_checkcorenow'] = 'Check core files now!';
$L['adm_checkingcore'] = 'Checking core files...';
$L['adm_checkthemes'] = 'Check if all files are present in themes';
$L['adm_checktheme'] = 'Check TPL files for the theme';
$L['adm_checkingtheme'] = 'Checking the theme...';
$L['adm_check_ok'] = 'Ok';
$L['adm_check_missing'] = 'Missing';
$L['adm_ref_prune'] = 'Cleaned';

/**
 * Other Section
 * Infos Subsection
 */
$L['adm_phpver'] = 'PHP engine version';
$L['adm_zendver'] = 'Zend engine version';
$L['adm_interface'] = 'Interface between webserver and PHP';
$L['adm_cachedrivers'] = 'Cache drivers';
$L['adm_os'] = 'Operating system';
$L['adm_clocks'] = 'Clocks';
$L['adm_time1'] = '#1: Raw server time';
$L['adm_time2'] = '#2: GMT time returned by the server';
$L['adm_time3'] = '#3: GMT time + server offset (Cotonti reference)';
$L['adm_time4'] = '#4: Your local time, adjusted from your profile';
$L['adm_help_versions'] = "Adjust the server time zone to have clock #3 properly set.<br />\nClock #4 depends on the time zone setting in your profile.<br />\nClocks #1 and #2 are being ignored by Cotonti.";

/**
 * Common Entries
 */
$L['adm_area'] = 'Area';
$L['adm_clicktoedit'] = '(Click to edit)';
$L['adm_confirm'] = 'Press this button to confirm: ';
$L['adm_done'] = 'Done';
$L['adm_failed'] = 'Failed';
$L['adm_from'] = 'From';
$L['adm_more'] = 'More tools...';
$L['adm_purgeall'] = 'Purge all';
$L['adm_queue_unvalidated'] = 'Unvalidated';
$L['adm_queue_validated'] = 'Validated';
$L['adm_required'] = '(Required)';
$L['adm_setby'] = 'Set by';
$L['adm_to'] = 'To';
$L['adm_totalsize'] = 'Total size';
$L['adm_warnings'] = 'Warnings';

$L['editdeleteentries'] = 'Edit or delete entries';
$L['viewdeleteentries'] = 'View or delete entries';

$L['alreadyaddnewentry'] = 'New entry added';
$L['alreadyupdatednewentry'] = 'Entry updated';
$L['alreadydeletednewentry'] = 'Entry deleted';

$L['adm_invalid_input'] = "Invalid value '{$value}' for variable '{$field_name}'";
$L['adm_set_default'] = 'Default value used';
$L['adm_max'] = "maximum allowed '{$value}'";
$L['adm_min'] = "minimum allowed '{$value}'";
$L['adm_set'] = 'Using ';
$L['adm_partially_updated'] = 'Not all values updated';
$L['adm_already_updated'] = 'Already updated';

/**
 * Extra Fields (Common Entries for Pages & Structure & Users)
 */
$L['adm_extrafields'] = 'Extra fields';
$L['adm_extrafield_added'] = 'Successfully added new extra field.';
$L['adm_extrafield_not_added'] = 'Error! New extra field not added.';
$L['adm_extrafield_updated'] = 'Successfully updated extra field \'%1$s\'.';
$L['adm_extrafield_not_updated'] = 'Error! Extra field \'%1$s\' not updated.';
$L['adm_extrafield_removed'] = 'Successfully removed extra field.';
$L['adm_extrafield_not_removed'] = 'Error! Extra field not deleted.';
$L['adm_extrafield_confirmdel'] = 'Really delete this extra field? All data in this field will be lost!';
$L['adm_extrafield_confirmupd'] = 'Really update this extra field? Some data in this field may be lost!';
$L['adm_extrafield_default'] = 'Default value';
$L['adm_extrafield_required'] = 'Required field';
$L['adm_extrafield_parse'] = 'Parser';
$L['adm_extrafield_enable'] = 'Enable field';
$L['adm_extrafield_params'] = 'Field parameters';

$L['extf_Name'] = 'Name';
$L['extf_Type'] = 'Type of field';
$L['extf_Base_HTML'] = 'Base HTML';
$L['extf_Page_tags'] = 'Tags';
$L['extf_Description'] = 'Description (_TITLE)';

$L['adm_extrafield_new'] = 'New extra field';
$L['adm_extrafield_noalter'] = 'Do not add actual field in DB, just register it as extra';
$L['adm_extrafield_selectable_values'] = 'Options for select, radio and checklistbox (comma sep.)';
$L['adm_help_extrafield'] = 'Hint: Field &quot;Base HTML&quot; is set to default automatically if you leave it blank and press Update.';

/**
 * Help messages that still don't work
 */
$L['adm_help_cache'] = 'Not available';
$L['adm_help_check1'] = 'Not available';
$L['adm_help_check2'] = 'Not available';
$L['adm_help_config']= 'Not available';
