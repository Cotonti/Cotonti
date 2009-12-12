<?php
/**
 * English Language File for Admin Area (admin.lang.php)
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Cotonti Translators Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL.');

/**
 * Home Section
 */

$L['home_hitsmonth'] = 'Hits for the past 15 days';
$L['home_pastdays'] = 'Activity for the past 7 days';

$L['home_newusers'] = 'New members';
$L['home_newpages'] = 'New pages';
$L['home_newtopics'] = 'New topics';
$L['home_newposts'] = 'New posts';
$L['home_newcomments'] = 'New comments';
$L['home_newpms'] = 'New private messages';

$L['home_db_rows'] = 'SQL database, number of rows';
$L['home_db_indexsize'] = 'SQL database, index size (KB)';
$L['home_db_datassize'] = 'SQL database, datas size (KB)';
$L['home_db_totalsize'] = 'SQL database, total size (KB)';

$L['home_ql_b1_title'] = 'Site properties';
$L['home_ql_b1_1'] = 'Basic config';
$L['home_ql_b1_2'] = 'Titles';
$L['home_ql_b1_3'] = 'Skin and charset';
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

$L['core_email'] = 'E-mail Settings';// New in N-0.7.0
$L['core_comments'] = &$L['Comments'];
$L['core_forums'] = &$L['Forums'];
$L['core_lang'] = &$L['Language'];
$L['core_main'] = 'Main Settings';
$L['core_menus'] = &$L['Menus'];
$L['core_page'] = &$L['Pages'];
$L['core_parser'] = &$L['Parser'];
$L['core_pfs'] = &$L['PFS'];
$L['core_plug'] = &$L['Plugins'];
$L['core_pm'] = &$L['Private_Messages'];
$L['core_polls'] = &$L['Polls'];
$L['core_ratings'] = &$L['Ratings'];
$L['core_rss'] = &$L['Rss_feeds'];// New in N-0.7.0
$L['core_skin'] = &$L['Skins'];
$L['core_structure'] = &$L['Categories'];// New in N-0.7.0
$L['core_time'] = 'Time and Date';
$L['core_title'] = 'Titles (&lt;title&gt; tag)';
$L['core_trash'] = &$L['Trashcan'];
$L['core_users'] = &$L['Users'];

/**
 * Config Section
 * E-mail Subsection
 */

$L['cfg_email_type'] = array('Type of sending E-mail', ''); // New in N-0.7.0
$L['cfg_smtp_address'] = array('Address smtp server', 'Set it if the type of sending E-mail selected smtp'); // New in N-0.7.0
$L['cfg_smtp_port'] = array('Port smtp server', 'Set it if the type of sending E-mail selected smtp'); // New in N-0.7.0
$L['cfg_smtp_login'] = array('Login', 'Set it if the type of sending E-mail selected smtp'); // New in N-0.7.0
$L['cfg_smtp_password'] = array('Password', 'Set it if the type of sending E-mail selected smtp'); // New in N-0.7.0
$L['cfg_smtp_uses_ssl'] = array('Use SSL', 'Set it if the type of sending E-mail selected smtp'); // New in N-0.7.0

/**
 * Config Section
 * Comments Subsection
 */

$L['cfg_countcomments'] = array('Count comments', 'Display the count of comments near the icon');
$L['cfg_disable_comments'] = array('Disable the comments', '');
$L['cfg_expand_comments'] = array('Expand comments', 'Show comments expanded by default');	// New in N-0.0.2
$L['cfg_maxcommentsperpage'] = array('Max. comments on page', ' ');   // New in N-0.0.6
$L['cfg_commentsize'] = array('Max. size of comment', 'In bytes (zero for unlimited size). Default: 0');   // New in N-0.0.6

/**
 * Config Section
 * Forums Subsection
 */

