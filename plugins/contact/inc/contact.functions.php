<?php
/**
 * Contact Plugin API
 *
 * @package contact
 * @version 2.1.0
 * @author Cotonti Team
 * @copyright (c) 2008-2012 Cotonti Team
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_langfile('contact', 'plug');
require_once cot_incfile('extrafields');
require_once cot_incfile('forms');

global $db_contact, $db_x;
$db_contact = (isset($db_contact)) ? $db_contact : $db_x . 'contact';
$cot_extrafields[$db_contact] = (!empty($cot_extrafields[$db_contact]))	? $cot_extrafields[$db_contact] : array();

$R['contact_message'] = <<<TXT
{\$sitetitle} - {\$siteurl}

{$L['Sender']}: {\$author} ({\$email})
{$L['Topic']}: {\$subject}
{$L['Message']}:

{\$text}

{\$extra}
TXT;

?>
