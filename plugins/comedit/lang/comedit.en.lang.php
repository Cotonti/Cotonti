<?PHP
/**
 * Russian Language File for ComEdit Plugin
 *
 * @package Cotonti
 * @version 0.1.0
 * @author Cotonti Translators Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL.');

/**
 * Plugin Title & Subtitle
 */

$L['plu_title'] = 'Comment Editing';

/**
 * Plugin Body
 */

$L['plu_comgup'] = ' left';
$L['plu_comhint'] = '* Your comment will be available for editing for %1$s';
$L['plu_comlive'] = 'New comment on our site';	// New in N-0.1.0
$L['plu_comlive1'] = 'Edited comment on the site';	// New in N-0.1.0
$L['plu_comlive2'] = 'left a comment:';	// New in N-0.1.0
$L['plu_comlive3'] = 'has edited the comment:';	// New in N-0.1.0
$L['plu_comtooshort'] = 'Comment text must not be blank';

/**
 * Plugin Config
 */

$L['cfg_mail'] = array('Notify about new comments by email?');
$L['cfg_markitup'] = array('Use markitup?');	// New in N-0.1.0
$L['cfg_time'] = array('Comments editable timeout for users', 'in minutes');

?>