$L['cfg_antibumpforums'] = array('Anti-bump protection', 'Will prevent users from posting twice in a row in the same topic');	// New in N-0.1.0
$L['cfg_disable_forums'] = array('Disable the forums', '');
$L['cfg_hideprivateforums'] = array('Hide private forums', '');
$L['cfg_hottopictrigger'] = array('Posts for a topic to be \'hot\'', '');
$L['cfg_maxtopicsperpage'] = array('Maximum topics per page', '');
$L['cfg_mergeforumposts'] = array('Post merge feature', 'Will merge user\'s posts if they are sent consecutively, anti-bump must be off');	// New in N-0.1.0
$L['cfg_mergetimeout'] = array('Post merge timeout', 'Will not merge user\'s posts if they are sent consecutively after the timeout (In hours), post merge must be on (Zero to disable this feature)');	// New in N-0.1.0
$L['cfg_maxpostsperpage'] = array('Max. posts per page', ' '); // New in N-0.0.6

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
$L['cfg_turnajax'] = array('Turn Ajax off', 'Works only if jQuery is enabled');
$L['cfg_cache'] = array('Internal cache', 'Keep it enabled for better performance');
$L['cfg_clustermode'] = array('Cluster of servers', 'Set to yes if it\'s a load balanced setup.');
$L['cfg_cookiedomain'] = array('Domain for cookies', 'Default: empty');
$L['cfg_cookielifetime'] = array('Maximum cookie lifetime', 'In seconds');
$L['cfg_cookiepath'] = array('Path for cookies', 'Default: empty');
$L['cfg_devmode'] = array('Debugging mode', 'Don\'t let this enabled on live sites');
$L['cfg_disablehitstats'] = array('Disable hit statistics', 'Referers and hits per day');
$L['cfg_disableactivitystats'] = array('Do not display statistics of activity', 'Activity for the last 7 days<br />Displayed on the home page administration panel');
$L['cfg_disabledbstats'] = array('Do not display database statistics', 'Displayed on the home page administration panel');
$L['cfg_gzip'] = array('Gzip', 'Gzip compression of the HTML output');
$L['cfg_hostip'] = array('Server IP', 'The IP of the server, optional.');
$L['cfg_jquery'] = array('Enable jQuery', '');	// New in N-0.0.1
$L['cfg_maintenance'] = array('Maintenance mode', 'Let only authorized personel access to site');	// New in N-0.0.2
$L['cfg_maintenancereason'] = array('Maintenance reason', 'Optional, should better be short');	// New in N-0.0.2
$L['cfg_shieldenabled'] = array('Enable the Shield', 'Anti-spamming and anti-hammering');
$L['cfg_shieldtadjust'] = array('Adjust Shield timers (in %)', 'The higher, the harder to spam');
$L['cfg_shieldzhammer'] = array('Anti-hammer after * fast hits', 'The smaller, the faster the auto-ban 3 minutes happens');
$L['cfg_redirbkonlogin'] = array('Redirect back on login', 'Redirect back to page user viewed before login');	// New in N-0.6.1
$L['cfg_redirbkonlogout'] = array('Redirect back on logout', 'Redirect back to page user viewed before logout');	// New in N-0.6.1

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
 * Page Subsection
 */

$L['cfg_allowphp_pages'] = array('Allow the PHP page type', 'Execution of PHP code in pages, use with caution!');
$L['cfg_autovalidate'] = array('Autovalidate page', 'Autovalidate page if poster have admin rights for page category');	// New in N-0.0.2
$L['cfg_count_admin'] = array('Count Administrators\' hits', '');	// New in N-0.0.1
$L['cfg_disable_page'] = array('Disable the pages', '');
$L['cfg_maxrowsperpage'] = array('Max. lines in lists', ' ');
$L['cfg_maxlistsperpage'] = array('Max. lists per page', ' '); // New in N-0.0.6

/**
 * Config Section
 * Parser Subsection
 */

$L['cfg_parsebbcodecom'] = array('Parse BBcode in comments and private messages', '');
$L['cfg_parsebbcodeforums'] = array('Parse BBcode in forums', '');
$L['cfg_parsebbcodepages'] = array('Parse BBcode in pages', '');
$L['cfg_parsebbcodeusertext'] = array('Parse BBcode in user signature', '');
$L['cfg_parser_cache'] = array('Enable HTML cache', '');	// New in N-0.0.1
$L['cfg_parser_custom'] = array('Enable custom parser', '');	// New in N-0.0.1
$L['cfg_parser_disable'] = array('Disable default parser', '');	// New in N-0.0.3
$L['cfg_parsesmiliescom'] = array('Parse smilies in comments and private messages', '');
$L['cfg_parsesmiliesforums'] = array('Parse smilies in forums', '');
$L['cfg_parsesmiliespages'] = array('Parse smilies in pages', '');
$L['cfg_parsesmiliesusertext'] = array('Parse smilies in user signature', '');

/**
 * Config Section
 * PFS Subsection
 */

