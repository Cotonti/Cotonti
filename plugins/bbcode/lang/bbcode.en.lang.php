<?php
/**
 * English Language File for BBcode management
 *
 * @package bbcode
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2013
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

$L['adm_bbcode'] = 'BBCode';
$L['adm_bbcodes'] = 'BBCodes';
$L['adm_bbcodes_added'] = 'Successfully added new bbcode.';
$L['adm_bbcodes_notadded'] = 'BBCode not added.';
$L['adm_bbcodes_notallfields'] = 'Some required fields not filled.';
$L['adm_bbcodes_clearcache'] = 'Clear HTML cache';
$L['adm_bbcodes_clearcache_confirm'] = 'This will clear cache for all pages and posts, continue?';
$L['adm_bbcodes_clearcache_done'] = 'HTML cache has been cleared.';
$L['adm_bbcodes_confirm'] = 'Really delete this bbcode?';
$L['adm_bbcodes_container'] = 'Container';
$L['adm_bbcodes_convert_comments'] = 'Convert comments to HTML';
$L['adm_bbcodes_convert_complete'] = 'Conversion complete';
$L['adm_bbcodes_convert_confirm'] = 'Are you sure? There is no rollback! If not sure, backup your database first.';
$L['adm_bbcodes_convert_forums'] = 'Convert forums to HTML';
$L['adm_bbcodes_convert_page'] = 'Convert pages to HTML';
$L['adm_bbcodes_convert_pm'] = 'Convert PMs to HTML';
$L['adm_bbcodes_convert_users'] = 'Convert user signatures to HTML';
$L['adm_bbcodes_mode'] = 'Mode';
$L['adm_bbcodes_new'] = 'New BBCode';
$L['adm_bbcodes_other'] = 'Other Actions';
$L['adm_bbcodes_pattern'] = 'Pattern';
$L['adm_bbcodes_postrender'] = 'Post-render';
$L['adm_bbcodes_priority'] = 'Priority';
$L['adm_bbcodes_removed'] = 'Successfully removed bbcode.';
$L['adm_bbcodes_notremoved'] = 'BBCode not deleted.';
$L['adm_bbcodes_replacement'] = 'Replacement';
$L['adm_bbcodes_updated'] = 'Successfully updated changed bbcode(s).';
$L['adm_bbcodes_notupdated'] = 'Some BBCode(s) not updated.';
$L['adm_bbcodes_fieldrequired'] = 'Required field not filled in some of BBCodes.';
$L['adm_help_bbcodes'] = "<ul>\n<li><strong>Name</strong> - BBcode name (use alphanumerics and underscores only)</li>\n<li><strong>Mode</strong> - Parsing mode, on of the following: 'str' (str_replace), 'ereg' (eregi_replace), 'pcre' (preg_replace) and 'callback' (preg_replace_callback)</li>\n<li><strong>Pattern</strong> - BBcode string or entire regular expression</li>\n<li><strong>Replacement</strong> - Replacement string or regular substitution or callback body</li>\n<li><strong>Container</strong> - Whether bbcode is container (like [bbcode]Something here[/bbcode])</li>\n<li><strong>Priority</strong> - BBcode priority from 0 to 255. Smaller priority bbcodes are parsed first, 128 is default medium priority.</li>\n<li><strong>Plugin</strong> - Plugin/part name this bbcode belongs to. Leave it blank, this is for plugins only.</li>\n<li><strong>Post-render</strong> - Whether this bbcode must be applied on a pre-rendered HTML cache. Use only if your callback does some per-request calculations.</li>\n</ul>";

$L['cfg_smilies'] = 'Enable smilies';
$L['cfg_smilies_hint'] = '';
$L['cfg_parse_autourls'] = 'Automatically convert URLs into links ?';

$L['info_desc'] = 'Customizable support for BBCodes and smilies parsing';
$L['hidefromguests'] = 'Only for registered users!';
