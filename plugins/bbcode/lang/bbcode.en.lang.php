<?php
/**
 * English Language File for BBcode management
 *
 * @package bbcode
 * @version 0.7.0
 * @author Cotonti Translators Team
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

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

$L['cfg_smilies'] = array('Enable smilies', '');

$L['info_desc'] = 'Enables BBCode parsing everywhere on site and disables default HTML parsing. Administrator can customize bbcodes with Admin tool. Also adds support for smilie codes and smilie sets.';

?>