$L['cfg_disable_pfs'] = array('Disable the '.$L['PFS'], '');
$L['cfg_maxpfsperpage'] = array('Max. elements on page', ' ');
$L['cfg_pfsfilecheck'] = array('File Check', 'If Enabled will check any uploaded files through the '.$L['PFS'].', or images through the profile. To insure they are valid files. &quot;Yes&quot; recommended, for security reasons.');	// New in N-0.0.2
$L['cfg_pfsnomimepass'] = array('No Mimetype Pass', 'If Enabled will it will allow uploaded files to pass even if there is no mimetype in the config file.');	// New in N-0.0.2
$L['cfg_pfstimename'] = array('Time-based filenames', 'Generate filenames based on current time stamp. By default the original file name is used with some necessary character conversions.');	// New in N-0.0.2
$L['cfg_pfsuserfolder'] = array('Folder storage mode', 'If enabled, will store the user files in subfolders /datas/users/USERID/FOLDERNAME/... Must be set at the FIRST setup of the site ONLY. As soon as a file is uploaded, it\'s too late to change this.');
$L['cfg_flashupload'] = array('Use flash uploader', 'Allows uploading many files at once.');	// New in N-1.0.0
$L['cfg_pfs_winclose'] = array('Close popup window after bbcode insertion');
$L['cfg_th_amode'] = array('Thumbnails generation', '');
$L['cfg_th_border'] = array('Thumbnails, border size', 'Default: 4 pixels');
$L['cfg_th_colorbg'] = array('Thumbnails, border color', 'Default: 000000, hex color code');
$L['cfg_th_colortext'] = array('Thumbnails, text color', 'Default: FFFFFF, hex color code');
$L['cfg_th_dimpriority'] = array('Thumbnails, rescaling priority dimension', '');
$L['cfg_th_jpeg_quality'] = array('Thumbnails, Jpeg quality', 'Default: 85');
$L['cfg_th_keepratio'] = array('Thumbnail, keep ratio?', '');
$L['cfg_th_textsize'] = array('Thumbnails, size of the text', '');
$L['cfg_th_x'] = array('Thumbnails, width', 'Default: 112 pixels');
$L['cfg_th_y'] = array('Thumbnails, height', 'Default: 84 pixel, recommended: Width x 0.75');

/**
 * Config Section
 * Plugins Subsection
 */

$L['cfg_disable_plug'] = array('Disable the plugins', '');

/**
 * Config Section
 * Private Messages Subsection
 */

$L['cfg_disable_pm'] = array('Disable the private messages', '');
$L['cfg_pm_allownotifications'] = array('Allow PM notifications by e-mail', '');
$L['cfg_pm_maxsize'] = array('Maximum length for messages', 'Default: 10000 chars');
$L['cfg_maxpmperpage'] = array('Max. messages per page', ' ');  // New in N-0.0.6

/**
 * Config Section
 * Polls Subsection
 */

$L['cfg_del_dup_options'] = array('Force duplicate option removal', ' Remove duplicate options even if it is already in the database');	// New in N-0.0.2
$L['cfg_disable_polls'] = array('Disable the polls', '');
$L['cfg_ip_id_polls'] = array('Vote counting method', '');	// New in N-0.0.2
$L['cfg_max_options_polls'] = array('Max number of options', 'Options above this limit will be automatically removed');	// New in N-0.0.2

/**
 * Config Section
 * Ratings Subsection
 */

$L['cfg_disable_ratings'] = array('Disable the ratings', '');
$L['cfg_ratings_allowchange'] = array('Allow Ratings to be changed?', 'If enabled it will allow users to change their rating.');	// New in N-0.0.2

/**
 * Config Section
 * RSS Subsection
 */

$L['cfg_disable_rss'] = array('Disable the RSS feeds', '');
$L['cfg_rss_timetolive'] = array('Refresh RSS cache every N seconds', ' '); // New in N-0.7.0
$L['cfg_rss_maxitems'] = array('Max. items in RSS feed', ' '); // New in N-0.7.0
$L['cfg_rss_charset'] = array('RSS charset', ' '); // New in N-0.7.0
$L['cfg_rss_pagemaxsymbols'] = array('Pages. Cut element description longer than N symbols', 'Disabled by default'); // New in N-0.7.0
$L['cfg_rss_commentmaxsymbols'] = array('Comments. Cut element description longer than N symbols', 'Disabled by default'); // New in N-0.7.0
$L['cfg_rss_postmaxsymbols'] = array('Posts. Cut element description longer than N symbols', 'Disabled by default'); // New in N-0.7.0


/**
 * Config Section
 * Skins Subsection
 */

$L['cfg_charset'] = array('HTML charset', '');
$L['cfg_disablesysinfos'] = array('Turn off page creation time', 'In footer.tpl');
$L['cfg_doctypeid'] = array('Document Type', '&lt;!DOCTYPE&gt; of the HTML layout');
$L['cfg_forcedefaultskin'] = array('Force the default skin for all users', '');
$L['cfg_homebreadcrumb'] = array('Show Home in breadcrumb', 'Put the link to the main page in the beginning of breadcrumb');	// New in N-0.0.2
$L['cfg_keepcrbottom'] = array('Keep the copyright notice in the tag {FOOTER_BOTTOMLINE}', 'In footer.tpl');
$L['cfg_metakeywords'] = array('HTML Meta keywords (comma separated)', 'Search engines');
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
$L['cfg_title_forum_posts'] = array('Forum Posts title', 'Options: {FORUM}, {TITLE}');
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
 * Trash Subsection
 */

