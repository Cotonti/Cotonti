<?php

/**
 * English Language File for the Admin Module (admin.en.lang.php)
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Cotonti Translators Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Common words
 */

$L['Extension'] = 'Extension';
$L['Extensions'] = 'Extensions';

/**
 * Home Section
 */

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

$L['home_rev_title'] = 'Revision';
$L['home_rev'] = 'r';
$L['home_update_notice'] = 'Update Avaliable';
$L['home_update_revision'] = 'Current Version: <span style="color:#C00;font-weight:bold;">%1$s(r%2$s)</span><br />New Version: <span style="color:#4E9A06;font-weight:bold;">%3$s(r%4$s)</span>'; // %1/%2 Current Version/Revision %3/%4 Updated Version/Revision

/**
 * Config Section
 */

$L['core_forums'] = &$L['Forums'];
$L['core_lang'] = &$L['Language'];
$L['core_main'] = 'Main Settings';
$L['core_menus'] = &$L['Menus'];
$L['core_page'] = &$L['Pages'];
$L['core_parser'] = &$L['Parser'];
$L['core_performance'] = 'Performance'; // New in 0.7.0
$L['core_pfs'] = &$L['PFS'];
$L['core_plug'] = &$L['Plugins'];
$L['core_pm'] = &$L['Private_Messages'];
$L['core_polls'] = &$L['Polls'];
$L['core_rss'] = &$L['RSS_Feeds'];// New in 0.7.0
$L['core_structure'] = &$L['Categories'];// New in 0.7.0
$L['core_theme'] = &$L['Themes'];
$L['core_time'] = 'Time and Date';
$L['core_title'] = 'Titles (&lt;title&gt; tag)';
$L['core_users'] = &$L['Users'];

$L['cfg_struct_defaults'] = 'Structure Defaults';

/**
 * Config Section
 * Lang Subsection
 */

$L['cfg_forcedefaultlang'] = array('Force the default language for all users', '');

/**
 * Config Section
 * Main Subsection
 */

$L['cfg_adminemail'] = array('Administrator\'s e-mail', 'Required');
$L['cfg_clustermode'] = array('Cluster of servers', 'Set to yes if it\'s a load balanced setup.');
$L['cfg_cookiedomain'] = array('Domain for cookies', 'Default: empty');
$L['cfg_cookielifetime'] = array('Maximum cookie lifetime', 'In seconds');
$L['cfg_cookiepath'] = array('Path for cookies', 'Default: empty');
$L['cfg_devmode'] = array('Debugging mode', 'Don\'t let this enabled on live sites');
$L['cfg_hostip'] = array('Server IP', 'The IP of the server, optional.');
$L['cfg_jquery'] = array('Enable jQuery', '');	// New in 0.0.1
$L['cfg_maintenance'] = array('Maintenance mode', 'Let only authorized personel access to site');	// New in 0.0.2
$L['cfg_maintenancereason'] = array('Maintenance reason', 'Optional, should better be short');	// New in 0.0.2
$L['cfg_redirbkonlogin'] = array('Redirect back on login', 'Redirect back to page user viewed before login');	// New in 0.6.1
$L['cfg_redirbkonlogout'] = array('Redirect back on logout', 'Redirect back to page user viewed before logout');	// New in 0.6.1
$L['cfg_shieldenabled'] = array('Enable the Shield', 'Anti-spamming and anti-hammering');
$L['cfg_shieldtadjust'] = array('Adjust Shield timers (in %)', 'The higher, the harder to spam');
$L['cfg_shieldzhammer'] = array('Anti-hammer after * fast hits', 'The smaller, the faster the auto-ban 3 minutes happens');
$L['cfg_turnajax'] = array('Enable Ajax', 'Works only if jQuery is enabled');

/**
 * Config Section
 * Menus Subsection
 */

$L['cfg_banner'] = array('Banner<br />{HEADER_BANNER} in header.tpl', '');
$L['cfg_bottomline'] = array('Bottom line<br />{FOOTER_BOTTOMLINE} in footer.tpl', '');
$L['cfg_topline'] = array('Top line<br />{HEADER_TOPLINE} in header.tpl', '');

