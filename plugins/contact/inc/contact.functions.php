<?php
/**
 * Contact Plugin API
 *
 * @package Contact
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
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