$L['cfg_trash_comment'] = array('Use the trash can for the comments', '');
$L['cfg_trash_forum'] = array('Use the trash can for the forums', '');
$L['cfg_trash_page'] = array('Use the trash can for the pages', '');
$L['cfg_trash_pm'] = array('Use the trash can for the private messages', '');
$L['cfg_trash_prunedelay'] = array('Remove the items from the trash can after * days (Zero to keep forever)', '');
$L['cfg_trash_user'] = array('Use the trash can for the users', '');

/**
 * Config Section
 * Users Subsection
 */

$L['cfg_av_maxsize'] = array('Avatar, maximum file size', 'Default: 8000 bytes');
$L['cfg_av_maxx'] = array('Avatar, maximum width', 'Default: 64 pixels');
$L['cfg_av_maxy'] = array('Avatar, maximum height', 'Default: 64 pixels');
$L['cfg_disablereg'] = array('Disable registration process', 'Prevent users from registering new accounts');
$L['cfg_disablewhosonline'] = array('Disable who\'s online', 'Automatically enabled if you turn on the Shield');
$L['cfg_maxusersperpage'] = array('Maximum lines in userlist', '');
$L['cfg_ph_maxsize'] = array('Photo, maximum file size', 'Default: 8000 bytes');
$L['cfg_ph_maxx'] = array('Photo, maximum width', 'Default: 96 pixels');
$L['cfg_ph_maxy'] = array('Photo, maximum height', 'Default: 96 pixels');
$L['cfg_regnoactivation'] = array('Skip e-mail check for new users', '\'No\'recommended, for security reasons');
$L['cfg_regrequireadmin'] = array('Administrators must validate new accounts', '');
$L['cfg_sig_maxsize'] = array('Signature, maximum file size', 'Default: 50000 bytes');
$L['cfg_sig_maxx'] = array('Signature, maximum width', 'Default: 468 pixels');
$L['cfg_sig_maxy'] = array('Signature, maximum height', 'Default: 60 pixels');
$L['cfg_user_email_noprotection'] = array('Disable password protection of e-mail change', '\'No\' recommended, for security reasons');
$L['cfg_useremailchange'] = array('Allow users to change their e-mail address', '\'No\' recommended, for security reasons');
$L['cfg_usertextimg'] = array('Allow images and HTML in user signature', '\'No\' recommended, for security reasons');
$L['cfg_usertextmax'] = array('Maximum length for user signature', 'Default: 300 chars');

/**
  * Page Section
 */

$L['addnewentry'] = 'Add a new entry';
$L['adm_queue_deleted'] = 'Page was deleted in to trash can';
$L['adm_valqueue'] = 'Waiting for validation';
$L['adm_structure'] = 'Structure of the pages (categories)';
$L['adm_extrafields_desc'] = 'Add/Edit extra fields';
$L['adm_sortingorder'] = 'Set a default sorting order for the categories';
$L['adm_showall'] = 'Show all';
$L['adm_help_page'] = 'The pages that belong to the category &quot;system&quot; are not displayed in the public listings, it\'s to make standalone pages.';
$L['adm_fileyesno'] = 'File (yes/no)';
$L['adm_fileurl'] = 'File URL';
$L['adm_filecount'] = 'File hit count';
$L['adm_filesize'] = 'File size';

/**
 * Page Section
 * Extrafields Subsection
 */

$L['adm_help_pages_extrafield'] = '<b>Base HTML</b> set automaticaly if you leave it blank<br /><br />
<b>New tags in tpl files:</b><br /><br />
page.tpl: {PAGE_XXXXX}, {PAGE_XXXXX_TITLE}<br /><br />
page.add.tpl: {PAGEADD_FORM_XXXXX}, {PAGEADD_FORM_XXXXX_TITLE}<br /><br />
page.edit.tpl: {PAGEEDIT_FORM_XXXXX}, {PAGEEDIT_FORM_XXXXX_TITLE}<br /><br />
list.tpl: {LIST_ROW_XXXXX}, {LIST_TOP_XXXXX}<br />';

/**
  * Structure Section
 */

$L['adm_tpl_mode'] = 'Template mode';
$L['adm_tpl_empty'] = 'Default';
$L['adm_tpl_forced'] = 'Same as';
$L['adm_tpl_parent'] = 'Same as the parent category';
$L['adm_enablecomments'] = 'Enable comments';	// New in N-0.1.0
$L['adm_enableratings'] = 'Enable ratings';	// New in N-0.1.0
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
 * Forums Section
 */