$L['cfg_freetext1'] = array('Freetext Slot #1<br />{PHP.cfg.freetext1} in all tpl files', '');
$L['cfg_freetext2'] = array('Freetext Slot #2<br />{PHP.cfg.freetext2} in all tpl files', '');
$L['cfg_freetext3'] = array('Freetext Slot #3<br />{PHP.cfg.freetext3} in all tpl files', '');
$L['cfg_freetext4'] = array('Freetext Slot #4<br />{PHP.cfg.freetext4} in all tpl files', '');
$L['cfg_freetext5'] = array('Freetext Slot #5<br />{PHP.cfg.freetext5} in all tpl files', '');
$L['cfg_freetext6'] = array('Freetext Slot #6<br />{PHP.cfg.freetext6} in all tpl files', '');
$L['cfg_freetext7'] = array('Freetext Slot #7<br />{PHP.cfg.freetext7} in all tpl files', '');
$L['cfg_freetext8'] = array('Freetext Slot #8<br />{PHP.cfg.freetext8} in all tpl files', '');
$L['cfg_freetext9'] = array('Freetext Slot #9<br />{PHP.cfg.freetext9} in all tpl files', '');

$L['cfg_menu1'] = array('Menu slot #1<br />{PHP.cfg.menu1} in all tpl files', '');
$L['cfg_menu2'] = array('Menu slot #2<br />{PHP.cfg.menu2} in all tpl files', '');
$L['cfg_menu3'] = array('Menu slot #3<br />{PHP.cfg.menu3} in all tpl files', '');
$L['cfg_menu4'] = array('Menu slot #4<br />{PHP.cfg.menu4} in all tpl files', '');
$L['cfg_menu5'] = array('Menu slot #5<br />{PHP.cfg.menu5} in all tpl files', '');
$L['cfg_menu6'] = array('Menu slot #6<br />{PHP.cfg.menu6} in all tpl files', '');
$L['cfg_menu7'] = array('Menu slot #7<br />{PHP.cfg.menu7} in all tpl files', '');
$L['cfg_menu8'] = array('Menu slot #8<br />{PHP.cfg.menu8} in all tpl files', '');
$L['cfg_menu9'] = array('Menu slot #9<br />{PHP.cfg.menu9} in all tpl files', '');

/**
 * Config Section
 * Performance Subsection
 */

$L['cfg_cache_forums'] = array('Page cache in forums', 'Caches entire pages output for guests'); // New in 0.7.0
$L['cfg_cache_index'] = array('Page cache on index', 'Index will be static for all guests'); // New in 0.7.0
$L['cfg_cache_page'] = array('Page cache in page and list', 'Caches entire pages for guests'); // New in 0.7.0
$L['cfg_gzip'] = array('Gzip', 'Gzip compression of the HTML output');
$L['cfg_headrc_consolidate'] = array('Consolidate &lt;head&gt; resources (JS/CSS)');
$L['cfg_headrc_minify'] = array('Minify consolidated JS/CSS');
$L['cfg_jquery_cdn'] = array('Use jQuery from Google Ajax APIs CDN');
$L['cfg_shared_drv'] = array('Shared memory cache driver', '(go to Other - Cache)'); // New in 0.7.0
$L['cfg_theme_consolidate'] = array('Include theme CSS in consolidated CSS', 'Works only if the default theme is forced for all users');

/**
 * Config Section
 * Plugins Subsection
 */

$L['cfg_disable_plug'] = array('Disable the plugins', '');

/**
 * Config Section
 * Themes Subsection
 */

$L['cfg_charset'] = array('HTML charset', '');
$L['cfg_disablesysinfos'] = array('Turn off page creation time', 'In footer.tpl');
$L['cfg_doctypeid'] = array('Document Type', '&lt;!DOCTYPE&gt; of the HTML layout');
$L['cfg_forcedefaulttheme'] = array('Force the default theme for all users', '');
$L['cfg_homebreadcrumb'] = array('Show Home in breadcrumb', 'Put the link to the main page in the beginning of breadcrumb');	// New in 0.0.2
$L['cfg_keepcrbottom'] = array('Keep the copyright notice in the tag {FOOTER_BOTTOMLINE}', 'In footer.tpl');
$L['cfg_metakeywords'] = array('HTML Meta keywords (comma separated)', 'Search engines');
$L['cfg_msg_separate'] = array('Display messages separately for each source', '');
$L['cfg_separator'] = array('Generic separator', 'Default:>');
$L['cfg_showsqlstats'] = array('Show SQL queries statistics', 'In footer.tpl');

