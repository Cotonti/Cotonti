<?php
/**
 * Removes HTML Purifier Serializer cache folder
 *
 * @package markitup
 * @version 0.7.0
 * @author Trustmaster
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

if (file_exists($cfg['cache_dir'] . '/htmlpurifier'))
{
	cot_rmdir($cfg['cache_dir'] . '/htmlpurifier');
}

?>