$L['adm_forum_structure'] = 'Structure of the forums (categories)';
$L['adm_forum_emptytitle'] = 'Error: title empty';	// New in N-0.1.0

/**
  * Forums Section
  * Structure Subsection
 */

$L['adm_defstate'] = 'Default state';
$L['adm_defstate_0'] = 'Folded';
$L['adm_defstate_1'] = 'Unfolded';

/**
  * Forums Section
  * Forum Edit Subsection
 */

$L['adm_forums_master'] = 'Master section';	// New in N-0.0.1
$L['adm_diplaysignatures'] = 'Display signatures';
$L['adm_enablebbcodes'] = 'Enable BBcodes';
$L['adm_enablesmilies'] = 'Enable smilies';
$L['adm_enableprvtopics'] = 'Allow private topics';
$L['adm_enableviewers'] = 'Enable Viewers';	// New in N-0.0.2
$L['adm_enablepolls'] = 'Enable Polls';	// New in N-0.0.2
$L['adm_countposts'] = 'Count posts';
$L['adm_autoprune'] = 'Auto-prune topics after * days';
$L['adm_postcounters'] = 'Check the counters';

/**
 * Users Section
 */

$L['adm_rightspergroup'] = 'Rights per group';
$L['adm_maxsizesingle'] = 'Max size for a single file in '.$L['PFS'].' (KB)';
$L['adm_maxsizeallpfs'] = 'Max size of all files together in '.$L['PFS'].' (KB)';
$L['adm_copyrightsfrom'] = 'Set the same rights as the group';
$L['adm_rights_maintenance'] = 'Access to site when maintenance mode on';	// New in N-0.0.2

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
$L['adm_installed'] = 'Installed';	// New in N-0.0.6
$L['adm_notinstalled'] = 'Not installed';

$L['adm_plugsetup'] = 'Plugin Setup';	// New in N-0.0.6
$L['adm_override_guests'] = 'System override, guests and inactive are not allowed to admin';	// New in N-0.0.6
$L['adm_override_banned'] = 'System override, Banned';	// New in N-0.0.6
$L['adm_override_admins'] = 'System override, Administrators';	// New in N-0.0.6

$L['adm_opt_installall'] = 'Install all';
$L['adm_opt_installall_explain'] = 'This will install or reset all the parts of the plugin.';
$L['adm_opt_uninstallall'] = 'Un-install all</a></td>';
$L['adm_opt_uninstallall_explain'] = 'This will disable all the parts of the plugin, but won\'t physically remove the files.';
$L['adm_opt_pauseall'] = 'Pause all';
$L['adm_opt_pauseall_explain'] = 'This will pause (disable) all the parts of the plugin.';
$L['adm_opt_unpauseall'] = 'Un-pause all';
$L['adm_opt_unpauseall_explain'] = 'This will un-pause (enable) all the parts of the plugin.';

$L['adm_opt_setoption_warn'] = 'Options found for this plugin. Would you like to install from kept options?';	// New in N-0.0.2
$L['adm_opt_uninstall_warn'] = 'You can delete this plugin without deleting old settings (rights and options). Click if you want to.';	// New in N-0.0.2
$L['adm_opt_setup_missing'] = 'Error: setup file missing!';	// New in N-0.0.6

$L['adm_pluginstall_msg01'] = 'Configuring plugin...';	// New in N-0.0.6
$L['adm_pluginstall_msg02'] = 'Deleting old configuration entries...';	// New in N-0.0.6
$L['adm_pluginstall_msg03'] = 'Looking for the setup file...';	// New in N-0.0.6
$L['adm_pluginstall_msg04'] = 'Looking for parts...';	// New in N-0.0.6
$L['adm_pluginstall_msg05'] = 'Installing the parts...';	// New in N-0.0.6
$L['adm_pluginstall_msg06'] = 'Looking for configuration entries in the setup file...';	// New in N-0.0.6
$L['adm_pluginstall_msg07'] = 'Not found! Installation failed!';	// New in N-0.0.6
$L['adm_pluginstall_msg08'] = 'Deleting any old rights about this plugin...';	// New in N-0.0.6
$L['adm_pluginstall_msg09'] = 'Adding the rights for the groups of users...';	// New in N-0.0.6
$L['adm_pluginstall_msg10'] = 'Resetting the auth column for all the users...';	// New in N-0.0.6
$L['adm_pluginstall_msg11'] = 'Running on-install part of the setup script...';	// New in N-0.0.6

/**
 * Tools Section
 */

$L['adm_listisempty'] = 'List is empty';