/**
 * Config Section
 * Time Subsection
 */

$L['cfg_dateformat'] = array('Main date mask', 'Default: Y-m-d H:i');
$L['cfg_formatmonthday'] = array('Short date mask', 'Default: m-d');
$L['cfg_formatyearmonthday'] = array('Medium date mask', 'Default: Y-m-d');
$L['cfg_formatmonthdayhourmin'] = array('Forum date mask', 'Default: m-d H:i');
$L['cfg_servertimezone'] = array('Server time zone', 'Offset of the server from the GMT+00');
$L['cfg_defaulttimezone'] = array('Default time zone', 'For guests and new members, from -12 to +12');
$L['cfg_timedout'] = array('Idle delay, in seconds', 'After this delay, user is away');

/**
 * Config Section
 * Title Subsection
 */

$L['cfg_maintitle'] = array('Site title', 'Main title for the website, required');
$L['cfg_subtitle'] = array('Description', 'Optional, will be displayed after the title of the site');
$L['cfg_title_forum_editpost'] = array('Forum EditPost title', 'Options: {FORUM}, {SECTION}, {EDIT}');
$L['cfg_title_forum_main'] = array('Forum Main title', 'Options: {FORUM}');
$L['cfg_title_forum_newtopic'] = array('Forum NewTopic title', 'Options: {FORUM}, {SECTION}, {NEWTOPIC}');
$L['cfg_title_forum_posts'] = array('Forum Posts title', 'Options: {FORUM}, {SECTION}, {TITLE}');
$L['cfg_title_forum_topics'] = array('Forum Topics title', 'Options: {FORUM}, {SECTION}');
$L['cfg_title_header'] = array('Header title', 'Options: {MAINTITLE}, {DESCRIPTION}, {SUBTITLE}');
$L['cfg_title_header_index'] = array('Header Index title', 'Options: {MAINTITLE}, {DESCRIPTION}, {SUBTITLE}');
$L['cfg_title_list'] = array('List title', 'Options: {TITLE}');
$L['cfg_title_page'] = array('Page title', 'Options: {TITLE}, {CATEGORY}');
$L['cfg_title_pfs'] = array($L['PFS'].' title', 'Options: {PFS}');
$L['cfg_title_pm_main'] = array('PM title', 'Options: {PM}, {INBOX}, {ARCHIVES}, {SENTBOX}');
$L['cfg_title_pm_send'] = array('PM Send title', 'Options: {PM}, {SEND_NEW}');
$L['cfg_title_users_details'] = array('Users Details title', 'Options: {USER}, {NAME}');
$L['cfg_title_users_edit'] = array('Users Edit title', 'Options: {EDIT}, {NAME}');
$L['cfg_title_users_main'] = array('Users Main title', 'Options: {USERS}');
$L['cfg_title_users_profile'] = array('Users Profile title', 'Options: {PROFILE}, {NAME}');
$L['cfg_title_users_pasrec'] = array('Users - password recovery', 'Options: {PASSRECOVER}');

/**
 * Config Section
 * Users Subsection
 */

$L['cfg_disablereg'] = array('Disable registration process', 'Prevent users from registering new accounts');
$L['cfg_disablewhosonline'] = array('Disable who\'s online', 'Automatically enabled if you turn on the Shield');
$L['cfg_forcerememberme'] = array('Force &quot;remember me&quot;', 'Use it on multi-domain sites or if there are sudden logouts');
$L['cfg_maxusersperpage'] = array('Maximum lines in userlist', '');
$L['cfg_regnoactivation'] = array('Skip e-mail check for new users', '\'No\'recommended, for security reasons');
$L['cfg_regrequireadmin'] = array('Administrators must validate new accounts', '');
$L['cfg_user_email_noprotection'] = array('Disable password protection of e-mail change', '\'No\' recommended, for security reasons');
$L['cfg_useremailchange'] = array('Allow users to change their e-mail address', '\'No\' recommended, for security reasons');
$L['cfg_usertextimg'] = array('Allow images and HTML in user signature', '\'No\' recommended, for security reasons');
$L['cfg_usertextmax'] = array('Maximum length for user signature', 'Default: 300 chars');

