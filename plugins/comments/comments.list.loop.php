<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=comments
Part=list.loop
File=comments.list.loop
Hooks=list.loop
Tags=list.tpl:{LIST_ROW_COMMENTS}
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * Comments system for Cotonti
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

$pag['page_comcount'] = (!$pag['page_comcount']) ? "0" : $pag['page_comcount'];
$pag['page_comments'] = sed_rc_link(sed_url('page', $page_urlp, '#comments'), sed_rc('icon_comments_cnt', array('cnt' => $pag['page_comcount'])));
$t->assign(array(
	"LIST_ROW_COMMENTS" => $pag['page_comments']
));

?>