/**
  * TrashCan Section
 */

$L['adm_help_trashcan'] = 'Here are listed the items recently deleted by the users and moderators.<br />
Wipe: Delete the item forever<br />
Restore: Put the item back in the live database<br />
<b>Note</b>:<br />
- restoring a forum topic will also restore all the posts that belongs to the topic<br />
- restoring a post in a deleted topic will restore the whole topic (if available) and all the child posts.<br />';

/**
 * Other Section
 * Comments Subsection
 */

$L['adm_comm_already_del'] = 'Comment deleted';		// New in N-0.0.2

/**
 * Other Section
 * PFS Subsection
 */

$L['adm_gd'] = 'GD graphical library';
$L['adm_allpfs'] = 'All '.$L['PFS'];
$L['adm_allfiles'] = 'All files';
$L['adm_thumbnails'] = 'Thumbnails';
$L['adm_orphandbentries'] = 'Orphan DB entries';
$L['adm_orphanfiles'] = 'Orphan files';
$L['adm_delallthumbs'] = 'Delete all thumbnails';
$L['adm_rebuildallthumbs']= 'Delete and rebuild all thumbnails';
$L['adm_help_allpfs'] = $L['PFS'].' of all registered users';
$L['adm_nogd'] = 'The GD graphical library is not supported by this host, Cotonti won\'t be able to create thumbnails for images. Go for '.$L['Configuration'].' &gt; '.$L['PFS'].' and set &quot;Thumbnails generation&quot; to &quot;'.$L['Disabled'].'&quot;.';

/**
 * Other Section
 * Polls Subsection
 */

$L['adm_help_polls'] = 'Fill in the form and press &quot;Create&quot; button to start a new poll. Empty options will be ignored and removed. It is not recommended to edit the poll after it has been started because it may compromise poll results.';	// New in N-0.0.2
$L['adm_polls_forumpolls'] = 'Polls from forums (recent at top):';	// New in N-0.0.1
$L['adm_polls_indexpolls'] = 'Index polls (recent at top):';	// New in N-0.0.1
$L['adm_polls_msg916_bump'] = 'Successfully bumped!';	// New in N-0.0.3
$L['adm_polls_msg916_deleted'] = 'Successfully deleted!';	// New in N-0.0.3
$L['adm_polls_msg916_reset'] = 'Successfully reset!';	// New in N-0.0.3
$L['adm_polls_on_page'] = 'on page';	// New in N-0.0.2
$L['adm_polls_polltopic'] = 'Poll topic';	// New in N-0.0.1

/**
 * Other Section
 * PM Subsection
 */

$L['adm_pm_totaldb'] = 'Private messages in the database';
$L['adm_pm_totalsent'] = 'Total of private messages ever sent';

/**
 * Other Section
 * Ratings Subsection
 */

$L['adm_ratings_already_del'] = 'Rating removed';	// New in N-0.0.3
$L['adm_ratings_totalitems'] = 'Total pages rated';
$L['adm_ratings_totalvotes'] = 'Total votes';
$L['adm_help_ratings'] = 'To reset a rating, simply delete it. It will be re-created with the first new vote.';

/**
 * Other Section
 * Cache Subsection
 */

$L['adm_delcacheitem'] = 'Cache item removed';	// New in N-0.0.2
$L['adm_internalcache'] = 'Internal cache';
$L['adm_purgeall_done'] = 'Cache cleared completely';	// New in N-0.0.2
$L['adm_diskcache'] = 'Disk cache';	// New in N-0.6.1

/**
 * Other Section
 * BBCode Subsection
 */

$L['adm_bbcode'] = 'BBCode';
$L['adm_bbcodes'] = 'BBCodes';
$L['adm_bbcodes_added'] = 'Successfully added new bbcode.';
$L['adm_bbcodes_clearcache'] = 'Clear HTML cache';
$L['adm_bbcodes_clearcache_confirm'] = 'This will clear cache for all pages and posts, continue?';
$L['adm_bbcodes_clearcache_done'] = 'HTML cache has been cleared.';
$L['adm_bbcodes_confirm'] = 'Really delete this bbcode?';
$L['adm_bbcodes_container'] = 'Container';
$L['adm_bbcodes_mode'] = 'Mode';
$L['adm_bbcodes_new'] = 'New BBCode';
$L['adm_bbcodes_pattern'] = 'Pattern';
$L['adm_bbcodes_postrender'] = 'Post-render';
$L['adm_bbcodes_priority'] = 'Priority';
$L['adm_bbcodes_removed'] = 'Successfully removed bbcode.';
$L['adm_bbcodes_replacement'] = 'Replacement';
$L['adm_bbcodes_updated'] = 'Successfully updated bbcode.';
$L['adm_help_bbcodes'] = <<<HTM
<ul>
<li><strong>Name</strong> - BBcode name (use alphanumerics and underscores only)</li>
<li><strong>Mode</strong> - Parsing mode, on of the following: 'str' (str_replace), 'ereg' (eregi_replace), 'pcre' (preg_replace) and 'callback' (preg_replace_callback)</li>
<li><strong>Pattern</strong> - BBcode string or entire regular expression</li>
<li><strong>Replacement</strong> - Replacement string or regular substitution or callback body</li>
<li><strong>Container</strong> - Whether bbcode is container (like [bbcode]Something here[/bbcode])</li>
<li><strong>Priority</strong> - BBcode priority from 0 to 255. Smaller priority bbcodes are parsed first, 128 is default medium priority.</li>
<li><strong>Plugin</strong> - Plugin/part name this bbcode belongs to. Leave it blank, this is for plugins only.</li>
<li><strong>Post-render</strong> - Whether this bbcode must be applied on a pre-rendered HTML cache. Use only if your callback does some per-request calculations.</li>
</ul>
HTM;