/**
 * Config Section
 * Common strings
 */
$L['cfg_markup'] = array('Enable markup', 'Enables HTML/BBcode or other parsing which is installed in your system');

/**
 * Extension management
 */

$L['ext_already_installed'] = 'This extension is already installed';
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
$L['ext_executed_php'] = 'Executed PHP handler part: {$ret}';
$L['ext_executed_sql'] = 'Executed SQL handler part: {$ret}';
$L['ext_installing'] = 'Installing {$type} &quot;{$name}&quot;';
$L['ext_invalid_format'] = 'This is not a valid Cotonti >= 0.9 extension. Please contact the developer';
$L['ext_patch_applied'] = 'Applied patch {$f}: {$msg}';
$L['ext_patch_error'] = 'Error applying patch {$f}: {$msg}';
$L['ext_setup_not_found'] = 'Setup file is not found';
$L['ext_uninstalling'] = 'Uninstalling {$type} &quot;{$name}&quot;';
$L['ext_up2date'] = '{$type} &quot;{$name}&quot; is up to date';
$L['ext_update_error'] = 'Failed updating {$type} &quot;{$name}&quot;';
$L['ext_updated'] = '{$type} &quot;{$name}&quot; has been updated to version {$ver}';
$L['ext_updating'] = 'Updating {$type} &quot;{$name}&quot;';

/**
  * Structure Section
 */

$L['adm_tpl_mode'] = 'Template mode';
$L['adm_tpl_empty'] = 'Default';
$L['adm_tpl_forced'] = 'Same as';
$L['adm_tpl_parent'] = 'Same as the parent category';
$L['adm_tpl_resyncalltitle'] = 'Resync all page counters';
$L['adm_tpl_resynctitle'] = 'Resync category page counters';
$L['adm_help_structure'] = 'The pages that belong to the category &quot;system&quot; are not displayed in the public listings, it\'s to make standalone pages.'; // Added in N-0.7.0

/**
 * Structure Section
 * Extrafields Subsection
 */

$L['adm_help_structure_extrafield'] = '<b>Base HTML</b> set automaticaly if you leave it blank<br /><br />
<b>New tags in tpl files:</b><br /><br />
<u>list.tpl:</u><br /><br />
&nbsp;&nbsp;&nbsp;{LIST_XXXXX}, {LIST_XXXXX_TITLE}<br /><br />
<u>list.group.tpl:</u><br /><br />
&nbsp;&nbsp;&nbsp;{LIST_XXXXX}, {LIST_XXXXX_TITLE}<br /><br />
<u>admin.structure.inc.tpl :</u><br /><br />
&nbsp;&nbsp;&nbsp;&lt;!-- BEGIN: OPTIONS --&gt; {ADMIN_STRUCTURE_XXXXX}, {ADMIN_STRUCTURE_XXXXX_TITLE} &lt;!-- END: OPTIONS --&gt;<br /><br />
&nbsp;&nbsp;&nbsp;&lt;!-- BEGIN: DEFULT --&gt; {ADMIN_STRUCTURE_FORMADD_XXXXX}, {ADMIN_STRUCTURE_FORMADD_XXXXX_TITLE} &lt;!-- END: DEFULT --&gt;<br /><br />
<br />';

/**
 * Users Section
 */

$L['adm_rightspergroup'] = 'Rights per group';
$L['adm_maxsizesingle'] = 'Max size for a single file in '.$L['PFS'].' (KB)';
$L['adm_maxsizeallpfs'] = 'Max size of all files together in '.$L['PFS'].' (KB)';
$L['adm_copyrightsfrom'] = 'Set the same rights as the group';
$L['adm_rights_maintenance'] = 'Access to site when maintenance mode on';	// New in 0.0.2

/**
 * Users Section
 * Extrafields Subsection
 */

