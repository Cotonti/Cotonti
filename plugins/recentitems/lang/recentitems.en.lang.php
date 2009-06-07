<?PHP
/**
 * English Language File for RecentItems Plugin
 *
 * @package Cotonti
 * @version 0.1.0
 * @author Cotonti Translators Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL.');

/**
 * Plugin Config
 */

$L['cfg_fd'] = array('Topic path display mode', '&quot;Standard&quot; &mdash; forum section by default<br />&quot;Parent only&quot; &mdash; parent section only<br />&quot;Subforums with Master Forums&quot; &mdash; parent section and subforum<br />&quot;Just Topics&quot; &mdash; topic only');
$L['cfg_maxpages'] = array('Recent pages displayed');
$L['cfg_maxtopics'] = array('Recent topics in forums displayed');
$L['cfg_redundancy'] = array('Redundancy ratio', 'With this setting you increase the number of recent items (pages and topics) by the specified ratio. This setting handles the errors that may occur with a big number of pages with restricted access and &quot;private&quot; forum topics .<br />Recommended setting: no less than 2.<br />It is not adviseable to make this setting too high, as you may encounter slow-down problems.');

?>