/**
 * Other Section
 * URLs Subsection
 */

$L['adm_urls'] = 'URLs';
$L['adm_urls_area'] = 'Area';
$L['adm_urls_error_dat'] = 'Error: datas/urltrans.dat is not writable!';
$L['adm_urls_format'] = 'Format';
$L['adm_urls_htaccess'] = 'Overwrite .htaccess?';
$L['adm_urls_new'] = 'New Rule';
$L['adm_urls_parameters'] = 'Parameters';
$L['adm_urls_rules'] = 'URL Transformation Rules';
$L['adm_urls_save'] = 'Save';
$L['adm_urls_your'] = 'Your';
$L['adm_urls_callbacks'] = 'Rule contains callbacks';
$L['adm_urls_errors'] = 'You will have to add rewrite options for them manually.';
$L['adm_help_urls'] = 'On this page you can customize your URLs using simple URL Transformation Rules. Please make sure the rules are correct and there are no duplicates. Do not use spaces, tabs and other special characters in the rules. Sections and parameters are explained below.
<ol>
<li><strong>Area</strong> is script name the rule belongs to. The metasymbol (*) stands for &quot;any script&quot;.</li>
<li><strong>Parameters</strong> is a condition matched against URL parameters. It is a string, containing name-value pairs separated with &amp; and = sign used between parameter name and value. No ? sign in the beginning is needed. If you specify some variable here, it must be present in the URL to match the rule. You can use * which means &quot;any value&quot;, a single value, or a list of possible values separated with | sign. All values should be urlencoded. <em>Example: name=Val|Josh&amp;id=124&amp;page=*</em>.</li>
<li><strong>Format</strong> sets format of the URLs matching this rule. It is a string containing special sequences substituded with their values. Normal sequence looks like {$name} where &quot;name&quot; is the name of URL parameter (GET variable), value of which will be inserted instead of this sequence. There are several special sequences which are not from URL parameters (&quot;query string&quot;):
	<ul>
		<li><em>{$_area}</em> - script name;</li>
		<li><em>{$_host}</em> - host name from your site Main URL;</li>
		<li><em>{$_rhost}</em> - host name from the current HTTP request;</li>
		<li><em>{$_path}</em> - server-related path of your site, / if your site is in server root.</li>
	</ul>
You can also use parametrized subdomains by specifying absolute URL format like: <em>http://{$c}.site.com/{$al}.html</em>. Currently subdomains are supported for Apache webservers only.</li>
<li><strong>New Rule</strong> appends a new rule line to the table.</li>
<li><strong>Order</strong> - keep in mind that order of the rules in the table is important. URL Transformation algorithm looks up a rule for a link this way: first it fetches all rules defined for the area, then it tries to find <em>the first</em> rule that matches the parameter condition; if no matching rules found, it will try to fall back to * area and look for the first matching rule there. It is recommended that your default rule (with * area and * parameters) is the last of the *-area rules, or even last in the table.<br />
You can change rule order by simply dragging the rows and dropping them at desired positions. It is recommended to save new rules before you can change their order with drag-and-drop.</li>
<li><strong>Query String</strong> is what you usually see in most links after the question mark. It is used to pass the rest of GET parameters that you have not used in the rest of the Format string and is appended automatically in that case.</li>
<li><strong>Save</strong> button will save rules and apply changes immediately. It will also apply changes on your .htaccess (if writable) and provide you with .htaccess/IsapiRewrite4.ini/nginx.conf (depending on your server type).</li>
</ol>';

/**
 * Other Section
 * Banlist Subsection
 */

