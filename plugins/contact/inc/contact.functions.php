<?php
/**
 * Contact Plugin API
 *
 * @package contact
 * @version 2.1.0
 * @author Seditio.by & Cotonti Team
 * @copyright (c) 2008-2011 Seditio.by and Cotonti Team
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_langfile('contact', 'plug');

global $db_contact, $db_x;
$db_contact = (isset($db_contact)) ? $db_contact : $db_x . 'contact';

?>
