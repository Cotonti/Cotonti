<?PHP
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=comedit
Part=form
File=comedit.form
Hooks=comments.newcomment.tags
Tags=
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * Comedit plug
 *
 * @package Cotonti
 * @version 0.0.3
 * @author Asmo (Edited by motor2hg), Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

require_once(sed_langfile('comedit'));

$allowed_time = sed_build_timegap($sys['now_offset']-$cfg['plugin']['comedit']['time']*60,$sys['now_offset']);
$com_hint = sprintf($L['plu_comhint'], $allowed_time);

$t->assign(array("COMMENTS_FORM_HINT" => $com_hint));

?>