$L['adm_ipmask'] = 'IP mask';
$L['adm_emailmask'] = 'E-mail mask';
$L['adm_neverexpire'] = 'Never expire';
$L['adm_help_banlist'] = 'Samples for IP masks: 194.31.13.41, 194.31.13.*, 194.31.*.*, 194.*.*.*<br />Samples for e-mail masks: @hotmail.com, @yahoo (Wildcards are not supported)<br />A single entry can contain one IP mask or one e-mail mask or both.<br />IPs are filtered for each and every page displayed, and e-mail masks at user registration only.';

$L['adm_searchthisuser'] = 'Search for this IP in the user database';
$L['adm_dnsrecord'] = 'DNS record for this address';

$L['alreadyaddnewentry'] = 'New entry added';	//N-0.0.2
$L['alreadyupdatednewentry'] = 'Entry updated';	//N-0.0.2
$L['alreadydeletednewentry'] = 'Entry deleted';	//N-0.0.2

/**
 * Other Section
 * Hits Subsection
 */

$L['adm_byyear'] = 'By year';
$L['adm_bymonth'] = 'By month';
$L['adm_byweek'] = 'By week';

$L['adm_ref_lowhits'] = 'Purge entries where hits are lower than 5';
$L['adm_maxhits'] = 'Maximum hitcount was reached %1$s, %2$s pages displayed this day.';

/**
 * Other Section
 * Referers Subsection
 */

$L['adm_ref_prune'] = 'Cleaned';
$L['adm_ref_prunelowhits'] = 'Referers with the number of visitors is less 5 successfully removed';

/**
 * Other Section
 * Log Subsection
 */

$L['adm_log'] = 'System log';
$L['adm_infos'] = 'Informations';
$L['adm_versiondclocks'] = 'Versions and clocks';
$L['adm_checkcoreskins'] = 'Check core files and skins';
$L['adm_checkcorenow'] = 'Check core files now!';
$L['adm_checkingcore'] = 'Checking core files...';
$L['adm_checkskins'] = 'Check if all files are present in skins';
$L['adm_checkskin'] = 'Check TPL files for the skin';
$L['adm_checkingskin'] = 'Checking the skin...';
$L['adm_hits'] = 'Hits';
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
$L['adm_queue_unvalidated'] = 'Unvalidated';	// New in N-0.0.3
$L['adm_queue_validated'] = 'Validated';	// New in N-0.0.3
$L['adm_required'] = '(Required)';
$L['adm_setby'] = 'Set by';
$L['adm_to'] = 'To';
$L['adm_totalsize'] = 'Total size';
$L['adm_warnings'] = 'Warnings';

$L['editdeleteentries'] = 'Edit or delete entries';
$L['viewdeleteentries'] = 'View or delete entries';

/**
 * Extra Fields (Common Entries for Pages & Structure & Users)
 */

$L['adm_extrafields'] = 'Extra fields';
$L['adm_extrafield_added'] = 'Successfully added new extra field.';
$L['adm_extrafield_not_added'] = 'Error! New extra field not added.';
$L['adm_extrafield_updated'] = 'Successfully updated extra field.';
$L['adm_extrafield_not_updated'] = 'Error! Extra field not updated.';
$L['adm_extrafield_removed'] = 'Successfully removed extra field.';
$L['adm_extrafield_not_removed'] = 'Error! Extra field not deleted.';
$L['adm_extrafield_confirmdel'] = 'Really delete this extra field? All data in this field will be lost!';
$L['adm_extrafield_confirmupd'] = 'Really update this extra field? Some data in this field may be lost!';

$L['extf_Name'] = 'Name';
$L['extf_Type'] = 'Type of field';
$L['extf_Base_HTML'] = 'Base HTML';
$L['extf_Page_tags'] = 'Tags';
$L['extf_Description'] = 'Description (_TITLE)';

$L['adm_extrafield_new'] = 'New extra field';
$L['adm_extrafield_noalter'] = 'Do not add actual field in DB, just register it as extra';
$L['adm_extrafield_selectable_values'] = 'Options for select (comma sep.)';
$L['adm_help_extrafield'] = 'Hint: Field &quot;Base HTML&quot; is set to default automatically if you leave it blank and press Update.';

/**
 * Help messages that still don't work
 */

$L['adm_help_cache'] = 'Not available';
$L['adm_help_check1'] = 'Not available';
$L['adm_help_check2'] = 'Not available';
$L['adm_help_config']= 'Not available';
$L['adm_help_forums'] = 'Not available';
$L['adm_help_forums_structure'] = 'Not available';
$L['adm_help_pfsfiles'] = 'Not available';
$L['adm_help_pfsthumbs'] = 'Not available';

?>