$L['adm_help_users_extrafield'] = '<b>Base HTML</b> set automaticaly if you leave it blank<br /><br />
<b>New tags in tpl files:</b><br /><br />
users.profile.tpl: {USERS_PROFILE_XXXXX}, {USERS_PROFILE_XXXXX_TITLE}<br /><br />
users.edit.tpl: {USERS_EDIT_XXXXX}, {USERS_EDIT_XXXXX_TITLE}<br /><br />
users.details.tpl: {USERS_DETAILS_XXXXX}, {USERS_DETAILS_XXXXX_TITLE}<br /><br />
user.register.tpl: {USERS_REGISTER_XXXXX}, {USERS_REGISTER_XXXXX_TITLE}<br /><br />
forums.posts.tpl: {FORUMS_POSTS_ROW_USERXXXXX}, {FORUMS_POSTS_ROW_USERXXXXX_TITLE}<br />';

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
$L['adm_installed'] = 'Installed';	// New in 0.0.6
$L['adm_notinstalled'] = 'Not installed';

$L['adm_plugsetup'] = 'Plugin Setup';	// New in 0.0.6
$L['adm_override_guests'] = 'System override, guests and inactive are not allowed to admin';	// New in 0.0.6
$L['adm_override_banned'] = 'System override, Banned';	// New in 0.0.6
$L['adm_override_admins'] = 'System override, Administrators';	// New in 0.0.6

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

$L['adm_opt_setup_missing'] = 'Error: setup file missing!';	// New in 0.0.6

/**
 * Tools Section
 */

$L['adm_listisempty'] = 'List is empty';

/**
 * Other Section
 * Cache Subsection
 */

$L['adm_delcacheitem'] = 'Cache item removed';	// New in 0.0.2
$L['adm_internalcache'] = 'Internal cache';
$L['adm_purgeall_done'] = 'Cache cleared completely';	// New in 0.0.2
$L['adm_diskcache'] = 'Disk cache';	// New in 0.6.1

/**
 * Other Section
 * Log Subsection
 */

$L['adm_log'] = 'System log';
$L['adm_infos'] = 'Informations';
$L['adm_versiondclocks'] = 'Versions and clocks';
$L['adm_checkcorethemes'] = 'Check core files and themes';
$L['adm_checkcorenow'] = 'Check core files now!';
$L['adm_checkingcore'] = 'Checking core files...';
$L['adm_checkthemes'] = 'Check if all files are present in themes';
$L['adm_checktheme'] = 'Check TPL files for the theme';
$L['adm_checkingtheme'] = 'Checking the theme...';
$L['adm_check_ok'] = 'Ok';
$L['adm_check_missing'] = 'Missing';

/**
 * Other Section
 * Infos Subsection
 */

$L['adm_phpver'] = 'PHP engine version';
$L['adm_zendver'] = 'Zend engine version';
$L['adm_interface'] = 'Interface between webserver and PHP';
$L['adm_os'] = 'Operating system';
$L['adm_clocks'] = 'Clocks';
$L['adm_time1'] = '#1: Raw server time';
$L['adm_time2'] = '#2: GMT time returned by the server';
$L['adm_time3'] = '#3: GMT time + server offset (Cotonti reference)';
$L['adm_time4'] = '#4: Your local time, adjusted from your profile';
$L['adm_help_versions'] = 'Adjust the Server time zone to have the clock #3 properlly set.<br />
Clock #4 depends of the timezone setting in your profile.<br />
Clocks #1 and #2 are ignored by Cotonti.';

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
$L['adm_queue_unvalidated'] = 'Unvalidated';	// New in 0.0.3
$L['adm_queue_validated'] = 'Validated';	// New in 0.0.3
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
$L['adm_extrafield_parse'] = 'Parse';

$L['extf_Name'] = 'Name';
$L['extf_Type'] = 'Type of field';
$L['extf_Base_HTML'] = 'Base HTML';
$L['extf_Page_tags'] = 'Tags';
$L['extf_Description'] = 'Description (_TITLE)';

$L['adm_extrafield_new'] = 'New extra field';
$L['adm_extrafield_noalter'] = 'Do not add actual field in DB, just register it as extra';
$L['adm_extrafield_selectable_values'] = 'Options for select and radio (comma sep.)';
$L['adm_help_extrafield'] = 'Hint: Field &quot;Base HTML&quot; is set to default automatically if you leave it blank and press Update.';

/**
 * Help messages that still don't work
 */

$L['adm_help_cache'] = 'Not available';
$L['adm_help_check1'] = 'Not available';
$L['adm_help_check2'] = 'Not available';
$L['adm_help_config']= 'Not available';

?>