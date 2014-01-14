<?php
/**
 * Contact Plugin API
 *
 * @package contact
 * @author Cotonti Team
 * @copyright (c) Cotonti Team 2008-2014
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_langfile('contact', 'plug');
require_once cot_incfile('extrafields');
require_once cot_incfile('forms');

cot::$db->registerTable('contact');
cot_extrafields_register_table('contact');

$R['contact_message'] = <<<TXT
{\$sitetitle} - {\$siteurl}

{$L['Sender']}: {\$author} ({\$email})
{$L['Topic']}: {\$subject}
{$L['Message']}:

{\$text}

{\$extra}
TXT;
